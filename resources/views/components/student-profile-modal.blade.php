<div x-data="studentProfileModal()" x-init="window.addEventListener('student-profile-open', event => openProfile(event.detail))">
    <div x-show="showProfile" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" @click="closeProfile()" role="dialog" aria-modal="true" aria-labelledby="student-profile-title">
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl" @click.stop>
            <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                <div class="flex items-center gap-3">
                    <img :src="profile.profile_image_url || '{{ asset('images/anonymous-profile.svg') }}'" :alt="profile.name" class="h-14 w-14 rounded-full border border-gray-200 object-cover">
                    <div>
                        <h2 id="student-profile-title" class="text-lg font-bold text-gray-900" x-text="profile.name"></h2>
                        <p class="mt-1 text-xs text-gray-500">Student profile</p>
                    </div>
                </div>
                <button type="button" @click="closeProfile()" class="text-gray-400 hover:text-gray-700" aria-label="Close profile">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-3 py-4 text-sm">
                <div><span class="text-gray-500">LRN / ID</span><div class="font-semibold" x-text="profile.lrn"></div></div>
                <div><span class="text-gray-500">Sex</span><div class="font-semibold" x-text="profile.sex"></div></div>
                <div><span class="text-gray-500">Birthdate</span><div class="font-semibold" x-text="profile.birthdate"></div></div>
                <div><span class="text-gray-500">Age</span><div class="font-semibold" x-text="profile.age"></div></div>
                <div><span class="text-gray-500">Grade</span><div class="font-semibold" x-text="profile.grade"></div></div>
                <div><span class="text-gray-500">Section</span><div class="font-semibold" x-text="profile.section"></div></div>
                <div><span class="text-gray-500">Parent Approval</span><div class="font-semibold" x-text="profile.approval"></div></div>
                <div><span class="text-gray-500">Disapproval Reason</span><div class="font-semibold" x-text="profile.reason"></div></div>
                <div class="col-span-2"><span class="text-gray-500">Guardian</span><div class="font-semibold" x-text="profile.guardian"></div></div>
                <div><span class="text-gray-500">Guardian Contact</span><div class="font-semibold" x-text="profile.guardian_contact"></div></div>
                <div><span class="text-gray-500">Guardian Email</span><div class="font-semibold break-all" x-text="profile.guardian_email"></div></div>
            </div>
            <div class="border-t border-gray-200 pt-4">
                <h3 class="mb-3 text-sm font-bold text-gray-900">Period Measurements</h3>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <template x-for="period in ['Baseline', 'Midline', 'Endline']" :key="period">
                        <div class="rounded border border-gray-200 bg-gray-50 p-2">
                            <div class="font-bold" x-text="period"></div>
                            <template x-if="profile.periods && profile.periods[period]">
                                <div class="mt-1 space-y-0.5">
                                    <div x-text="'Weight: ' + profile.periods[period].weight + ' kg'"></div>
                                    <div x-text="'Height: ' + profile.periods[period].height + ' cm'"></div>
                                    <div x-text="'BMI: ' + profile.periods[period].bmi"></div>
                                </div>
                            </template>
                            <span x-show="!profile.periods || !profile.periods[period]" class="text-gray-400">No data</span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="mt-5 flex justify-end">
                <button type="button" @click="closeProfile()" class="rounded bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    function studentProfileModal() {
        return {
            showProfile: false,
            profile: { periods: {} },
            openProfile(profile) {
                this.profile = profile;
                this.showProfile = true;
            },
            closeProfile() {
                this.showProfile = false;
            },
        };
    }
</script>
