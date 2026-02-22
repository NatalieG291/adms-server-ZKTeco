@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $lable }}</h2>
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-warning mb-3" onclick="RestartDevice()">Restart</button>
            </div>
            <div class="btn-group me-2" role="group" aria-label="Basic outlined example" style="display: block !important;">
                <button id=btnGroupDrop1 type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Clear data
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <li><a class="link-danger dropdown-item" onclick="ClearAdmin()">Clear admin</a></li>
                    <li><a class="link-danger dropdown-item" onclick="DeleteData()">Delete data</a></li>
                    <li><a class="link-dark dropdown-item" onclick="ClearLog()">Clear log</a></li>
                </ul>
            </div>
            <div class="btn-group me-2" role="group" aria-label="Basic outlined example" style="display: block !important;">
                <button id=btnGroupDrop1 type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Device Settings
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#pictureModal">Capture Setting</a></li>
                    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#duplicateTimeModal">Duplicate punch period</a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary mb-3 me-2" data-bs-toggle="modal" data-bs-target="#enrollModal">Enroll Remotely</button>
            <button type="button" class="btn btn-primary mb-3 me-2" data-bs-toggle="modal" data-bs-target="#downloadData">Download User Data</button>
            <button type="button" class="btn btn-primary mb-3 me-2" data-bs-toggle="modal" data-bs-target="#uploadData">Upload User Data</button>
            
        </div>
        <br><br>
        {{-- <a href="{{ route('devices.create') }}" class="btn btn-primary mb-3">Tambah Device</a> --}}
        <table class="table table-bordered data-table" id="devices">
            <thead>
                <tr>
                    <th></th>
                    {{-- <th>No</th> --}}
                    <th>Serial Number</th>
                    <th>Description</th>
                    <th>Model</th>
                    <th>IP Address</th>
                    <th>Transaction Count</th>
                    <th>Attendance photos</th>
                    <th>User Count</th>
                    <th>FP Count</th>
                    <th>Face Count</th>
                    <th>Online</th>
                    @auth
                    <!-- <th>Action</th> -->
                    @endauth
                </tr>
            </thead>
            <tbody>
                @foreach ($log as $d)
                    <tr onclick="document.getElementById('radioNoLabel{{ $d->id }}').checked = true; setCurrentSN('{{ $d->id }}')">
                        <td>
                            <div>
                                <input class="form-check-input" type="radio" name="selectedDevice" id="radioNoLabel{{ $d->id }}" value="{{ $d->id }}" aria-label="..." onchange="setCurrentSN('{{ $d->id }}')">
                            </div>
                        </td>
                        {{-- <td>{{ $d->id }}</td> --}}
                        <td>{{ $d->no_sn }}</td>
                        <td>{{ $d->descripcion }}</td>
                        <td>{{ $d->model }}</td>
                        <td>{{ $d->ip_address }}</td>
                        <td>{{ $d->transaction_count }}</td>
                        <td>{{ $d->photo_count }}
                        <td>{{ $d->user_count }}</td>
                        <td>{{ $d->fp_count }}</td>
                        <td>{{ $d->face_count }}</td>
                        <td>{{ $d->online }}</td>
                        @auth
                        <!-- <td>
                            <button class="btn btn-sm btn-warning" onclick="RestartDevice('{{ $d->id }}')">Restart</button>
                            <button class="btn btn-sm btn-danger" onclick="ClearAdmin('{{ $d->id }}')">Clear admin</button>
                            <button class="btn btn-sm btn-secondary" onclick="ClearLog('{{ $d->id }}')">Clear log</button>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#enrollModal" onclick="setCurrentSN('{{ $d->id }}')">Enroll Emp</button>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pictureModal" onclick="setCurrentSN('{{ $d->id }}')">Photo config</button>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#duplicateTimeModal" onclick="setCurrentSN('{{ $d->id }}')">Duplicate punch period</button>
                        </td> -->
                        @endauth
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <div class="loader loader-double" id="loader-lu"></div>


    <div class="modal fade" id="downloadData" tabindex="-1" aria-labelledby="downloadDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadDataLabel">Download user data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" onChange="document.getElementById('employeeSelect').classList.add('visually-hidden');" checked>
                        <label class="form-check-label" for="inlineRadio1">All employees</label>
                    </div>
                    <div class="mb-3 form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" onChange="document.getElementById('employeeSelect').classList.remove('visually-hidden');">
                        <label class="form-check-label" for="inlineRadio2">Specific employee</label>
                    </div>
                    <div class="dropdown-container visually-hidden" id="employeeSelect">
                        <div class="dropdown-button noselect w-100">
                            <div class="dropdown-label">Employees</div>
                            <div class="dropdown-quantity">(<span class="quantity"></span>)</div>
                        <i class="fa fa-chevron-down"></i>
				    </div>
                        <div class="dropdown-list" style="">
                            <input type="search" placeholder="Search employees" class="dropdown-search">
                            <ul class="dropdown-list">
                            </ul>
                        </div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="DownloadData()">Download</button>
                </div>
            </div>
        </div>
    </div>

        <div class="modal fade" id="uploadData" tabindex="-1" aria-labelledby="uploadDataLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDataLabel">Upload user data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container border-bottom mb-3">
                        <div class="mb-3 form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="allEmployeesUpload" value="option1" onChange="document.getElementById('employeeSelectUpload').classList.add('visually-hidden');" checked>
                            <label class="form-check-label" for="allEmployeesUpload">All employees</label>
                        </div>
                        <div class="mb-3 form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="specificEmployeeUpload" value="option2" onChange="document.getElementById('employeeSelectUpload').classList.remove('visually-hidden');">
                            <label class="form-check-label" for="specificEmployeeUpload">Specific employee</label>
                        </div>
                    </div>  
                    <div class="container border-bottom mb-3">
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="checkbox" id="fingerprints" value="option1">
                            <label class="form-check-label" for="fingerprints">Fingerprints</label>
                        </div>
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="checkbox" id="faces" value="option2">
                            <label class="form-check-label" for="faces">Faces</label>
                        </div>
                        <div class="form-check form-check-inline mb-3">
                            <input class="form-check-input" type="checkbox" id="Photos" value="option3">
                            <label class="form-check-label" for="Photos">User Photos</label>
                        </div>
                    </div>
                    <div class="dropdown-container visually-hidden mb-3" id="employeeSelectUpload">
                        <div class="dropdown-button noselect w-100">
                            <div class="dropdown-label">Employees</div>
                            <div class="dropdown-quantity">(<span class="quantity"></span>)</div>
                        <i class="fa fa-chevron-down"></i>
				    </div>
                        <div class="dropdown-list-upload" style="">
                            <input type="search" placeholder="Search employees" class="dropdown-search">
                            <ul class="dropdown-list-upload">
                            </ul>
                        </div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="UploadData()">Upload</button>
                </div>
            </div>
        </div>
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
                    <button type="button" class="btn btn-primary" onclick="setDuplicateTime()">Save</button>
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
                    <button type="button" class="btn btn-primary" onclick="SetPhotoConfig()">Save</button>
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
                    <button type="button" class="btn btn-primary" onclick="EnrollEmployee()" >Enroll</button>
                </div>
            </div>
        </div>
    </div>
@endsection
