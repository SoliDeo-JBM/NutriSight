@props(['participants', 'action'])
@php
$profileUploadError = collect($errors->getMessages())
->filter(fn ($messages, $key) => $key === 'profiles' || str_starts_with($key, 'profiles.'))
->flatten()
->first();
$profileUploadSuccess = session('profile_upload_success');
@endphp
<div x-data="{
    showProfileImages: false,
    showUploadConfirmation: false,
    showUploadResult: @js((bool) ($profileUploadError || $profileUploadSuccess)),
    resultType: @js($profileUploadError ? 'error' : 'success'),
    resultMessage: @js($profileUploadError ?: $profileUploadSuccess ?: ''),
    uploadForm: null,
    selectedImages: 0,
    uploadError: '',
    previews: {},
    async validateFile(event, participantId) {
        const input = event.target;
        const file = input.files[0];
        if (!file) {
            this.clearPreview(participantId);
            this.uploadError = '';
            return;
        }
        this.clearPreview(participantId);
        this.uploadError = '';

        const errorMessage = 'The selected file is not a valid JPEG, PNG, or WEBP image. Renamed or unsupported files are not accepted.';
        if (file.size > 5 * 1024 * 1024) {
            this.rejectSelectedFile(input, 'Profile images must be 5 MB or smaller.');
            return;
        }

        let bytes;
        try {
            bytes = new Uint8Array(await file.slice(0, 12).arrayBuffer());
        } catch {
            this.rejectSelectedFile(input, errorMessage);
            return;
        }

        const matches = (signature, offset = 0) => signature.every((byte, index) => bytes[offset + index] === byte);
        const isJpeg = matches([0xff, 0xd8, 0xff]);
        const isPng = matches([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a]);
        const isWebp = matches([0x52, 0x49, 0x46, 0x46]) && matches([0x57, 0x45, 0x42, 0x50], 8);
        if (!isJpeg && !isPng && !isWebp) {
            this.rejectSelectedFile(input, errorMessage);
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        try {
            await new Promise((resolve, reject) => {
                const image = new Image();
                image.onload = resolve;
                image.onerror = reject;
                image.src = previewUrl;
            });
        } catch {
            URL.revokeObjectURL(previewUrl);
            if (input.files[0] === file) {
                this.rejectSelectedFile(input, errorMessage);
            }
            return;
        }

        if (input.files[0] !== file) {
            URL.revokeObjectURL(previewUrl);
            return;
        }

        this.previews[participantId] = previewUrl;
    },
    rejectSelectedFile(input, message) {
        input.value = '';
        this.uploadError = message;
        this.openUploadResult('error', message);
    },
    openUploadResult(type, message) {
        this.showProfileImages = false;
        this.showUploadConfirmation = false;
        this.resultType = type;
        this.resultMessage = message;
        this.showUploadResult = true;
    },
    closeUploadResult() {
        this.showUploadResult = false;
        this.showProfileImages = false;
        this.showUploadConfirmation = false;
    },
    clearPreview(participantId) {
        if (this.previews[participantId]) {
            URL.revokeObjectURL(this.previews[participantId]);
            delete this.previews[participantId];
        }
    },
    closeProfileImages() {
        Object.keys(this.previews).forEach((participantId) => this.clearPreview(participantId));
        this.showProfileImages = false;
    }
}">
    <button type="button" @click="showProfileImages = true" class="shrink-0 bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 whitespace-nowrap inline-flex items-center gap-2">
        <i class="fas fa-image"></i> Add Profile Images
    </button>

    <div x-show="showProfileImages" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto" @click.outside="closeProfileImages()">
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
                                    <div class="flex items-center gap-3">
                                        <div class="shrink-0 text-center">
                                            <img
                                                x-show="previews['{{ $row['id'] }}']"
                                                :src="previews['{{ $row['id'] }}']"
                                                alt="Selected profile image preview"
                                                class="h-12 w-12 rounded-full object-cover border-2 border-indigo-300">
                                            <span x-show="!previews['{{ $row['id'] }}']" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-dashed border-gray-300 text-xs text-gray-400">New</span>
                                        </div>
                                        <input type="file" name="profiles[{{ $row['id'] }}]" accept="image/png,image/jpeg,image/webp" @change="validateFile($event, '{{ $row['id'] }}')" class="min-w-0 text-xs">
                                    </div>
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
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 font-semibold">Upload</button>
                    <button type="button" @click="closeProfileImages()" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
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
                <button type="button" @click="showUploadConfirmation = false; closeProfileImages(); showLoadingModal('Uploading profile images, please wait...'); uploadForm?.submit()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-semibold">Confirm</button>
            </div>
        </div>
    </div>

    <div x-show="showUploadResult" x-transition x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[70]" role="alertdialog" aria-modal="true" aria-labelledby="profile-upload-result-title">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4 text-center">
            <div :class="resultType === 'success' ? 'text-green-600' : 'text-red-600'" class="text-4xl mb-4">
                <i :class="resultType === 'success' ? 'fas fa-circle-check' : 'fas fa-circle-exclamation'" aria-hidden="true"></i>
            </div>
            <h3 id="profile-upload-result-title" x-text="resultType === 'success' ? 'Profile Images Uploaded' : 'Profile Image Upload Failed'" class="text-lg font-bold text-gray-900 mb-2"></h3>
            <p x-text="resultMessage" class="text-sm text-gray-600 mb-6"></p>
            <button type="button" @click="closeUploadResult()" :class="resultType === 'success' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'" class="px-5 py-2 text-white rounded text-sm font-semibold">OK</button>
        </div>
    </div>
</div>