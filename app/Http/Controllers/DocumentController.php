<?php

namespace App\Http\Controllers;

use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = MedicalDocument::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,doc,docx|max:1024',
            'description' => 'nullable|string|max:1000'
        ]);

        // Check monthly upload limit (5 uploads per month)
        $monthlyCount = MedicalDocument::where('user_id', auth()->id())
            ->where('upload_month', now()->format('Y-m'))
            ->count();

        if ($monthlyCount >= 5) {
            return back()->withErrors(['document' => 'Monthly upload limit reached']);
        }

        // Check total storage limit (5MB)
        $totalStorage = MedicalDocument::where('user_id', auth()->id())->sum('file_size');
        if ($totalStorage + $request->file('document')->getSize() > 5 * 1024 * 1024) {
            return back()->withErrors(['document' => 'Storage limit reached']);
        }

        $path = $request->file('document')->store('medical-documents/' . auth()->id(), 's3');

        MedicalDocument::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $request->file('document')->extension(),
            'file_size' => $request->file('document')->getSize(),
            'upload_month' => now()->format('Y-m')
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully');
    }

    public function destroy(MedicalDocument $document)
    {
        if ($document->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        Storage::disk('s3')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully');
    }

    public function download(MedicalDocument $document)
    {
        if ($document->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return Storage::disk('s3')->download($document->file_path);
    }
} 