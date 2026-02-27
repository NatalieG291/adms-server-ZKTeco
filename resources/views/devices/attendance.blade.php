@extends('layouts.app')  {{-- Asumsikan Anda memiliki layout utama --}}

@section('content')
<div class="container">
    <h2 class="mb-4">Asistencia</h2>

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
                    <th>Status 1</th>
                    <th class="d-none d-xl-table-cell">Status 2</th>
                    <th class="d-none d-xl-table-cell">Status 3</th>
                    <th class="d-none d-xl-table-cell">Status 4</th>
                    <th class="d-none d-xl-table-cell">Status 5</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->id }}</td>
                        <td class="d-none d-xl-table-cell">{{ $attendance->sn }}</td>
                        <td>{{ $attendance->employee_id }}</td>
                        <td>{{ $attendance->timestamp }}</td>
                        <td>{{ $attendance->status1 }}</td>
                        <td class="d-none d-xl-table-cell">{{ $attendance->status2 }}</td>
                        <td class="d-none d-xl-table-cell">{{ $attendance->status3 }}</td>
                        <td class="d-none d-xl-table-cell">{{ $attendance->status4 }}</td>
                        <td class="d-none d-xl-table-cell">{{ $attendance->status5 }}</td>

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