@props(['participants', 'action'])
<div x-data="{ showProfileImages: false }">
    <button type="button" @click="showProfileImages = true" class="shrink-0 bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 whitespace-nowrap inline-flex items-center gap-2">
        <i class="fas fa-image"></i> Add Profile Images
    </button>

    <div x-show="showProfileImages" x-transition x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto" @click.outside="showProfileImages = false">
            <h3 class="text-lg font-bold mb-1">Add Profile Images</h3>
            <p class="text-sm text-gray-500 mb-4">Upload or replace profile images for SBFP participants. Accepted: JPG, PNG, WEBP (max 4MB each). Leave blank to keep the current image.</p>

            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
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
                                    <input type="file" name="profiles[{{ $row['id'] }}]" accept="image/png,image/jpeg,image/webp" class="text-xs">
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
                    <button type="button" @click="showProfileImages = false" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
