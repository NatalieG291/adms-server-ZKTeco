@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Dispositivos</h2>
        <!-- xxl toolbar -->
        <div class="btn-toolbar d-none d-lg-flex d-xl-flex" role="toolbar">
            @can('device-reboot')
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-warning" onclick="RestartDevice()">Reiniciar</button>
            </div>
            @endcan
            @canany(['device-clear-admin', 'device-clear-data', 'device-clear-log'])
            <div class="btn-group me-2" role="group" aria-label="Basic outlined example">
                <button id=btnGroupDrop1 type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Borrar datos
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    @can('device-clear-admin')
                    <li><a class="link-danger dropdown-item" onclick="ClearAdmin()">Borrar administrador</a></li>
                    @endcan
                    @can('device-clear-data')
                    <li><a class="link-danger dropdown-item" onclick="DeleteData()">Borrar datos</a></li>
                    @endcan
                    @can('device-clear-log')
                    <li><a class="link-dark dropdown-item" onclick="ClearLog()">Borrar registro</a></li>
                    @endcan
                </ul>
            </div>
            @endcanany
            @canany(['device-capture-setting', 'device-punch-period'])
            <div class="btn-group me-2" role="group" aria-label="Basic outlined example">
                <button id=btnGroupDrop1 type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Configuración del Dispositivo
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    @can('device-capture-setting')
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#pictureModal">Configuración de captura</a></li>
                    @endcan
                    @can('device-punch-period')
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#duplicateTimeModal">Período de acceso duplicado</a></li>
                    @endcan
                </ul>
            </div>
            @endcanany
            @can('device-remote-enroll')
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#enrollModal">Enrolamiento remoto</button>
            @endcan
            @can('device-download-data')
            <button type="button" class="btn btn-primary me-2 d-none d-xl-block d-xxl-none" data-bs-toggle="modal" data-bs-target="#downloadData">Descargar datos de usuario</button>
            @endcan
            @can('device-upload-data')
            <button type="button" class="btn btn-primary me-2 d-none d-xl-block d-xxl-none" data-bs-toggle="modal" data-bs-target="#uploadData">Subir datos de usuario</button>
            @endcan
            @canany(['device-download-data', 'device-upload-data'])
            <div class="btn-group me-2 d-lg-none d-xxl-block" role="group" >
                <button id="userdata" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Datos de usuarios
                </button>
                <ul class="dropdown-menu">
                    @can('device-download-data')
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#downloadData">Descargar datos de usuario</a></li>
                    @endcan
                    @can('device-upload-data')
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadData">Subir datos de usuario</a></li>
                    @endcan
                </ul>
            </div>
            @endcanany
        </div>
        <!--  -->
        <div class="btn-toolbar d-xs-block d-md-block d-lg-none" role="toolbar">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                    Acciones
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    @can('device-reboot')
                        <li><button class="dropdown-item bg-warning" type="button" onclick="RestartDevice()">Reiniciar</button></li>
                    @endcan
                    @canany(['device-clear-admin', 'device-clear-data', 'device-clear-log'])
                    <li><hr class="dropdown-divider"></li>
                    @can('device-clear-admin')
                    <li><button class="dropdown-item" type="button" onclick="ClearAdmin()">Borrar administrador</button></li>
                    @endcan
                    @can('device-clear-data')
                    <li><button class="dropdown-item bg-danger text-white" type="button" onclick="DeleteData()">Borrar datos</button></li>
                    @endcan
                    @can('device-clear-log')
                    <li><button class="dropdown-item" type="button" onclick="ClearLog()">Borrar registro</button></li>
                    @endcan
                    @endcanany
                    @canany(['device-capture-setting', 'device-punch-period'])
                    <li><hr class="dropdown-divider"></li>
                    @can('device-capture-setting')
                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#pictureModal">Configuracion de captura</button></li>
                    @endcan
                    @can('device-punch-period')
                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#duplicateTimeModal">Periodo de acceso duplicado</button></li>
                    @endcan
                    @endcanany
                    @can('device-remote-enroll')
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#enrollModal">Enrolamiento remoto</button></li>
                    @endcan
                    @canany(['device-download-data', 'device-upload-data'])
                    <li><hr class="dropdown-divider"></li>
                    @can('device-download-data')
                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#downloadData">Descargar datos de usuario</button></li>
                    @endcan
                    @can('device-upload-data')
                    <li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#uploadData">Subir datos de usuario</button></li>
                    @endcan
                    @endcanany
                </ul>
            </div>
        </div>
        
        <br><br>
        {{-- <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Tambah Device</a> --}}
        <div class="table-responsive">
            <table class="table table-bordered data-table table-hover align-middle" id="devices">
                <thead>
                    <tr>
                        @auth
                        <th></th>
                        @endauth
                        <th>Estado</th>
                        {{-- <th>No</th> --}}
                        @auth
                        <th class="d-none d-xl-table-cell">Número de Serie</th>
                        @endauth
                        <th>Descripción</th>
                        @auth
                        <th class="d-none d-xl-table-cell">Modelo</th>
                        <th class="d-none d-xl-table-cell">Dirección IP</th>
                        @endauth
                        <th>Número de transacciones</th>
                        <th>Fotos de asistencia</th>
                        <th>Número de usuarios</th>
                        <th>Conteo de huellas</th>
                        <th>Conteo de rostros</th>
                        <th>En línea</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($log as $d)
                        <tr onclick="document.getElementById('radioNoLabel{{ $d->id }}').checked = true; setCurrentSN('{{ $d->id }}')">
                            @auth
                            <td>
                                <div>
                                    <input class="form-check-input" type="radio" name="selectedDevice" id="radioNoLabel{{ $d->id }}" value="{{ $d->id }}" aria-label="..." onchange="setCurrentSN('{{ $d->id }}')">
                                </div>
                            </td>
                            @endauth
                            <td class="align-middle">
                                @switch(strtolower($d->state ?? ''))
                                    @case('offline')
                                        <img src="{{ asset('storage/state3.gif') }}" alt="Offline" title="Offline" style="width:15px;height:15px;">
                                        @break
                                    @case('ok')
                                        <img src="{{ asset('storage/state1.gif') }}" alt="Online" title="Online" style="width:15px;height:15px;">
                                        @break
                                    @case('uploading')
                                        <img src="{{ asset('storage/state4.gif') }}" alt="syncUp" title="syncUp" style="width:15px;height:15px;">
                                        @break
                                    @case('downloading')
                                        <img src="{{ asset('storage/state2.gif') }}" alt="syncDown" title="syncDowm" style="width:15px;height:15px;">
                                        @break
                                    @default
                                        {{ $d->state }}
                                @endswitch
                            </td>
                            {{-- <td>{{ $d->id }}</td> --}}
                            @auth
                            <td class="d-none d-xl-table-cell">{{ $d->no_sn }}</td>
                            @endauth
                            <td>{{ $d->descripcion }}</td>
                            @auth
                            <td class="d-none d-xl-table-cell">{{ $d->model }}</td>
                            <td class="d-none d-xl-table-cell">{{ $d->ip_address }}</td>
                            @endauth
                            <td>{{ $d->transaction_count }}</td>
                            <td>{{ $d->photo_count }}
                            <td>{{ $d->user_count }}</td>
                            <td>{{ $d->fp_count }}</td>
                            <td>{{ $d->face_count }}</td>
                            <td>{{ $d->online }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="loader loader-double" id="loader-lu"></div>


    <div class="modal fade" id="downloadData" tabindex="-1" aria-labelledby="downloadDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadDataLabel">Descargar datos de usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" onChange="document.getElementById('employeeSelect').classList.add('visually-hidden');" checked>
                        <label class="form-check-label" for="inlineRadio1">Todos los empleados</label>
                    </div>
                    <div class="mb-3 form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" onChange="document.getElementById('employeeSelect').classList.remove('visually-hidden');">
                        <label class="form-check-label" for="inlineRadio2">Empleado específico</label>
                    </div>
                    <div class="dropdown-container visually-hidden" id="employeeSelect">
                        <div class="dropdown-button noselect w-100">
                            <div class="dropdown-label">Empleados</div>
                            <div class="dropdown-quantity">(<span class="quantity"></span>)</div>
                        <i class="fa fa-chevron-down"></i>
				    </div>
                        <div class="dropdown-list" style="">
                            <input type="search" placeholder="Buscar empleados" class="dropdown-search">
                            <ul class="dropdown-list">
                            </ul>
                        </div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="DownloadData()">Descargar</button>
                </div>
            </div>
        </div>
    </div>

        <div class="modal fade" id="uploadData" tabindex="-1" aria-labelledby="uploadDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDataLabel">Subir datos de usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="container border-bottom mb-3">
                        <div class="mb-3 form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="allEmployeesUpload" value="option1" onChange="document.getElementById('employeeSelectUpload').classList.add('visually-hidden');" checked>
                            <label class="form-check-label" for="allEmployeesUpload">Todos los empleados</label>
                        </div>
                        <div class="mb-3 form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="specificEmployeeUpload" value="option2" onChange="document.getElementById('employeeSelectUpload').classList.remove('visually-hidden');">
                            <label class="form-check-label" for="specificEmployeeUpload">Empleado específico</label>
                        </div>
                    </div>  
                    <div class="container border-bottom mb-3">
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="checkbox" id="fingerprints" value="option1">
                            <label class="form-check-label" for="fingerprints">Huellas dactilares</label>
                        </div>
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="checkbox" id="faces" value="option2">
                            <label class="form-check-label" for="faces">Rostros</label>
                        </div>
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="checkbox" id="Photos" value="option3">
                            <label class="form-check-label" for="Photos">Fotos de usuario</label>
                        </div>
                    </div>
                    <div class="dropdown-container visually-hidden mb-3" id="employeeSelectUpload">
                        <div class="dropdown-button noselect w-100">
                            <div class="dropdown-label">Empleados</div>
                            <div class="dropdown-quantity">(<span class="quantity"></span>)</div>
                        <i class="fa fa-chevron-down"></i>
				    </div>
                        <div class="dropdown-list-upload" style="">
                            <input type="search" placeholder="Buscar empleados" class="dropdown-search">
                            <ul class="dropdown-list-upload">
                            </ul>
                        </div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="UploadData()">Subir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="duplicateTimeModal" tabindex="-1" aria-labelledby="duplicateTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateTimeModalLabel">Período de acceso duplicado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="setDuplicateTime()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pictureModal" tabindex="-1" aria-labelledby="pictureModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pictureModalLabel">Configuración de captura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="photoConfig" class="col-form-label">Modo de captura</label>
                            <select class="form-select" aria-label="Photo configuration" id="photoConfig">
                                <option value="0">Sin foto</option>
                                <option value="1">Capturar foto pero no guardar</option>
                                <option value="2">Tomar foto y guardar</option>
                                <option value="3">Guardar en verificacion correcta</option>
                                <option value="4">Guardar en verificacion fallida</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="SetPhotoConfig()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="enrollModalLabel">Enrolar empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="EnrollEmployee()" >Enrolar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
