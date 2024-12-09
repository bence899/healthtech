<?php

namespace App\Http\Controllers;

use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDocumentRequest;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class DocumentController extends BaseController
{
    const MAX_FILE_SIZE = 1024; // 1MB in kilobytes
    const MONTHLY_STORAGE_LIMIT = 5120; // 5MB in bytes
    const MONTHLY_REQUEST_LIMIT = 5;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreDocumentRequest $request)
    {
        $file = $request->file('document');
        $monthlyStorage = $this->getMonthlyStorageUsage();
        $monthlyRequests = $this->getMonthlyRequestCount();

        if ($monthlyRequests >= self::MONTHLY_REQUEST_LIMIT) {
            return back()->withErrors([
                'document' => 'Monthly upload limit reached'
            ]);
        }

        if ($monthlyStorage + $file->getSize() > self::MONTHLY_STORAGE_LIMIT) {
            return back()->withErrors([
                'document' => 'Storage limit reached'
            ]);
        }

        $path = $file->store('medical-documents/' . auth()->id(), 's3');

        $document = MedicalDocument::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientOriginalExtension(),
            'upload_month' => now()->format('Y-m')
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully');
    }

    private function getMonthlyStorageUsage()
    {
        return MedicalDocument::where('user_id', auth()->id())
            ->where('upload_month', now()->format('Y-m'))
            ->sum('file_size');
    }

    private function getMonthlyRequestCount()
    {
        return MedicalDocument::where('user_id', auth()->id())
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
    }

    public function index()
    {
        $documents = auth()->user()->role === 'admin'
            ? MedicalDocument::with('user')->orderBy('created_at', 'desc')->get()
            : MedicalDocument::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

        return view('documents.index', compact('documents'));
    }

    public function destroy(MedicalDocument $document)
    {
        // Check if user owns the document
        if ($document->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        // Delete file from S3
        Storage::disk('s3')->delete($document->file_path);

        // Delete database record
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully');
    }

    public function download(MedicalDocument $document)
    {
        // Check if user owns the document or is admin
        if ($document->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        // Check if file exists in storage
        if (!Storage::disk('s3')->exists($document->file_path)) {
            return back()->withErrors(['error' => 'File not found']);
        }

        return Storage::disk('s3')->download(
            $document->file_path,
            $document->title . '.' . $document->file_type,
            ['Content-Type' => 'application/octet-stream']
        );
    }
} 