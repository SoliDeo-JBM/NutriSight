<style>
    .student-profile-scroll {
        scrollbar-color: #cbd5e1 transparent;
        scrollbar-width: thin;
    }

    .student-profile-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .student-profile-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .student-profile-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border: 1px solid transparent;
        border-radius: 9999px;
        background-clip: padding-box;
    }

    .student-profile-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
        background-clip: padding-box;
    }
</style>
<div x-data="studentProfileModal()" x-init="window.addEventListener('student-profile-open', event => openProfile(event.detail))">
    <div x-show="showProfile" x-cloak class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/50 p-2 sm:items-center sm:p-4" @click="closeProfile()" role="dialog" aria-modal="true" aria-labelledby="student-profile-title">
        <div class="student-profile-scroll my-2 max-h-[calc(100vh-1rem)] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-4 shadow-2xl sm:my-4 sm:max-h-[calc(100vh-2rem)] sm:p-6" @click.stop>
            <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4">
                <div class="flex min-w-0 items-center gap-3">
                    <img :src="profile.profile_image_url || '{{ asset('images/anonymous-profile.svg') }}'" :alt="profile.name" class="h-12 w-12 shrink-0 rounded-full border border-gray-200 object-cover sm:h-14 sm:w-14">
                    <div class="min-w-0">
                        <h2 id="student-profile-title" class="break-words text-base font-bold text-gray-900 sm:text-lg" x-text="profile.name"></h2>
                        <p class="mt-1 text-xs text-gray-500">Learner profile</p>
                    </div>
                </div>
                <button type="button" @click="closeProfile()" class="text-gray-400 hover:text-gray-700" aria-label="Close profile">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 gap-3 py-4 text-sm sm:grid-cols-2">
                <div><span class="text-gray-500">LRN / ID</span>
                    <div class="font-semibold" x-text="profile.lrn"></div>
                </div>
                <div><span class="text-gray-500">Sex</span>
                    <div class="font-semibold" x-text="profile.sex"></div>
                </div>
                <div><span class="text-gray-500">Birthdate</span>
                    <div class="font-semibold" x-text="profile.birthdate"></div>
                </div>
                <div><span class="text-gray-500">Age</span>
                    <div class="font-semibold" x-text="profile.age"></div>
                </div>
                <div><span class="text-gray-500">Grade</span>
                    <div class="font-semibold" x-text="profile.grade"></div>
                </div>
                <div><span class="text-gray-500">Section</span>
                    <div class="font-semibold" x-text="profile.section"></div>
                </div>
                <div><span class="text-gray-500">Parent Approval</span>
                    <div class="font-semibold" x-text="profile.approval"></div>
                </div>
                <div><span class="text-gray-500">Disapproval Reason</span>
                    <div class="font-semibold" x-text="profile.reason"></div>
                </div>
                <div class="sm:col-span-2"><span class="text-gray-500">Guardian</span>
                    <div class="break-words font-semibold" x-text="profile.guardian"></div>
                </div>
                <div><span class="text-gray-500">Guardian Contact</span>
                    <div class="font-semibold" x-text="profile.guardian_contact"></div>
                </div>
                <div><span class="text-gray-500">Guardian Email</span>
                    <div class="font-semibold break-all" x-text="profile.guardian_email"></div>
                </div>
            </div>
            <div class="border-t border-gray-200 pt-4">
                <h3 class="mb-3 text-sm font-bold text-gray-900">Period Measurements</h3>
                <div class="grid grid-cols-1 gap-2 text-xs sm:grid-cols-3">
                    <template x-for="period in ['Baseline', 'Midline', 'Endline']" :key="period">
                        <div class="rounded border border-gray-200 bg-gray-50 p-2">
                            <div class="font-bold" x-text="period"></div>
                            <template x-if="profile.periods && profile.periods[period]">
                                <div class="mt-1 space-y-0.5">
                                    <div x-text="'Weight: ' + profile.periods[period].weight + ' kg'"></div>
                                    <div x-text="'Height: ' + profile.periods[period].height + ' cm'"></div>
                                    <div x-text="'BMI: ' + profile.periods[period].bmi"></div>
                                    <template x-if="profile.periods[period].target">
                                        <div class="mt-2 border-t border-gray-200 pt-2 text-[11px] text-blue-700">
                                            <div class="font-semibold">Estimated normal range</div>
                                            <div x-text="'BMI: ' + profile.periods[period].target.bmi"></div>
                                            <div x-text="'Weight: ' + profile.periods[period].target.weight"></div>
                                            <div x-text="'Height: ' + profile.periods[period].target.height"></div>
                                        </div>
                                    </template>
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
            profile: {
                periods: {}
            },
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