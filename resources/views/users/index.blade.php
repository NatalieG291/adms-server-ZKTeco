@if(! auth()->user()->hasRole('admin'))
    @php
        auth()->logout();
        
        header('Location: ' . route('login'));
    @endphp
@endif
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Usuarios</h2>
        <div class="container mb-3">
            <div class="row">
                <!-- <div class="col-md-8"></div> -->
                    <div class="col-lg-12">
                        <div class="form-actions form-group text-end">
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#newUserModal" onclick="newUserForm()">Nuevo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-bordered data-table align-middle table-responsive" id="users">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Creado</th>
                    <th style="width: 15%">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $d)
                    <tr>
                        <td>{{ $d->name}}</td>
                        <td>{{ $d->email}}</td>
                        <td>{{ $d->created_at}}</td>
                        <td class="text-end">
                            <button class="btn btn-primary" type="button" @if( $d->name == 'Administrador' ) disabled @endif data-bs-toggle="modal" data-bs-target="#newUserModal" onclick="getUserData('{{ $d->email }}')">Editar</button>
                            <button class="btn btn-danger" type="button" @if( $d->name == 'Administrador' ) disabled @endif id="{{ $d->email }}" onclick="dropUser('{{ $d->email }}')">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>  

    <div class="loader loader-double" id="loader-lu"></div>

    <div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newUserModalTitle">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="userName" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="userName" name="name">
                                </div>
                                <div class="col-md-12">
                                    <label for="userEmail" class="form-label">Correo</label>
                                    <input type="text" class="form-control" id="userEmail" name="email">
                                </div>
                                <div class="col-md-12">
                                    <label for="userPasswd" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="userPasswd" name="passwd">
                                </div>
                                <div class="col-md-12">
                                    <div class="dropdown-container" id="permissionsSelect">
                                        <div class="dropdown-button noselect w-100">
                                            <div class="dropdown-label">Permisos</div>
                                            <div class="dropdown-quantity">(<span class="quantity"></span>)</div>
                                    </div>
                                        <div class="dropdown-list-permissions" style="">
                                            <!-- <input type="search" placeholder="Buscar empleados" class="dropdown-search"> -->
                                            <ul class="dropdown-list-permissions">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="userAction" onclick="newUser()">Crear usuario</button>
                </div>
            </div>
        </div>
    </div>
    <script> 
        document.addEventListener("DOMContentLoaded", () => {

            document.querySelectorAll(".dropdown-container").forEach(container => {

                container.addEventListener("click", e => {
                    if (e.target.classList.contains("dropdown-button")) {
                        const list = container.querySelector(".dropdown-list-permissions");
                        list.classList.toggle("show");
                    }
                });

                container.addEventListener("input", e => {
                    if (e.target.classList.contains("dropdown-search")) {
                        const search = e.target.value.toLowerCase();
                        const items = container.querySelectorAll(".dropdown-list-permissions li");

                        items.forEach(li => {
                            const text = li.textContent.toLowerCase();
                            li.style.display = text.includes(search) ? "" : "none";
                        });
                    }
                });

                container.addEventListener("change", e => {
                    if (e.target.type === "checkbox") {
                        const checked = container.querySelectorAll('input[type="checkbox"]:checked').length;
                        container.querySelector(".quantity").textContent = checked || "Any";
                    }
                });

            });

            function createListItem(usr) {
                const li = document.createElement("li");
                const capName = `${usr.description}`;

                li.innerHTML = `
                    <label class="checkbox-wrap">
                        <input name="${usr.name}" type="checkbox">
                        <span>${capName}</span>
                        <span class="checkmark"></span>
                    </label>
                `;
                return li;
            }

            const allPermissionsLists = document.querySelectorAll(".dropdown-list-permissions ul");

            fetch("{{ route('users.get-permissions') }}", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(r => r.json())
            .then(data => {
                const usrData = data.permissions;

                allPermissionsLists.forEach(ul => {
                    usrData.forEach(usr => {
                        ul.appendChild(createListItem(usr));
                    });
                });
            });
        });
    </script>
    @php
        $permissionsRoutes = [
            'getPermissions' => route('users.get-permissions'),
            'getUserPermissions' => route('users.get-user-permissions'),
            'newUser' => route('users.new-user'),
            'dropUser' => route('users.drop-user'),
            'csrfToken' => csrf_token(),
        ];
    @endphp
    <script>
        window.usersRoutes = @json($permissionsRoutes);
    </script>
    @vite(['resources/js/users.js'])
@endsection