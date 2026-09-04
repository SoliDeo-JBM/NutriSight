<x-modal name="attendance-scanner" :show="false" focusable>
    <div class="p-6" x-data="{
        scanValue: '',
        status: '',
        statusClass: '',
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.status = data.success;
                    this.statusClass = 'text-green-600';
                } else {
                    this.status = data.error;
                    this.statusClass = 'text-red-600';
                }
                this.scanValue = '';
            });
        }
    }">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Scan Student QR Code</h2>
        
        <input type="text" 
               x-ref="scannerInput"
               x-model="scanValue" 
               x-on:keyup.enter="processScan()"
               x-on:open-modal.window="$event.detail == 'attendance-scanner' ? setTimeout(() => $refs.scannerInput.focus(), 100) : null"
               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="Please scan now..." />
        
        <div class="mt-4 text-center font-semibold" :class="statusClass" x-text="status"></div>
    </div>
</x-modal>
