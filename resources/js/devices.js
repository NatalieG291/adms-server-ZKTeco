let currentSN = null;
let currentDeviceName = null;

function setCurrentSN(sn, deviceName) {
    currentSN = sn;
    currentDeviceName = deviceName;
}

// MODALS //

function CloseEnrollModal() {
    var enrollModal = document.getElementById("enrollModal");
    var modal = bootstrap.Modal.getInstance(enrollModal);
    modal.hide();
}

function OpenEnrollModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
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
    document.getElementById("send").checked = false;
    document.getElementById("empid").value = "";
    var modal = new bootstrap.Modal(document.getElementById("enrollModal"), {});
    modal.show();
    $("#devices").select2({
        dropdownParent: $("#enrollModal .modal-body"),
    });
    $("#devices").val(null).trigger("change");
    $("#devices").val("all").trigger("change");
}

function ClosePictureModal() {
    var PictureModal = document.getElementById("pictureModal");
    var modal = bootstrap.Modal.getInstance(PictureModal);
    modal.hide();
}

function OpenPictureModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
    var modal = new bootstrap.Modal(
        document.getElementById("pictureModal"),
        {},
    );
    modal.show();
}

function CloseDuplicateTimeModal() {
    var duplicateTimeModal = document.getElementById("duplicateTimeModal");
    var modal = bootstrap.Modal.getInstance(duplicateTimeModal);
    modal.hide();
}

function OpenDuplicateTimeModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
    var modal = new bootstrap.Modal(
        document.getElementById("duplicateTimeModal"),
        {},
    );
    modal.show();
}

function CloseDownloadModal() {
    var downloadModal = document.getElementById("downloadData");
    var modal = bootstrap.Modal.getInstance(downloadModal);
    modal.hide();
}

function OpenDownloadModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
    document.getElementById("inlineRadio1").checked = true;
    document.getElementById("inlineRadio2").checked = false;
    document.getElementById("employeeSelect").classList.add("visually-hidden");
    const listEmployees = document.querySelectorAll(".dropdown-list ul li");
    listEmployees.forEach((item) => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) {
            checkbox.checked = false;
        }
    });
    var modal = new bootstrap.Modal(
        document.getElementById("downloadData"),
        {},
    );
    modal.show();
}

function CloseUploadModal() {
    var uploadModal = document.getElementById("uploadData");
    var modal = bootstrap.Modal.getInstance(uploadModal);
    modal.hide();
}

function OpenUploadModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
    document.getElementById("allEmployeesUpload").checked = true;
    document.getElementById("specificEmployeeUpload").checked = false;
    document
        .getElementById("employeeSelectUpload")
        .classList.add("visually-hidden");
    const listEmployees = document.querySelectorAll(".dropdown-list ul li");
    listEmployees.forEach((item) => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) {
            checkbox.checked = false;
        }
    });
    var modal = new bootstrap.Modal(document.getElementById("uploadData"), {});
    modal.show();
}

function CloseDeleteEmployeeModal() {
    var DeleteEmployeeModal = document.getElementById("DeleteEmployee");
    var modal = bootstrap.Modal.getInstance(DeleteEmployeeModal);
    modal.hide();
}

function OpenDeleteEmployeeModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
    const listEmployees = document.querySelectorAll(".dropdown-list ul li");
    listEmployees.forEach((item) => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) {
            checkbox.checked = false;
        }
    });
    document.getElementById("bajas").checked = true;
    document
        .getElementById("employeeSelectDelete")
        .classList.add("visually-hidden");
    document
        .getElementById("deleteDateContainer")
        .classList.remove("visually-hidden");
    var modal = new bootstrap.Modal(
        document.getElementById("DeleteEmployee"),
        {},
    );
    modal.show();
}

function CloseDeviceConfigModal() {
    var DeviceConfigModal = document.getElementById("DeviceConfig");
    var modal = bootstrap.Modal.getInstance(DeviceConfigModal);
    modal.hide();
}

function OpenDeviceConfigModal() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }
    $("#loader-lu").addClass("is-active");
    fetch(deviceRoutes.getDeviceConfig, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": deviceRoutes.csrfToken,
        },
        body: JSON.stringify({ sn: currentSN }),
    })
        .then((r) => r.json())
        .then((data) => {
            $("#loader-lu").removeClass("is-active");
            var configs = data.configs;
            document.getElementById("deviceName").value = configs.name;
            document.getElementById("timeZone").value = configs.timezone;
            document.getElementById("delay").value = configs.delay;
            document.getElementById("transfer").value = configs.realtime;
            document.getElementById("transferTime").value =
                configs.transinterval;
            document.getElementById("transtimes").value = configs.transtimes;
            if (configs.realtime == "1") {
                document.getElementById("transferTime").disabled = true;
                document.getElementById("transtimes").disabled = true;
            } else {
                document.getElementById("transferTime").disabled = false;
                document.getElementById("transtimes").disabled = false;
            }
        })
        .catch((err) => {
            console.error(err);
            Swal.fire(
                "Error al consultar la informacion del lector",
                "",
                "error",
            );
            $("#loader-lu").removeClass("is-active");
        });
    var modal = new bootstrap.Modal(
        document.getElementById("DeviceConfig"),
        {},
    );
    modal.show();
}

////////////

// DEVICE COMMANDS //

function UploadData() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const all = document.getElementById("allEmployeesUpload").checked;
    const many = document.getElementById("specificEmployeeUpload").checked;
    const fp = document.getElementById("fingerprints").checked;
    const face = document.getElementById("faces").checked;
    const photo = document.getElementById("Photos").checked;

    if (many) {
        const listEmployees = document.querySelectorAll(".dropdown-list ul li");
        const checkedEmpIds = [];
        listEmployees.forEach((item) => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                checkedEmpIds.push(checkbox.name);
            }
        });
        if (checkedEmpIds.length === 0) {
            Swal.fire("Ningún empleado seleccionado", "", "error");
            return;
        }
        Swal.fire({
            title: "¿Estás seguro?",
            text:
                "¿Subir datos para empleados seleccionados al dispositivo " +
                currentDeviceName +
                "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "¡Sí, subirlo!",
            cancelButtonText: "No, cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#loader-lu").addClass("is-active");
                fetch(deviceRoutes.upload, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                    },
                    body: JSON.stringify({
                        sn: currentSN,
                        empids: checkedEmpIds,
                        fp: fp,
                        face: face,
                        photo: photo,
                    }),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        $("#loader-lu").removeClass("is-active");
                        Swal.fire(
                            data.message || "Solicitud de subida enviada",
                            "",
                            "success",
                        );
                    })
                    .then(() => {
                        CloseUploadModal();
                    })
                    .catch((err) => {
                        console.error(err);
                        Swal.fire(
                            "Error al enviar la solicitud de subida",
                            "",
                            "error",
                        );
                        $("#loader-lu").removeClass("is-active");
                    });
            }
        });
    } else {
        Swal.fire({
            title: "¿Estás seguro?",
            text:
                "¿Subir datos para " +
                (all ? "todos los empleados" : "empleados seleccionados") +
                " al dispositivo " +
                currentDeviceName +
                "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "¡Sí, subirlo!",
            cancelButtonText: "No, cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#loader-lu").addClass("is-active");
                fetch(deviceRoutes.upload, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                    },
                    body: JSON.stringify({
                        sn: currentSN,
                        all: all,
                        fp: fp,
                        face: face,
                        photo: photo,
                    }),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        $("#loader-lu").removeClass("is-active");
                        Swal.fire(
                            data.message || "Solicitud de subida enviada",
                            "",
                            "success",
                        );
                    })
                    .then(() => {
                        CloseUploadModal();
                    })
                    .catch((err) => {
                        console.error(err);
                        Swal.fire(
                            "Error al enviar la solicitud de subida",
                            "",
                            "error",
                        );
                        $("#loader-lu").removeClass("is-active");
                    });
            }
        });
    }
}
function DownloadData() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const all = document.getElementById("inlineRadio1").checked;
    const many = document.getElementById("inlineRadio2").checked;

    if (many) {
        const listEmployees = document.querySelectorAll(".dropdown-list ul li");
        const checkedEmpIds = [];
        listEmployees.forEach((item) => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                checkedEmpIds.push(checkbox.name);
            }
        });
        if (checkedEmpIds.length === 0) {
            Swal.fire("Ningún empleado seleccionado", "", "error");
            return;
        }
        Swal.fire({
            title: "¿Estás seguro?",
            text:
                "¿Descargar datos para empleados seleccionados del dispositivo " +
                currentDeviceName +
                "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "¡Sí, descargarlo!",
            cancelButtonText: "No, cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#loader-lu").addClass("is-active");
                fetch(deviceRoutes.download, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                    },
                    body: JSON.stringify({
                        sn: currentSN,
                        empids: checkedEmpIds,
                    }),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        $("#loader-lu").removeClass("is-active");
                        Swal.fire(
                            data.message || "Solicitud de descarga enviada",
                            "",
                            "success",
                        );
                    })
                    .then(() => {
                        CloseDownloadModal();
                    })
                    .catch((err) => {
                        console.error(err);
                        Swal.fire(
                            "Error al enviar la solicitud de descarga",
                            "",
                            "error",
                        );
                        $("#loader-lu").removeClass("is-active");
                    });
            }
        });
    } else {
        Swal.fire({
            title: "¿Estás seguro?",
            text:
                "¿Descargar datos para " +
                (all ? "todos los empleados" : "empleados seleccionados") +
                " del dispositivo " +
                currentDeviceName +
                "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "¡Sí, descargarlo!",
            cancelButtonText: "No, cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#loader-lu").addClass("is-active");
                fetch(deviceRoutes.download, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                    },
                    body: JSON.stringify({ sn: currentSN, all: all }),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        $("#loader-lu").removeClass("is-active");
                        Swal.fire(
                            data.message || "Solicitud de descarga enviada",
                            "",
                            "success",
                        );
                    })
                    .then(() => {
                        CloseDownloadModal();
                    })
                    .catch((err) => {
                        console.error(err);
                        Swal.fire(
                            "Error al enviar la solicitud de descarga",
                            "",
                            "error",
                        );
                    });
            }
        });
    }
}

