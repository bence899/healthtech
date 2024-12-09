<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Medical Document') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Document Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="document" :value="__('Document File')" />
                            <input type="file" id="document" name="document" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" required>
                            <p class="mt-1 text-sm text-gray-500">Accepted formats: PDF, JPG, PNG (Max 10MB)</p>
                            <x-input-error :messages="$errors->get('document')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Upload Document') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 