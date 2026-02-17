@extends('layouts.app')  {{-- Asumsikan Anda memiliki layout utama --}}
@section('content')
<div class="container">
    <h2 class="mb-4">Attendance Photos</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered data-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>SN</th>
                    <th>Employee ID</th>
                    <th>Timestamp</th>
                    <th>Filename</th>
                    <th>Size (bytes)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($photos as $photo)
                    <tr>
                        <td>{{ $photo->id }}</td>
                        <td>{{ $photo->sn }}</td>
                        <td>{{ $photo->employee_id }}</td>
                        <td>{{ $photo->timestamp }}</td>
                        <td><img src="{{ asset('storage/attphoto/' . $photo->filename) }}" width="100" height="100"></td>
                        <td>{{ $photo->size }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            <div class="d-felx justify-content-center">
                {{ $photos->links() }}  {{-- Tampilkan pagination jika ada --}}
                    </div>
    </div>
    @endsection