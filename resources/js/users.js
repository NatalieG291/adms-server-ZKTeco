function CloseNewUserModal() {
    var newUserModal = document.getElementById("newUserModal");
    var modal = bootstrap.Modal.getInstance(newUserModal);
    modal.hide();
}
function newUserForm() {
    document.getElementById("newUserModalTitle").textContent = "Nuevo usuario";
    document.getElementById("userAction").textContent = "Crear usuario";
    document.getElementById("userName").value = "";
    document.getElementById("userEmail").value = "";
    const listPermissions = document.querySelectorAll(
        ".dropdown-list-permissions ul li",
    );
    listPermissions.forEach((item) => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        checkbox.checked = false;
    });
}

function getUserData(email) {
    $("#loader-lu").addClass("is-active");
    fetch(usersRoutes.getUserPermissions + "?email=" + encodeURIComponent(email), {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": usersRoutes.csrfToken,
        },
    })
        .then((r) => r.json())
        .then((data) => {
            var userData = data.data;
            document.getElementById("userName").value = userData.name;
            document.getElementById("userEmail").value = userData.email;
            document.getElementById("newUserModalTitle").textContent =
                "Editar usuario";
            document.getElementById("userAction").textContent =
                "Editar usuario";
            //const permissions = JSON.parse(userData.permissions);
            const listPermissions = document.querySelectorAll(
                ".dropdown-list-permissions ul li",
            );
            listPermissions.forEach((item) => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                checkbox.checked = userData.permissions.some(
                    (p) => p.name === checkbox.name,
                );
            });
            $("#loader-lu").removeClass("is-active");
        });
}

function dropUser(email) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¿Eliminar usuario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, eliminarlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(usersRoutes.dropUser, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": usersRoutes.csrfToken,
                },
                body: JSON.stringify({ email: email }),
            })
                .then((r) => r.json())
                .then((data) => {
                    Swal.fire(
                        data.message || "Usuario eliminado",
                        "",
                        "success",
                    );
                })
                .catch((err) => {
                    Swal.fire("Error al eliminar el usuario", "", "error");
                });
        }
    });
}

function newUser() {
    const listPermissions = document.querySelectorAll(
        ".dropdown-list-permissions ul li",
    );
    const checkedPerIds = [];
    listPermissions.forEach((item) => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox && checkbox.checked) {
            checkedPerIds.push(checkbox.name);
        }
    });

    var nombre = document.getElementById("userName").value;
    var email = document.getElementById("userEmail").value;
    var pass = document.getElementById("userPasswd").value;

    Swal.fire({
        title: "¿Estás seguro?",
        text: "¿Crear nuevo usuario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "¡Sí, crearlo!",
        cancelButtonText: "No, cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $("#loader-lu").addClass("is-active");
            fetch(usersRoutes.newUser, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": usersRoutes.csrfToken,
                },
                body: JSON.stringify({
                    name: nombre,
                    email: email,
                    pass: pass,
                    perm: checkedPerIds,
                }),
            })
                .then((r) => r.json())
                .then((data) => {
                    $("#loader-lu").removeClass("is-active");
                    Swal.fire(data.message || "Usuario creado", "", "success");
                })
                .then(() => {
                    CloseNewUserModal();
                })
                .catch((err) => {
                    console.error(err);
                    Swal.fire("Error al crear el usuario", "", "error");
                    $("#loader-lu").removeClass("is-active");
                });
        }
    });
}

Object.assign(window, {
    CloseNewUserModal,
    newUserForm,
    getUserData,
    dropUser,
    newUser,
});
