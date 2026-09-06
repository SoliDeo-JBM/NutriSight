<x-modal name="attendance-scanner" :show="false" focusable>
    <div class="p-6" x-data="{
        scanValue: '',
        studentName: '',
        studentDetails: '',
        statusMessage: '',
        statusClass: '',
        statusBg: '',
        processScan() {
            if (!this.scanValue || !this.scanValue.trim()) return;
            
            const lrnToScan = this.scanValue.trim();
            this.scanValue = '';

            fetch('{{ route('encoder.attendance.scan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ lrn: lrnToScan })
            })
            .then(async response => {
                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    data = { error: 'Invalid server response.', student_name: 'Unknown QR Code' };
                }

                if (response.ok) {
                    this.studentName = data.student_name || 'Student';
                    this.studentDetails = (data.grade_level && data.section) ? (data.grade_level + ' - ' + data.section) : '';
                    this.statusMessage = data.success || 'Attendance logged successfully.';
                    this.statusClass = 'text-green-700 font-bold';
                    this.statusBg = 'bg-green-50 border-green-200';
                } else {
                    this.studentName = data.student_name || 'Invalid QR / Barcode';
                    this.studentDetails = (data.grade_level && data.section) ? (data.grade_level + ' - ' + data.section) : 'No student record matched LRN: ' + lrnToScan;
                    this.statusMessage = data.error || 'Student not found in active school year.';
                    if (response.status === 409) {
                        this.statusClass = 'text-amber-700 font-bold';
                        this.statusBg = 'bg-amber-50 border-amber-200';
                    } else {
                        this.statusClass = 'text-red-700 font-bold';
                        this.statusBg = 'bg-red-50 border-red-200';
                    }
                }
            })
            .catch(error => {
                this.studentName = 'Scan Error';
                this.studentDetails = 'Failed to process LRN: ' + lrnToScan;
                this.statusMessage = 'Network or server error occurred.';
                this.statusClass = 'text-red-700 font-bold';
                this.statusBg = 'bg-red-50 border-red-200';
            });
        }
    }">
        <h2 class="text-lg font-medium text-gray-900 mb-4"><i class="fas fa-qrcode text-orange-600 mr-2"></i> Scan Student QR Code</h2>
        
        <input type="text" 
               x-ref="scannerInput"
               x-model="scanValue" 
               x-on:keyup.enter="processScan()"
               x-on:change="processScan()"
               x-on:open-modal.window="$event.detail == 'attendance-scanner' ? setTimeout(() => $refs.scannerInput.focus(), 100) : null"
               class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 p-2.5 text-sm"
               placeholder="Ready for scan (press Enter or blur)..." />
        
        <template x-if="studentName">
            <div class="mt-5 p-4 rounded-lg border transition-all duration-300" :class="statusBg">
                <div class="text-base font-bold text-gray-900" x-text="studentName"></div>
                <div class="text-xs text-gray-600 mt-0.5" x-text="studentDetails"></div>
                <div class="mt-2 text-sm pt-2 border-t border-gray-200/60" :class="statusClass" x-text="statusMessage"></div>
            </div>
        </template>
    </div>
</x-modal>
