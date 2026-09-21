@if(! auth()->user()->can('view-attendance-photos'))
    @php
        auth()->logout();
        
        header('Location: ' . route('login'));
    @endphp
@endif

@extends('layouts.app')  {{-- Asumsikan Anda memiliki layout utama --}}
@section('content')
<div class="container">
    <h2 class="mb-4">Fotos de Asistencia</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered data-table">
            <thead class="thead-dark">
                <tr>
                    <th class="d-none d-xl-table-cell">Lector</th>
                    <th>Clave de empleado</th>
                    <th>Fecha y Hora</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($photos as $photo)
                    <tr>
                        <td class="d-none d-xl-table-cell">{{ $photo->descripcion }}</td>
                        <td>{{ $photo->employee_id }}</td>
                        <td>{{ $photo->timestamp }}</td>
                        <td><img src="{{ asset('storage/attphoto/' . $photo->filename) }}" width="100" height="100"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
            <div class="d-flex justify-content-center">
                {{ $photos->onEachSide(1)->links() }}  {{-- Tampilkan pagination jika ada --}}
                    </div>
    </div>
    @endsection