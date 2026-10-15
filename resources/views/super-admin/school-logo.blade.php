@extends('layouts.dashboard')

@section('content')
<div x-data="{
    showLogoConfirmation: false,
    showLogoError: false,
    logoUploadForm: null,
    logoName: '',
    logoErrorMessage: '',
    schoolLogoPreview: null,
    depedLogoPreview: null,
    async validateLogoFile(event, kind) {
        const input = event.target;
        const file = input.files?.[0];
        if (!file) return;

        const previewKey = kind + 'LogoPreview';
        const previousPreview = this[previewKey];
        const rejectFile = (message) => {
            input.value = '';
            this[previewKey] = null;
            if (previousPreview) URL.revokeObjectURL(previousPreview);
            this.logoErrorMessage = message;
            this.showLogoError = true;
        };

        if (file.size > 2 * 1024 * 1024) {
            rejectFile('The selected image must be 2 MB or smaller.');
            return;
        }

        let signature;
        try {
            signature = new Uint8Array(await file.slice(0, 12).arrayBuffer());
        } catch {
            rejectFile('The selected file could not be read. Choose a valid JPEG, PNG, or WebP image.');
            return;
        }

        const isJpeg = signature[0] === 0xff && signature[1] === 0xd8 && signature[2] === 0xff;
        const isPng = signature[0] === 0x89 && signature[1] === 0x50 && signature[2] === 0x4e && signature[3] === 0x47
            && signature[4] === 0x0d && signature[5] === 0x0a && signature[6] === 0x1a && signature[7] === 0x0a;
        const isWebp = signature[0] === 0x52 && signature[1] === 0x49 && signature[2] === 0x46 && signature[3] === 0x46
            && signature[8] === 0x57 && signature[9] === 0x45 && signature[10] === 0x42 && signature[11] === 0x50;

        if (!isJpeg && !isPng && !isWebp) {
            rejectFile('The selected file is not a valid JPEG, PNG, or WebP image. Renamed or unsupported files are not accepted.');
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        try {
            let width;
            let height;
            if ('createImageBitmap' in window) {
                const bitmap = await createImageBitmap(file);
                width = bitmap.width;
                height = bitmap.height;
                bitmap.close();
            } else {
                const image = new Image();
                image.src = previewUrl;
                await image.decode();
                width = image.naturalWidth;
                height = image.naturalHeight;
            }

            if (!width || !height || width * height > 20000000) {
                URL.revokeObjectURL(previewUrl);
                rejectFile('The selected image is invalid or exceeds the 20-megapixel limit.');
                return;
            }
        } catch {
            URL.revokeObjectURL(previewUrl);
            rejectFile('The selected image could not be decoded. Choose a valid JPEG, PNG, or WebP image.');
            return;
        }

        this[previewKey] = previewUrl;
        if (previousPreview) URL.revokeObjectURL(previousPreview);
    },
    requestLogoUpload(event, name) {
        if (!event.target.reportValidity()) {
            return;
        }
        this.logoUploadForm = event.target;
        this.logoName = name;
        this.showLogoConfirmation = true;
    },
    confirmLogoUpload() {
        this.showLogoConfirmation = false;
        showLoadingModal('Uploading ' + this.logoName + ', please wait...');
        this.logoUploadForm?.submit();
    }
}" class="mx-auto flex max-w-4xl flex-col gap-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Report Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage the report logos and signatory details for the active school year.</p>
    </div>

    @if(session('success'))
    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
    @endif

    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900">Project Development Officer</h2>
        <p class="mt-1 text-sm text-gray-500">This name appears as the prepared-by signatory on reports for the active school year.</p>
        <form method="POST" action="{{ route('super-admin.school-logo.project-development-officer.update') }}" class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end">
            @csrf @method('PUT')
            <div class="flex-1">
                <label for="project_development_officer_name" class="block text-sm font-semibold text-gray-700">Full name</label>
                <input id="project_development_officer_name" name="project_development_officer_name" type="text" value="{{ old('project_development_officer_name', $projectDevelopmentOfficerName) }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="mt-2 block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="e.g., Juan Dela Cruz">
                @error('project_development_officer_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"><i class="fas fa-save"></i> Save name</button>
        </form>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">School Logo</h2>
            <div class="my-5 flex min-h-40 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6">
                <img :src="schoolLogoPreview || @js($schoolLogoUrl)" :alt="schoolLogoPreview ? 'Selected school logo preview' : 'Current school logo'" class="max-h-32 max-w-full object-contain">
            </div>
            <p x-show="schoolLogoPreview" x-cloak class="-mt-3 text-center text-xs font-medium text-blue-700">Selected image preview</p>
            <form method="POST" action="{{ route('super-admin.school-logo.update') }}" enctype="multipart/form-data" class="space-y-4" @submit.prevent="requestLogoUpload($event, 'school logo')">
                @csrf @method('PUT')
                <label for="school_logo" class="block text-sm font-semibold text-gray-700">Upload school logo</label>
                <input id="school_logo" name="school_logo" type="file" accept="image/jpeg,image/png,image/webp" required @change="validateLogoFile($event, 'school')" class="block w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-semibold file:text-white">
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"><i class="fas fa-upload"></i> Upload school logo</button>
                    <button type="submit" form="reset-school-logo" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"><i class="fas fa-rotate-left"></i> Reset default</button>
                </div>
            </form>
            <form id="reset-school-logo" method="POST" action="{{ route('super-admin.school-logo.reset') }}" data-confirm-message="Reset the school logo to the default? The currently uploaded logo will be replaced.">@csrf @method('DELETE')</form>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900">DepEd Logo</h2>
            <div class="my-5 flex min-h-40 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6">
                <img :src="depedLogoPreview || @js($depedLogoUrl)" :alt="depedLogoPreview ? 'Selected DepEd logo preview' : 'Current DepEd logo'" class="max-h-32 max-w-full object-contain">
            </div>
            <p x-show="depedLogoPreview" x-cloak class="-mt-3 text-center text-xs font-medium text-blue-700">Selected image preview</p>
            <form method="POST" action="{{ route('super-admin.school-logo.deped.update') }}" enctype="multipart/form-data" class="space-y-4" @submit.prevent="requestLogoUpload($event, 'DepEd logo')">
                @csrf @method('PUT')
                <label for="deped_logo" class="block text-sm font-semibold text-gray-700">Upload DepEd logo</label>
                <input id="deped_logo" name="deped_logo" type="file" accept="image/jpeg,image/png,image/webp" required @change="validateLogoFile($event, 'deped')" class="block w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-semibold file:text-white">
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"><i class="fas fa-upload"></i> Upload DepEd logo</button>
                    <button type="submit" form="reset-deped-logo" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"><i class="fas fa-rotate-left"></i> Reset default</button>
                </div>
            </form>
            <form id="reset-deped-logo" method="POST" action="{{ route('super-admin.school-logo.deped.reset') }}" data-confirm-message="Reset the DepEd logo to the default? The currently uploaded logo will be replaced.">@csrf @method('DELETE')</form>
        </section>
    </div>

    <div x-show="showLogoError" x-transition x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50" role="alertdialog" aria-modal="true" aria-labelledby="logo-upload-error-title">
        <div class="mx-4 w-full max-w-sm rounded-lg bg-white p-6 text-center shadow-xl">
            <div class="mb-4 text-red-600 text-4xl"><i class="fas fa-circle-exclamation" aria-hidden="true"></i></div>
            <h2 id="logo-upload-error-title" class="mb-2 text-lg font-bold text-gray-900">Image Not Accepted</h2>
            <p class="mb-6 text-sm text-gray-600" x-text="logoErrorMessage"></p>
            <button type="button" @click="showLogoError = false" class="w-full rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Close</button>
        </div>
    </div>

    <div x-show="showLogoConfirmation" x-transition x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50" role="dialog" aria-modal="true" aria-labelledby="logo-upload-confirmation-title">
        <div class="mx-4 w-full max-w-sm rounded-lg bg-white p-6 text-center shadow-xl">
            <div class="mb-4 text-blue-600 text-4xl"><i class="fas fa-cloud-arrow-up" aria-hidden="true"></i></div>
            <h2 id="logo-upload-confirmation-title" class="mb-2 text-lg font-bold text-gray-900">Confirm Logo Upload</h2>
            <p class="mb-6 text-sm text-gray-600">Upload the selected <span x-text="logoName"></span> now?</p>
            <div class="flex gap-2">
                <button type="button" @click="showLogoConfirmation = false" class="flex-1 rounded bg-gray-400 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-500">Cancel</button>
                <button type="button" @click="confirmLogoUpload()" class="flex-1 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection