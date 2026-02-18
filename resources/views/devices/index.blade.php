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
                    @auth
                    <th>Action</th>
                    @endauth
                </tr>
            </thead>
            <tbody>
                @foreach ($log as $d)
                    <tr>
                        {{-- <td>{{ $d->id }}</td> --}}
                        <td>{{ $d->no_sn }}</td>
                        <td>{{ $d->online }}</td>
                        @auth
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="RestartDevice('{{ $d->id }}')">Restart</button>
                            <button class="btn btn-sm btn-danger" onclick="ClearAdmin('{{ $d->id }}')">Clear admin</button>
                            <button class="btn btn-sm btn-secondary" onclick="ClearLog('{{ $d->id }}')">Clear log</button>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal" onclick="setCurrentSN('{{ $d->id }}')">Enroll Emp</button>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pictureModal" onclick="setCurrentSN('{{ $d->id }}')">Photo config</button>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#duplicateTimeModal" onclick="setCurrentSN('{{ $d->id }}')">Duplicate punch period</button>
                        </td>
                        @endauth
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <div class="modal fade" id="duplicateTimeModal" tabindex="-1" aria-labelledby="duplicateTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateTimeModalLabel">Duplicate punch period</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="startTime" class="col-form-label">Minutos de acceso duplicado</label>
                            <input type="number" value=1 min=0 max=1440 class="form-control" id="duplicateTime">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="setDuplicateTime()" data-bs-dismiss="modal">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pictureModal" tabindex="-1" aria-labelledby="pictureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pictureModalLabel">Configuracion de captura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="photoConfig" class="col-form-label">Modo de captura</label>
                            <select class="form-select" aria-label="Photo configuration" id="photoConfig">
                                <option value="0">Sin foto</option>
                                <option value="1">Capturar foto pero no guardar</option>
                                <option value="2">Verificar foto y guardar</option>
                                <option value="3">Verificacion exitosa y guardar</option>
                                <option value="4">Guardar</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="SetPhotoConfig()" data-bs-dismiss="modal">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enrollModalLabel">Enroll Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="empid" class="col-form-label">ID Empleado</label>
                            <input type="text" class="form-control" id="empid">
                        </div>
                        <div class="mb-3">
                            <label for="dedo" class="col-form-label">Dedo</label>
                            <select class="form-select" aria-label="Dedo a enrolar" id="dedo">
                                <optgroup label="Mano izquierda">
                                    <option value="4">Pulgar</option>
                                    <option value="3">Índice</option>
                                    <option value="2">Medio</option>
                                    <option value="1">Anular</option>
                                    <option value="0">Meñique</option>
                                </optgroup>
                                <optgroup label="Mano derecha">
                                    <option value="5">Pulgar</option>
                                    <option value="6">Índice</option>
                                    <option value="7">Medio</option>
                                    <option value="8">Anular</option>
                                    <option value="9">Meñique</option>
                                </optgroup>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="EnrollEmployee()" data-bs-dismiss="modal">Enroll</button>
                </div>
            </div>
        </div>
    </div>
@endsection
