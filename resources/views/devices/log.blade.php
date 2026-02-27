@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Device log</h2>
        <div class="table-responsive">
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
        </div>
    <div class="d-flex justify-content-center">
        {{ $logs->onEachSide(1)->links() }}  {{-- Tampilkan pagination jika ada --}}
    </div>
    </div>
@endsection
