@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Employees</h2>
        <br><br>
        <table class="table table-bordered data-table" id="employees">
            <thead>
                <tr>
                    <th>Edit</th>
                    <th>PIN</th>
                    <th>Name</th>
                    <th>Privilege</th>
                    <th>Password</th>
                    <th>Card</th>
                    <th>Verify Type</th>
                    <th>Updated at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $d)
                    <tr>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEmployeeModal" onclick="setCurrentEmployee('{{ $d->employee_id }}', '{{ $d->name }}', '{{ $d->pri }}', '{{ $d->pri_id }}', '{{ $d->passwd }}', '{{ $d->card }}', '{{ $d->verify_id }}')">Edit</button>
                        </td>
                        <td>{{ $d->employee_id }}</td>
                        <td>{{ $d->name }}</td>
                        <td>{{ $d->pri }}</td>
                        <td>{{ $d->passwd }}</td>
                        <td>{{ $d->card }}</td>
                        <td>{{ $d->verify }}</td>
                        <td>{{ $d->updated_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3 d-flex align-items-start justify-content-center">
                            <div class="w-100 d-flex flex-column align-items-center">
                                <img id="employeePhoto" src="" alt="Employee Photo" class="img-thumbnail" onerror="this.src='/storage/userpic/default.png';" style="width: 100%; height: auto; object-fit: cover;">
                                <button class="btn btn-sm btn-secondary mt-2" onclick="document.getElementById('employeePhotoInput').click()">Change Photo</button>
                                <input type="file" id="employeePhotoInput" name="photo" accept="image/jpeg" style="display: none;" onchange="uploadEmployeePhoto()">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="employeeName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="employeeName" name="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="employeePri" class="form-label">Privilege</label>
                                    <select class="form-select" id="employeePri" name="pri">
                                        <option value="0">Employee</option>
                                        <option value="2">Register</option>
                                        <option value="6">System Administrator</option>
                                        <option value="10">User Defined</option>
                                        <option value="14">Super Administrator</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="employeePasswd" class="form-label">Password</label>
                                    <input type="text" class="form-control" id="employeePasswd" name="passwd">
                                </div>
                                <div class="col-md-6">
                                    <label for="employeeCard" class="form-label">Card</label>
                                    <input type="text" class="form-control" id="employeeCard" name="card">
                                </div>
                                <div class="col-md-6">
                                    <label for="employeeVerify" class="form-label">Verify Type</label>
                                    <select class="form-select" id="employeeVerify" name="verify">
                                        <option value="-1">Apply group mode</option>
                                        <option value="0">Any</option>
                                        <option value="1">Fingerprint</option>
                                        <option value="2">UserID Only</option>
                                        <option value="3">Password</option>
                                        <option value="4">Card Only</option>
                                        <option value="5">Fingerprint/Password</option>
                                        <option value="6">Fingerprint/Card</option>
                                        <option value="7">Card/Password</option>
                                        <option value="8">UserID & Fingerprint</option>
                                        <option value="9">Fingerptint & Password</option>
                                        <option value="10">Fingerprint & Card</option>
                                        <option value="11">Password & Card</option>
                                        <option value="12">Fingerprint & Password & Card</option>
                                        <option value="13">UserID & Fingerprint & Password</option>
                                        <option value="14">Fingerprint & Card / UserID</option>
                                        <option value="15">Face Only</option>
                                        <option value="16">Face & Fingerprint</option>
                                        <option value="17">Face & Password</option>
                                        <option value="18">Face & Card</option>
                                        <option value="19">Face & Fingerprint & Card</option>
                                        <option value="20">Face & Fingerprint & Password</option>
                                    </select>   
                                </div>
                                <div class="col-6">
                                    <input class="form-check-input" type="checkbox" id="send" name="sendToDevices">
                                    <label class="form-check-label" for="send">Send to devices</label>
                                </div>
                                <div class="col-6">
                                    <select class="form-select" id="devices" name="devices[]">
                                        <option value="all">All Devices</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="EditEmployeeData()">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection