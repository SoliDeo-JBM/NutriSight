@props(['participants', 'action'])
@php
    $profileUploadError = collect($errors->getMessages())
        ->filter(fn ($messages, $key) => $key === 'profiles' || str_starts_with($key, 'profiles.'))
        ->flatten()
        ->first();
@endphp
<div x-data="{
    showProfileImages: false,
    showUploadConfirmation: false,
    uploadForm: null,
    selectedImages: 0,
    uploadError: '',
    validateFile(event) {
        const file = event.target.files[0];
        if (!file) {
            return;
        }
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        this.uploadError = !allowedTypes.includes(file.type)
            ? 'Profile image upload failed. Please use a JPG, PNG, or WEBP image up to 5 MB.'
            : file.size > 5 * 1024 * 1024
                ? 'Profile image upload failed. Please use a JPG, PNG, or WEBP image up to 5 MB.'
                : '';
    }
}">
    <button type="button" @click="showProfileImages = true" class="shrink-0 bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 whitespace-nowrap inline-flex items-center gap-2">
        <i class="fas fa-image"></i> Add Profile Images
    </button>

    <div x-show="showProfileImages" x-transition x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto" @click.outside="showProfileImages = false">
            <h3 class="text-lg font-bold mb-1">Add Profile Images</h3>
            <p class="text-sm text-gray-500 mb-4">Upload or replace profile images for SBFP participants. Accepted: JPG, PNG, WEBP (max 5 MB each). Leave blank to keep the current image.</p>

            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" @submit.prevent="selectedImages = [...$el.querySelectorAll('input[type=file]')].filter((input) => input.files.length > 0).length; if (uploadError) { return; } if (selectedImages > 0) { uploadForm = $el; showUploadConfirmation = true; }">
                @csrf
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 border text-left">Current</th>
                                <th class="px-3 py-2 border text-left">Learner</th>
                                <th class="px-3 py-2 border text-left">New Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $row)
                            <tr>
                                <td class="px-3 py-2 border">
                                    <img src="{{ $row['image'] ?: asset('images/anonymous-profile.svg') }}" alt="{{ $row['name'] }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                </td>
                                <td class="px-3 py-2 border whitespace-nowrap">{{ $row['name'] }}</td>
                                <td class="px-3 py-2 border">
                                    <input type="file" name="profiles[{{ $row['id'] }}]" accept="image/png,image/jpeg,image/webp" @change="validateFile($event)" class="text-xs">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 border text-center text-gray-500">No SBFP participants available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p x-show="uploadError" x-text="uploadError" class="mb-4 text-sm font-semibold text-red-600" role="alert"></p>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 font-semibold">Upload</button>
                    <button type="button" @click="showProfileImages = false" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showUploadConfirmation" x-transition x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]" role="dialog" aria-modal="true" aria-labelledby="profile-upload-confirmation-title">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm mx-4 text-center">
            <div class="text-blue-600 text-4xl mb-4"><i class="fas fa-cloud-arrow-up" aria-hidden="true"></i></div>
            <h3 id="profile-upload-confirmation-title" class="text-lg font-bold text-gray-900 mb-2">Confirm Profile Image Upload</h3>
            <p class="text-sm text-gray-600 mb-6">Upload <span x-text="selectedImages"></span> profile image<span x-show="selectedImages !== 1">s</span> now?</p>
            <div class="flex gap-2">
                <button type="button" @click="showUploadConfirmation = false" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                <button type="button" @click="showUploadConfirmation = false; showProfileImages = false; uploadForm?.submit()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-semibold">Confirm</button>
            </div>
        </div>
    </div>

    @if($profileUploadError)
    <div x-data="{ open: true }" x-show="open" x-transition x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[70]" role="alertdialog" aria-modal="true" aria-labelledby="profile-upload-error-title">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4 text-center">
            <div class="text-red-600 text-4xl mb-4"><i class="fas fa-circle-exclamation" aria-hidden="true"></i></div>
            <h3 id="profile-upload-error-title" class="text-lg font-bold text-gray-900 mb-2">Profile Image Upload Failed</h3>
            <p class="text-sm text-gray-600 mb-6">{{ $profileUploadError }}</p>
            <button type="button" @click="open = false" class="px-5 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 font-semibold">OK</button>
        </div>
    </div>
    @endif
</div>
