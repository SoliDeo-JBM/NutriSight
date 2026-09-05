<x-modal name="attendance-scanner" :show="false" focusable>
    <div class="p-6" x-data="{
        scanValue: '',
        studentName: '',
        studentDetails: '',
        statusMessage: '',
        statusClass: '',
        statusBg: '',
        processScan() {
            if (!this.scanValue.trim()) return;
            
            fetch('{{ route('encoder.attendance.scan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_number: this.scanValue })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok) {
                    this.studentName = data.student_name;
                    this.studentDetails = data.grade_level + ' - ' + data.section;
                    this.statusMessage = data.success;
                    this.statusClass = 'text-green-700 font-bold';
                    this.statusBg = 'bg-green-50 border-green-200';
                } else {
                    this.studentName = data.student_name || 'Unknown QR Code';
                    this.studentDetails = data.grade_level && data.section ? (data.grade_level + ' - ' + data.section) : '';
                    this.statusMessage = data.error;
                    if (response.status === 409) {
                        // Already recorded
                        this.statusClass = 'text-amber-700 font-bold';
                        this.statusBg = 'bg-amber-50 border-amber-200';
                    } else {
                        // Not found or disapproved
                        this.statusClass = 'text-red-700 font-bold';
                        this.statusBg = 'bg-red-50 border-red-200';
                    }
                }
                this.scanValue = '';
            });
        }
    }">
        <h2 class="text-lg font-medium text-gray-900 mb-4"><i class="fas fa-qrcode text-indigo-600 mr-2"></i> Scan Student QR Code</h2>
        
        <input type="text" 
               x-ref="scannerInput"
               x-model="scanValue" 
               x-on:keyup.enter="processScan()"
               x-on:open-modal.window="$event.detail == 'attendance-scanner' ? setTimeout(() => $refs.scannerInput.focus(), 100) : null"
               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 text-sm"
               placeholder="Ready for scan..." />
        
        <template x-if="studentName">
            <div class="mt-5 p-4 rounded-lg border transition-all duration-300" :class="statusBg">
                <div class="text-base font-bold text-gray-900" x-text="studentName"></div>
                <div class="text-xs text-gray-600 mt-0.5" x-text="studentDetails"></div>
                <div class="mt-2 text-sm pt-2 border-t border-gray-200/60" :class="statusClass" x-text="statusMessage"></div>
            </div>
        </template>
    </div>
</x-modal>
