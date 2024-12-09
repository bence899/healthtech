<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Medical Documents') }}
            </h2>
            <a href="{{ route('documents.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Upload New Document
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($documents->isEmpty())
                        <p class="text-gray-500 text-center py-4">No documents uploaded yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($documents as $document)
                                    <tr>
                                        <td class="px-6 py-4">
                                            {{ $document->title }}
                                            @if($document->description)
                                                <p class="text-sm text-gray-500">{{ $document->description }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ Str::upper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}</td>
                                        <td class="px-6 py-4">{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                        <td class="px-6 py-4">{{ $document->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 space-x-2">
                                            <a href="{{ route('documents.download', $document) }}" 
                                               class="text-blue-600 hover:text-blue-900">Download</a>
                                            
                                            <form action="{{ route('documents.destroy', $document) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 