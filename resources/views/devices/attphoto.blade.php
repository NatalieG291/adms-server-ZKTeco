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
                    <th class="d-none d-xl-table-cell">SN</th>
                    <th>Clave de empleado</th>
                    <th>Hora</th>
                    <th>Foto</th>
                    <th class="d-none d-xl-table-cell">Tamaño (bytes)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($photos as $photo)
                    <tr>
                        <td>{{ $photo->id }}</td>
                        <td class="d-none d-xl-table-cell">{{ $photo->descripcion }}</td>
                        <td>{{ $photo->employee_id }}</td>
                        <td>{{ $photo->timestamp }}</td>
                        <td><img src="{{ asset('storage/attphoto/' . $photo->filename) }}" width="100" height="100"></td>
                        <td class="d-none d-xl-table-cell">{{ $photo->size }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            <div class="d-flex justify-content-center">
                {{ $photos->onEachSide(1)->links() }}  {{-- Tampilkan pagination jika ada --}}
                    </div>
    </div>
    @endsection