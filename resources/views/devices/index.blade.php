@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $lable }}</h2>
        {{-- <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Tambah Device</a> --}}
        <table class="table table-bordered data-table" id="devices">
            <thead>
                <tr>
                    {{-- <th>No</th> --}}
                    <th>Serial Number</th>
                    <th>Online</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($log as $d)
                    <tr>
                        {{-- <td>{{ $d->id }}</td> --}}
                        <td>{{ $d->no_sn }}</td>
                        <td>{{ $d->online }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="RestartDevice('{{ $d->id }}')">Restart</button>
                            <button class="btn btn-sm btn-danger" onclick="ClearAdmin('{{ $d->id }}')">Clear admin</button>
                            <button class="btn btn-sm btn-secondary" onclick="ClearLog('{{ $d->id }}')">Clear log</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection

<!-- @push('scripts')
<script>
    function RestartDevice(sn) {
        if (!confirm('Restart device with ID ' + sn + '?')) return;

        fetch("{{ route('devices.restart') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sn: sn })
        })
        .then(function(response){ return response.json(); })
        .then(function(data){
            if (data && data.message) {
                alert(data.message);
            } else {
                alert('Restart request sent');
            }
        })
        .catch(function(err){
            console.error(err);
            alert('Failed to send restart request');
        });
    }
</script>
@endpush -->
