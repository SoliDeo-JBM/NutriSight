@extends('layouts.dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Attendance Scanner</h1>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
        <div id="reader" style="width: 400px; margin: 0 auto;"></div>
        <div id="result" class="mt-4 font-bold text-center"></div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            fetch('{{ route('attendance.scan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ student_number: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('result').innerText = data.success || data.error;
                setTimeout(() => { document.getElementById('result').innerText = ''; }, 3000);
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
@endsection
