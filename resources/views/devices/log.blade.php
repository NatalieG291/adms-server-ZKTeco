@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Device log1</h2>
        <table class="table table-bordered data-table" id="devices">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Url</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->url }}</td>
                        <td>{{ $d->data }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    <div class="d-felx justify-content-center">
        {{ $logs->links() }}  {{-- Tampilkan pagination jika ada --}}
    </div>
    </div>
@endsection
