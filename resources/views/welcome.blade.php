<script src="{{ asset('assets/plugins/aerodrop/aerodrop.min.js') }}"></script>
<link href="{{ asset('assets/plugins/aerodrop/aerodrop.min.css') }}" rel="stylesheet"></link>

<x-lawyer.app>
   

    <div class="card">
        <div class="card-body">
            <div id="aerodrop" class="aerodrop mb-3"></div>
        </div>
    </div>

    @push('custom-scripts')
        <script>
            const aerodrop = new AeroDrop(document.querySelector('#aerodrop'), {
                name: 'attachments',
                uploadURL: '/upload',
                maxFileSize: 10 * 1024 * 1024, // 5 MB limit
                allowedFileTypes: ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                },
            });

            aerodrop.onupload = (res) => {
                console.log(res);
            };

            aerodrop.onerror = (error) => {
                console.log(error);
            };
        </script>
    @endpush


</x-lawyer.app>
