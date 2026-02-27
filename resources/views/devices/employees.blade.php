@extends('layouts.app')
@section('content')
<?php
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
?>
    <div class="container">
        <h2>Empleados</h2>
        <div class="container mb-3">
            <div class="row">
                <div class="col-md-8"></div>
                    <div class="col-6 col-md-4 col-12">
                        <form method="get" action="">
                            <div class="input-group">
                                <input type="text" class="form-control" name="busqueda" placeholder="Buscar" value="<?php echo htmlspecialchars($busqueda); ?>">
                                <button  n class="btn btn-outline-secondary" type="submit">Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered data-table" id="employees">
                <thead>
                    <tr>
                        <th>Editar</th>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th class="d-none d-xl-table-cell">Privilegio</th>
                        <th class="d-none d-xl-table-cell">Contraseña</th>
                        <th class="d-none d-xl-table-cell">Tarjeta</th>
                        <th class="d-none d-xl-table-cell">Tipo de verificación</th>
                        <th>Ultima actualizacion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $d)
                        <tr>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEmployeeModal" onclick="setCurrentEmployee('{{ $d->employee_id }}', '{{ $d->name }}', '{{ $d->pri }}', '{{ $d->pri_id }}', '{{ $d->passwd }}', '{{ $d->card }}', '{{ $d->verify_id }}')">Editar</button>
                            </td>
                            <td>{{ $d->employee_id }}</td>
                            <td>{{ $d->name }}</td>
                            <td class="d-none d-xl-table-cell">{{ $d->pri }}</td>
                            <td class="d-none d-xl-table-cell">{{ $d->passwd }}</td>
                            <td class="d-none d-xl-table-cell">{{ $d->card }}</td>
                            <td class="d-none d-xl-table-cell">{{ $d->verify }}</td>
                            <td>{{ $d->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center">
                {{ $employees->onEachSide(1)->links() }}  {{-- Tampilkan pagination jika ada --}}
    </div>
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Editar empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3 d-flex align-items-start justify-content-center">
                            <div class="w-100 d-flex flex-column align-items-center">
                                <img id="employeePhoto" src="" alt="Foto del empleado" class="img-thumbnail" onerror="this.src='/storage/userpic/default.png';" style="width: 100%; height: auto; object-fit: cover;">
                                <button class="btn btn-sm btn-secondary mt-2" onclick="document.getElementById('employeePhotoInput').click()">Cambiar foto</button>
                                <input type="file" id="employeePhotoInput" name="photo" accept="image/jpeg" style="display: none;" onchange="uploadEmployeePhoto()">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="employeeName" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="employeeName" name="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="employeePri" class="form-label">Privilegio</label>
                                    <select class="form-select" id="employeePri" name="pri">
                                        <option value="0">Empleado</option>
                                        <option value="2">Registro</option>
                                        <option value="6">Administrador del sistema</option>
                                        <option value="10">Definido por el usuario</option>
                                        <option value="14">Superadministrador</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="employeePasswd" class="form-label">Contraseña</label>
                                    <input type="text" class="form-control" id="employeePasswd" name="passwd">
                                </div>
                                <div class="col-md-6">
                                    <label for="employeeCard" class="form-label">Tarjeta</label>
                                    <input type="text" class="form-control" id="employeeCard" name="card">
                                </div>
                                <div class="col-md-6">
                                    <label for="employeeVerify" class="form-label">Tipo de verificación</label>
                                    <select class="form-select" id="employeeVerify" name="verify">
                                        <option value="-1">Aplicar modo de grupo</option>
                                        <option value="0">Cualquiera</option>
                                        <option value="1">Huella dactilar</option>
                                        <option value="2">Solo ID de usuario</option>
                                        <option value="3">Contraseña</option>
                                        <option value="4">Solo tarjeta</option>
                                        <option value="5">Huella/Contraseña</option>
                                        <option value="6">Huella/Tarjeta</option>
                                        <option value="7">Tarjeta/Contraseña</option>
                                        <option value="8">ID de usuario y huella</option>
                                        <option value="9">Huella y contraseña</option>
                                        <option value="10">Huella y tarjeta</option>
                                        <option value="11">Contraseña y tarjeta</option>
                                        <option value="12">Huella, contraseña y tarjeta</option>
                                        <option value="13">ID de usuario, huella y contraseña</option>
                                        <option value="14">Huella y tarjeta / ID de usuario</option>
                                        <option value="15">Solo rostro</option>
                                        <option value="16">Rostro y huella</option>
                                        <option value="17">Rostro y contraseña</option>
                                        <option value="18">Rostro y tarjeta</option>
                                        <option value="19">Rostro, huella y tarjeta</option>
                                        <option value="20">Rostro, huella y contraseña</option>
                                    </select>   
                                </div>
                                <div class="col-6">
                                    <input class="form-check-input" type="checkbox" id="send" name="sendToDevices">
                                    <label class="form-check-label" for="send">Enviar a dispositivos</label>
                                </div>
                                <div class="col-6">
                                    <select class="form-select" id="devices" name="devices[]">
                                        <option value="all">Todos los dispositivos</option>
                                        @foreach ($devices as $device)
                                            <option value="{{ $device->id }}">{{ $device->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="EditEmployeeData()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
@endsection