function DeleteData() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Borrar todos los datos en el dispositivo " +
            currentDeviceName +
            "? Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, borrarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Segunda confirmación",
                text: "Esto borrará permanentemente todos los datos en el dispositivo. ¿Estás absolutamente seguro?",
                icon: "error",
                showCancelButton: true,
                confirmButtonText: "¡Sí, borrar permanentemente!",
                cancelButtonText: "No, cancelar",
            }).then((secondResult) => {
                if (secondResult.isConfirmed) {
                    fetch(deviceRoutes.deleteData, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                        },
                        body: JSON.stringify({ sn: currentSN }),
                    })
                        .then((r) => r.json())
                        .then((data) =>
                            Swal.fire(
                                data.message || "Solicitud de borrado enviada",
                                "",
                                "success",
                            ),
                        )
                        .catch((err) => {
                            console.error(err);
                            Swal.fire(
                                "Error al enviar la solicitud de borrado",
                                "",
                                "error",
                            );
                        });
                } else {
                    Swal.fire("Acción de borrado cancelada", "", "info");
                }
            });
        }
    });
}
function EnrollEmployee() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const empid = document.getElementById("empid").value;
    const dedo = new Array();
    const replicar = document.getElementById("send").checked;
    const lectores = $("#devices").select2("data");
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
            currentDeviceName +
            "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, inscribir!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deviceRoutes.enroll, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({
                    sn: currentSN,
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

