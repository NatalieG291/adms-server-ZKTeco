let currentEmployee = null;
let currentEmployeeName = null;

function setCurrentEmployee(
    empid,
    name,
    pri,
    pri_id,
    passwd,
    card,
    verify,
    fingerprints,
) {
    currentEmployee = empid;
    currentEmployeeName = name;
    document.getElementById("empid").value = empid;
    document.getElementById("employeeName").value = name;
    document.getElementById("employeePri").value = pri_id;
    document.getElementById("employeePasswd").value = passwd;
    document.getElementById("employeeCard").value = card;
    document.getElementById("employeeVerify").value = verify;
    document.getElementById("employeePhoto").src =
        "/storage/userpic/" + empid + ".jpg";
    if (fingerprints == "1") {
        document.getElementById("fingers").value =
            fingerprints + " Huella registrada";
    } else {
        document.getElementById("fingers").value =
            fingerprints + " Huellas registradas";
    }
}

function OpenEnrollEmployeeModal() {
    $("#loader-lu").addClass("is-active");
    var fingers = [
        "#finger-thumb_1",
        "#finger-index_1",
        "#finger-middle_1",
        "#finger-ring_1",
        "#finger-pinky_1",
        "#finger-pinky_2",
        "#finger-ring_2",
        "#finger-middle_2",
        "#finger-index_2",
        "#finger-thumb_2",
    ];
    for (var i = 0; i < fingers.length; i++) {
        document.querySelector("[finger-index='" + Number(i) + "']").checked =
            false;
    }
    fetch(employeeRoutes.fingerprints, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "employeeRoutes.csrfToken",
        },
        body: JSON.stringify({ pin: currentEmployee }),
    })
        .then((r) => r.json())
        .then((data) => {
            $("#loader-lu").removeClass("is-active");
            const fingerprints = data.fids;
            fingerprints.forEach((finger) => {
                document.querySelector(
                    "[finger-index='" + Number(finger) + "']",
                ).style.backgroundColor = "#655cca";
                document.querySelector(
                    "[finger-index='" + Number(finger) + "']",
                ).disabled = true;
            });
        })
        .catch((err) => {
            console.error(err);
            Swal.fire("Error al obtener las huellas del empleado", "", "error");
            $("#loader-lu").removeClass("is-active");
        });

    document.getElementById("send").checked = false;
    document.querySelector("#enrollModal .modal-title").textContent =
        "Enrolar empleado | " + currentEmployee.substring(1, 10);
    var modal = new bootstrap.Modal(document.getElementById("enrollModal"), {});
    modal.show();
    $("#devicesEnroll").select2({
        dropdownParent: $("#enrollModal .modal-body"),
    });
    $("#devicesEnroll").val(null).trigger("change");
    $("#devicesEnroll").val("all").trigger("change");

    $("#devices-select-Enroll").select2({
        dropdownParent: $("#enrollModal .modal-body"),
    });
    $("#devices-select-Enroll").val(null).trigger("change");
    $("#devices-select-Enroll").val("all").trigger("change");
}
function EditEmployeeData() {
    if (!currentEmployee) {
        Swal.fire("Ningún empleado seleccionado", "", "error");
        return;
    }

    const empid = document.getElementById("empid").value;
    const name = document.getElementById("employeeName").value;
    const pri = document.getElementById("employeePri").value;
    const passwd = document.getElementById("employeePasswd").value;
    const card = document.getElementById("employeeCard").value;
    const verify = document.getElementById("employeeVerify").value;
    const send = document.getElementById("send").checked;
    const devices = document.getElementById("devices").value;

    Swal.fire({
        title: "¿Estás seguro?",
        text: "¿Guardar cambios en el empleado " + currentEmployee + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, guardarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            if (currentEmployee != empid) {
                Swal.fire({
                    title: "Cambio de clave de empleado",
                    text: "Se edito la clave del empleado, esto eliminara la clave anterior del empleado en todos los lectores, debera enviar al empleado con la nueva clave de manera manual desde la seccion de Dispositivos, ¿Desea continuar?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "¡Sí, continuar!",
                    cancelButtonText: "No, cancelar",
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(employeeRoutes.EditEmployeeData, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": employeeRoutes.csrfToken,
                            },
                            body: JSON.stringify({
                                newid: empid,
                                empid: currentEmployee,
                                name: name,
                                pri: pri,
                                passwd: passwd,
                                card: card,
                                verify: verify,
                                send: send,
                                devices: devices,
                            }),
                        })
                            .then((r) => r.json())
                            .then((data) =>
                                Swal.fire(
                                    data.message ||
                                        "Solicitud de edición de empleado enviada",
                                    "",
                                    "success",
                                ),
                            )
                            .then(() => {
                                location.reload();
                            })
                            .catch((err) => {
                                console.error(err);
                                Swal.fire(
                                    "Error al enviar la solicitud de edición de empleado",
                                    "",
                                    "error",
                                );
                            });
                    }
                });
            } else {
                fetch(employeeRoutes.EditEmployeeData, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": employeeRoutes.csrfToken,
                    },
                    body: JSON.stringify({
                        empid: currentEmployee,
                        name: name,
                        pri: pri,
                        passwd: passwd,
                        card: card,
                        verify: verify,
                        send: send,
                        devices: devices,
                    }),
                })
                    .then((r) => r.json())
                    .then((data) =>
                        Swal.fire(
                            data.message ||
                                "Solicitud de edición de empleado enviada",
                            "",
                            "success",
                        ),
                    )
                    .then(() => {
                        location.reload();
                    })
                    .catch((err) => {
                        console.error(err);
                        Swal.fire(
                            "Error al enviar la solicitud de edición de empleado",
                            "",
                            "error",
                        );
                    });
            }
        }
    });
}
function EnrollEmployeeKardex() {
    var lector = document.getElementById("devices-select-Enroll").value;
    if (!lector) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const empid = currentEmployee;
    const dedo = new Array();
    const replicar = document.getElementById("send").checked;
    const lectores = $("#devicesEnroll").select2("data");
    var fingers = [
        "#finger-thumb_1",
        "#finger-index_1",
        "#finger-middle_1",
        "#finger-ring_1",
        "#finger-pinky_1",
        "#finger-pinky_2",
        "#finger-ring_2",
        "#finger-middle_2",
        "#finger-index_2",
        "#finger-thumb_2",
    ];
    for (var i = 0; i < fingers.length; i++) {
        if (
            document.querySelector("[finger-index='" + Number(i) + "']")
                .checked == true
        ) {
            dedo.push(i);
        }
    }

    if (!empid) {
        Swal.fire("Ningún ID de empleado ingresado", "", "error");
        return;
    }

    if (dedo.length == 0) {
        Swal.fire("Por favor seleccione un dedo", "", "error");
        return;
    }

    if (replicar && lectores.length == 0) {
        Swal.fire("Seleccione un lector", "", "warning");
        return;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Inscribir empleado " +
            empid +
            " con dedo " +
            dedo +
            " en el dispositivo " +
            lector +
            "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, inscribir!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(employeeRoutes.enroll, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": employeeRoutes.csrfToken,
                },
                body: JSON.stringify({
                    sn: lector,
                    empid: empid,
                    dedo: dedo,
                    replicar: replicar,
                    lectores: lectores,
                }),
            })
                .then((r) => r.json())
                .then((data) =>
                    Swal.fire(
                        data.message || "Solicitud de inscripción enviada",
                        "",
                        "success",
                    ),
                )
                .then(() => {
                    CloseEnrollModal();
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire(
                        "Error al enviar la solicitud de inscripción",
                        "",
                        "error",
                    );
                });
        }
    });
}
function OpenDeleteEmployeeKardexModal() {
    document.getElementById("deleteEmployeeText").innerText =
        currentEmployee.substring(1, 10) + " - " + currentEmployeeName;

    var modal = new bootstrap.Modal(
        document.getElementById("DeleteEmployeeModalKardex"),
        {},
    );
    modal.show();
    $("#devicesDelete").select2({
        dropdownParent: $("#DeleteEmployeeModalKardex .modal-body"),
    });
    $("#devicesDelete").val(null).trigger("change");
    $("#devicesDelete").val("all").trigger("change");
}
function performEmployeeDeletionKardex(devices, deleteDatabase, employee) {
    $("#loader-lu").addClass("is-active");
    fetch(employeeRoutes.deleteEmployee, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": employeeRoutes.csrfToken,
        },
        body: JSON.stringify({
            devices: devices,
            empids: employee,
            deleteDatabase: deleteDatabase,
        }),
    })
        .then((r) => r.json())
        .then((data) => {
            $("#loader-lu").removeClass("is-active");
            Swal.fire(
                data.message || "Empleado enviado para eliminar",
                "",
                "success",
            );
        })
        .then(() => {
            var deleteModal = document.getElementById(
                "DeleteEmployeeModalKardex",
            );
            var modal = bootstrap.Modal.getInstance(deleteModal);
            modal.hide();
        })
        .catch((err) => {
            console.error(err);
            Swal.fire(
                "Error al enviar la solicitud de eliminacion",
                "",
                "error",
            );
            $("#loader-lu").removeClass("is-active");
        });
}

function DeleteEmployeeKardex() {
    if (!currentEmployee) {
        Swal.fire("Ningún empleado seleccionado", "", "error");
        return;
    }

    const employee = [currentEmployee];
    const deleteDatabase =
        document.getElementById("deleteFromDatabase").checked;
    const devices = $("#devicesDelete").select2("data");

    if (devices.length == 0) {
        Swal.fire("Seleccione un lector", "", "warning");
        return;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Eliminar empleado " +
            currentEmployee +
            " del dispositivo(s) seleccionado(s)?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, eliminar!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            if (deleteDatabase) {
                Swal.fire({
                    title: "¿Estás realmente seguro?",
                    text: "Esto eliminará permanentemente las hueellas y rostros del empleado seleccionado de la base de datos además del dispositivo. Esta acción no se puede deshacer.",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonText: "¡Sí, eliminar permanentemente!",
                    cancelButtonText: "No, cancelar",
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        performEmployeeDeletionKardex(
                            devices,
                            deleteDatabase,
                            employee,
                        );
                    }
                });
            } else {
                performEmployeeDeletionKardex(
                    devices,
                    deleteDatabase,
                    employee,
                );
            }
        }
    });
}

function uploadEmployeePhoto() {
    if (!currentEmployee) {
        Swal.fire("Ningún empleado seleccionado", "", "error");
        return;
    }

    const fileInput = document.getElementById("employeePhotoInput");
    const file = fileInput.files[0];

    if (!file) {
        Swal.fire("Ninguna foto seleccionada", "", "error");
        return;
    }

    if (file.type !== "image/jpeg") {
        Swal.fire(
            "Tipo de archivo inválido. Por favor seleccione una imagen JPEG.",
            "",
            "error",
        );
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const base64 = e.target.result.split(",")[1];
        const size = file.size;

        fetch(employeeRoutes.uploadPhoto, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": employeeRoutes.csrfToken,
            },
            body: JSON.stringify({
                employee_id: currentEmployee,
                base64: base64,
                size: size,
            }),
        })
            .then((r) => r.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire("Foto subida exitosamente", "", "success");
                    document.getElementById("employeePhoto").src =
                        "/storage/userpic/" + currentEmployee + ".jpg";
                } else {
                    Swal.fire(
                        data.message || "Error al subir la foto",
                        "",
                        "error",
                    );
                }
            })
            .catch((err) => {
                console.error(err);
                Swal.fire("Error al subir la foto", "", "error");
            });
    };
    reader.readAsDataURL(file);
}

Object.assign(window, {
    setCurrentEmployee,
    OpenEnrollEmployeeModal,
    EditEmployeeData,
    EnrollEmployeeKardex,
    OpenDeleteEmployeeKardexModal,
    DeleteEmployeeKardex,
    performEmployeeDeletionKardex,
    uploadEmployeePhoto,
});
