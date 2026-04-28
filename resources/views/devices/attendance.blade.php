@if(! auth()->user()->can('view-attendance'))
    @php
        auth()->logout();
        
        header('Location: ' . route('login'));
    @endphp
@endif
@extends('layouts.app')  {{-- Asumsikan Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <h2 class="mb-4">Asistencia</h2>

    <form method="GET" class="row g-3 mb-3">
        <div class="col">
            <label class="form-label" for="employeeid">Empleado</label>
            <select class="form-select" name="employeeid[]" id="employeeid" multiple="multiple" width="auto">
                @foreach($employees as $employee)
                    <option value="{{ $employee->employee_id }}" {{ in_array($employee->employee_id, request('employee_id', [])) ? 'selected' : '' }}>
                        {{ $employee->employee_id }} - {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="form-label" for="device_id">Lector</label>
            <select class="form-select" name="deviceid[]" id="device_id" multiple="multiple" width="auto">
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ request('id') == $device->id ? 'selected' : '' }}>
                        {{ $device->descripcion }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="form-label">Fecha inicio</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col">
            <label class="form-label">Hora inicio</label>
            <input type="time" name="start_time" class="form-control" value="{{ request('start_time') }}">
        </div>
        <div class="col">
            <label class="form-label">Fecha fin</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col">
            <label class="form-label">Hora fin</label>
            <input type="time" name="end_time" class="form-control" value="{{ request('end_time') }}">
        </div>
        <div class="col-6 align-self-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <button type="submit" name="export" value="1" class="btn btn-success">Exportar a Excel</button>
        </div>
    </form>

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
                    <th>Hora</th>
                    <th>Metodo checada</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td class="d-none d-xl-table-cell">{{ $attendance->descripcion }}</td>
                        <td>{{ $attendance->employee_id }}</td>
                        <td>{{ $attendance->timestamp }}</td>
                        <td>{{ $attendance->status1 }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- source: https://stackoverflow.com/a/70119390 -->
    <div class="d-flex justify-content-center">
                {{ $attendances->onEachSide(1)->links() }}  {{-- Tampilkan pagination jika ada --}}
                    </div>


</div>
@endsection