function setDuplicateTime() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const minutes = document.getElementById("duplicateTime").value;

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Establecer período de acceso duplicado a " +
            minutes +
            " minutos en el dispositivo " +
            currentDeviceName +
            "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, configurarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deviceRoutes.setDuplicateTime, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({ sn: currentSN, minutes: minutes }),
            })
                .then((r) => r.json())
                .then((data) =>
                    Swal.fire(
                        data.message ||
                            "Solicitud de período de acceso duplicado enviada",
                        "",
                        "success",
                    ),
                )
                .then(() => {
                    CloseDuplicateTimeModal();
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire(
                        "Error al enviar la solicitud de período de acceso duplicado",
                        "",
                        "error",
                    );
                });
        }
    });
}
function RestartDevice() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Reiniciar dispositivo " + currentDeviceName + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, reiniciarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deviceRoutes.restart, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({ sn: currentSN }),
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data && data.message) {
                        Swal.fire(data.message, "", "success");
                    } else {
                        Swal.fire(
                            "Solicitud de reinicio enviada",
                            "",
                            "success",
                        );
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    Swal.fire(
                        "Error al enviar la solicitud de reinicio",
                        "",
                        "error",
                    );
                });
        }
    });
}
function ClearAdmin() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Borrar datos de administrador en el dispositivo " +
            currentDeviceName +
            "? Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, borrarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deviceRoutes.clearAdmin, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({ sn: currentSN }),
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data && data.message) {
                        Swal.fire(data.message, "", "success");
                    } else {
                        Swal.fire(
                            "Solicitud de borrado de administrador enviada",
                            "",
                            "success",
                        );
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    Swal.fire(
                        "Error al enviar la solicitud de borrado de administrador",
                        "",
                        "error",
                    );
                });
        }
    });
}
function ClearLog() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Borrar registro en el dispositivo " +
            currentDeviceName +
            "? Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, borrarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deviceRoutes.clearLog, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({ sn: currentSN }),
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data && data.message) {
                        Swal.fire(data.message, "", "success");
                    } else {
                        Swal.fire(
                            "Solicitud de borrado de registro enviada",
                            "",
                            "success",
                        );
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    Swal.fire(
                        "Error al enviar la solicitud de borrado de registro",
                        "",
                        "error",
                    );
                });
        }
    });
}
function performEmployeeDeletion(
    empIds,
    deleteDatabase,
    baja,
    especifico,
    inexistente,
    todos,
    fbaja,
) {
    $("#loader-lu").addClass("is-active");
    fetch(deviceRoutes.deleteEmployee, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": deviceRoutes.csrfToken,
        },
        body: JSON.stringify({
            sn: currentSN,
            empids: empIds,
            deleteDatabase: deleteDatabase,
            baja: baja,
            especifico: especifico,
            inexistente: inexistente,
            todos: todos,
            fbaja: fbaja,
        }),
    })
        .then((r) => r.json())
        .then((data) => {
            $("#loader-lu").removeClass("is-active");
            Swal.fire(
                data.message || "Empleados enviados para eliminar",
                "",
                "success",
            );
        })
        .then(() => {
            CloseDeleteEmployeeModal();
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

function EmployeeDeleteData() {
    const baja = document.getElementById("bajas").checked;
    const fbaja = document.getElementById("deleteDate").value;
    const especifico = document.getElementById("especifico").checked;
    const inexistente = document.getElementById("inexistentes").checked;
    const todos = document.getElementById("allDevices").checked;
    var msg = "";
    if (baja) {
        if (fbaja == "") {
            Swal.fire("Ingrese una fecha de baja", "", "error");
            return;
        }
        if (todos) {
            msg =
                "¿Eliminar empleados dados de baja de todos los dispositivos?";
        } else {
            msg =
                "¿Eliminar empleados dados de baja del dispositivo " +
                currentDeviceName +
                "?";
        }
    } else if (especifico) {
        if (todos) {
            msg =
                "¿Eliminar empleados seleccionados de todos los dispositivos?";
        } else {
            msg =
                "¿Eliminar empleados seleccionados del dispositivo " +
                currentDeviceName +
                "?";
        }
    } else if (inexistente) {
        if (todos) {
            msg = "¿Eliminar empleados inexistentes de todos los dispositivos?";
        } else {
            msg =
                "¿Eliminar empleados inexistentes del dispositivo " +
                currentDeviceName +
                "?";
        }
    }

    const listEmployees = document.querySelectorAll(".dropdown-list ul li");
    const deleteDatabase = document.getElementById("deleteEmployees").checked;
    const checkedEmpIds = [];
    listEmployees.forEach((item) => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) {
            checkedEmpIds.push(checkbox.name);
        }
    });
    if (especifico) {
        if (checkedEmpIds.length === 0) {
            Swal.fire("Ningún empleado seleccionado", "", "error");
            return;
        }
    }
    Swal.fire({
        title: "¿Estás seguro?",
        text: msg,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, eliminarlos!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            if (deleteDatabase) {
                Swal.fire({
                    title: "¿Estás realmente seguro?",
                    text: "Esto eliminará permanentemente huellas y rostros de los empleados seleccionados de la base de datos además del dispositivo. Esta acción no se puede deshacer.",
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonText: "¡Sí, eliminar permanentemente!",
                    cancelButtonText: "No, cancelar",
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        performEmployeeDeletion(
                            checkedEmpIds,
                            deleteDatabase,
                            baja,
                            especifico,
                            inexistente,
                            todos,
                            fbaja,
                        );
                    }
                });
            } else {
                performEmployeeDeletion(
                    checkedEmpIds,
                    deleteDatabase,
                    baja,
                    especifico,
                    inexistente,
                    todos,
                    fbaja,
                );
            }
        }
    });
}
function SaveDeviceConfig() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const name = document.getElementById("deviceName").value;
    const timezone = document.getElementById("timeZone").value;
    const delay = document.getElementById("delay").value;
    const realtime = document.getElementById("transfer").value;
    const transfertime = document.getElementById("transferTime").value;
    const transtimes = document.getElementById("transtimes").value;

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Guardar la configuracion del dispositivo " +
            currentDeviceName +
            "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, guardar!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $("#loader-lu").addClass("is-active");
            fetch(deviceRoutes.saveDeviceConfig, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({
                    sn: currentSN,
                    name: name,
                    timezone: timezone,
                    delay: delay,
                    realtime: realtime,
                    transfertime: transfertime,
                    transtimes: transtimes,
                }),
            })
                .then((r) => r.json())
                .then((data) => {
                    $("#loader-lu").removeClass("is-active");
                    Swal.fire(
                        data.message || "Configuraciones guardadas",
                        "",
                        "success",
                    );
                })
                .then(() => {
                    CloseDeviceConfigModal();
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire(
                        "Error al guardar las configuraciones",
                        "",
                        "error",
                    );
                    $("#loader-lu").removeClass("is-active");
                });
        }
    });
}
function SetPhotoConfig() {
    if (!currentSN) {
        Swal.fire("Ningún dispositivo seleccionado", "", "error");
        return;
    }

    const config = document.getElementById("photoConfig").value;

    Swal.fire({
        title: "¿Estás seguro?",
        text:
            "¿Establecer configuración de foto a " +
            config +
            " en el dispositivo " +
            currentDeviceName +
            "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, configurarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(deviceRoutes.setPhotoConfig, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": deviceRoutes.csrfToken,
                },
                body: JSON.stringify({ sn: currentSN, config: config }),
            })
                .then((r) => r.json())
                .then((data) =>
                    Swal.fire(
                        data.message ||
                            "Solicitud de configuración de foto enviada",
                        "",
                        "success",
                    ),
                )
                .then(() => {
                    ClosePictureModal();
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire(
                        "Error al enviar la solicitud de configuración de foto",
                        "",
                        "error",
                    );
                });
        }
    });
}

Object.assign(window, {
    setCurrentSN,
    CloseEnrollModal,
    OpenEnrollModal,
    ClosePictureModal,
    OpenPictureModal,
    CloseDuplicateTimeModal,
    OpenDuplicateTimeModal,
    CloseDownloadModal,
    OpenDownloadModal,
    CloseUploadModal,
    OpenUploadModal,
    CloseDeleteEmployeeModal,
    OpenDeleteEmployeeModal,
    CloseDeviceConfigModal,
    OpenDeviceConfigModal,
    UploadData,
    DownloadData,
    DeleteData,
    EnrollEmployee,
    setDuplicateTime,
    RestartDevice,
    ClearAdmin,
    ClearLog,
    EmployeeDeleteData,
    SaveDeviceConfig,
    SetPhotoConfig,
});
/////////////////////////
