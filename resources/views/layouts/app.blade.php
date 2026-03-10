<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMS Server</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" fetchpriority="high">
    <script>
        function navActive() {
            const pathname = window.location.pathname;
            const page = pathname.replace("/", "");
            if(page == "devices"){
                document.getElementById('devicesnav').classList.add('active');
            }
            else{
                document.getElementById(page).classList.add('active');
            }
        }
    </script>
    <style>
        thead th {
            position: sticky;
            top: 0;
            background: #fff !important;
        }
        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }
        a{
            cursor: pointer !important;
        }
        .dropdown-item:active{
            background-color: gray !important;
            color: white !important;
        }
        .loader{
            color:#fff;
            position:fixed;
            box-sizing:border-box;
            left:-9999px;
            top:-9999px;
            width:0;
            height:0;
            overflow:hidden;
            z-index:999999
        }
        .loader:after,
        .loader:before{
            box-sizing:border-box;
            display:none
        }
        .loader.is-active{
            background-color:rgba(0,0,0,.6);
            width:100%;
            height:100%;
            left:0;top:0
        }
        .loader.is-active:after,.loader.is-active:before{
            display:block
        }
        @keyframes rotation{
            0%{
                transform:rotate(0)
            }
            to{
                transform:rotate(359deg)
            }
        }
        @keyframes blink{
            0%{
                opacity:.5
            }
            to{
                opacity:1
            }
        }
        .loader[data-text]:before{position:fixed;left:0;top:50%;color:currentColor;font-family:Helvetica,Arial,sans-serif;text-align:center;width:100%;font-size:14px}.loader[data-text=""]:before{content:"Loading"}.loader[data-text]:not([data-text=""]):before{content:attr(data-text)}.loader[data-text][data-blink]:before{animation:blink 1s linear infinite alternate}.loader-default[data-text]:before{top:calc(50% - 63px)}.loader-default:after{content:"";position:fixed;width:48px;height:48px;border:8px solid #fff;border-left-color:transparent;border-radius:50%;top:calc(50% - 24px);left:calc(50% - 24px);animation:rotation 1s linear infinite}.loader-default[data-half]:after{border-right-color:transparent}.loader-default[data-inverse]:after{animation-direction:reverse}.loader-double:after,.loader-double:before{content:"";position:fixed;border-radius:50%;border:8px solid;animation:rotation 1s linear infinite}.loader-double:after{width:48px;height:48px;border-color:#fff;border-left-color:transparent;top:calc(50% - 24px);left:calc(50% - 24px)}.loader-double:before{width:64px;height:64px;border-color:#0098d8;border-right-color:transparent;animation-duration:2s;top:calc(50% - 32px);left:calc(50% - 32px)}.loader-bar[data-text]:before{top:calc(50% - 40px);color:#fff}.loader-bar:after{content:"";position:fixed;top:50%;left:50%;width:200px;height:20px;transform:translate(-50%,-50%);background:linear-gradient(-45deg,#4183d7 25%,#52b3d9 0,#52b3d9 50%,#4183d7 0,#4183d7 75%,#52b3d9 0,#52b3d9);background-size:20px 20px;box-shadow:inset 0 10px 0 hsla(0,0%,100%,.2),0 0 0 5px rgba(0,0,0,.2);animation:moveBar 1.5s linear infinite reverse}.loader-bar[data-rounded]:after{border-radius:15px}.loader-bar[data-inverse]:after{animation-direction:normal}@keyframes moveBar{0%{background-position:0 0}to{background-position:20px 20px}}.loader-bar-ping-pong:before{width:200px;background-color:#000}.loader-bar-ping-pong:after,.loader-bar-ping-pong:before{content:"";height:20px;position:absolute;top:calc(50% - 10px);left:calc(50% - 100px)}.loader-bar-ping-pong:after{width:50px;background-color:#f19;animation:moveBarPingPong .5s linear infinite alternate}.loader-bar-ping-pong[data-rounded]:before{border-radius:10px}.loader-bar-ping-pong[data-rounded]:after{border-radius:50%;width:20px;animation-name:moveBarPingPongRounded}@keyframes moveBarPingPong{0%{left:calc(50% - 100px)}to{left:calc(50% - -50px)}}@keyframes moveBarPingPongRounded{0%{left:calc(50% - 100px)}to{left:calc(50% - -80px)}}@keyframes corners{6%{width:60px;height:15px}25%{width:15px;height:15px;left:calc(100% - 15px);top:0}31%{height:60px}50%{height:15px;top:calc(100% - 15px);left:calc(100% - 15px)}56%{width:60px}75%{width:15px;left:0;top:calc(100% - 15px)}81%{height:60px}}.loader-border[data-text]:before{color:#fff}.loader-border:after{content:"";position:absolute;top:0;left:0;width:15px;height:15px;background-color:#ff0;animation:corners 3s ease both infinite}.loader-ball:before{content:"";position:absolute;width:50px;height:50px;top:50%;left:50%;margin:-25px 0 0 -25px;background-color:#fff;border-radius:50%;z-index:1;animation:kickBall 1s infinite alternate ease-in both}.loader-ball[data-shadow]:before{box-shadow:inset -5px -5px 10px 0 rgba(0,0,0,.5)}.loader-ball:after{content:"";position:absolute;background-color:rgba(0,0,0,.3);border-radius:50%;width:45px;height:20px;top:calc(50% + 10px);left:50%;margin:0 0 0 -22.5px;z-index:0;animation:shadow 1s infinite alternate ease-out both}@keyframes shadow{0%{background-color:transparent;transform:scale(0)}40%{background-color:transparent;transform:scale(0)}95%{background-color:rgba(0,0,0,.75);transform:scale(1)}to{background-color:rgba(0,0,0,.75);transform:scale(1)}}@keyframes kickBall{0%{transform:translateY(-80px) scaleX(.95)}90%{border-radius:50%}to{transform:translateY(0) scaleX(1);border-radius:50% 50% 20% 20%}}.loader-smartphone:after{content:"";color:#fff;font-size:12px;font-family:Helvetica,Arial,sans-serif;text-align:center;line-height:120px;position:fixed;left:50%;top:50%;width:70px;height:130px;margin:-65px 0 0 -35px;border:5px solid #fd0;border-radius:10px;box-shadow:inset 0 5px 0 0 #fd0;background:radial-gradient(circle at 50% 90%,rgba(0,0,0,.5) 6px,transparent 0),linear-gradient(0deg,#fd0 22px,transparent 0),linear-gradient(0deg,rgba(0,0,0,.5) 22px,rgba(0,0,0,.5));animation:shake 2s cubic-bezier(.36,.07,.19,.97) both infinite}.loader-smartphone[data-screen=""]:after{content:"Loading"}.loader-smartphone:not([data-screen=""]):after{content:attr(data-screen)}@keyframes shake{5%{transform:translate3d(-1px,0,0)}10%{transform:translate3d(1px,0,0)}15%{transform:translate3d(-1px,0,0)}20%{transform:translate3d(1px,0,0)}25%{transform:translate3d(-1px,0,0)}30%{transform:translate3d(1px,0,0)}35%{transform:translate3d(-1px,0,0)}40%{transform:translate3d(1px,0,0)}45%{transform:translate3d(-1px,0,0)}50%{transform:translate3d(1px,0,0)}55%{transform:translate3d(-1px,0,0)}}.loader-clock:before{width:120px;height:120px;border-radius:50%;margin:-60px 0 0 -60px;background:linear-gradient(180deg,transparent 50%,#f5f5f5 0),linear-gradient(90deg,transparent 55px,#2ecc71 0,#2ecc71 65px,transparent 0),linear-gradient(180deg,#f5f5f5 50%,#f5f5f5 0);box-shadow:inset 0 0 0 10px #f5f5f5,0 0 0 5px #555,0 0 0 10px #7b7b7b;animation:rotation infinite 2s linear}.loader-clock:after,.loader-clock:before{content:"";position:fixed;left:50%;top:50%;overflow:hidden}.loader-clock:after{width:60px;height:40px;margin:-20px 0 0 -15px;border-radius:20px 0 0 20px;background:radial-gradient(circle at 14px 20px,#25a25a 10px,transparent 0),radial-gradient(circle at 14px 20px,#1b7943 14px,transparent 0),linear-gradient(180deg,transparent 15px,#2ecc71 0,#2ecc71 25px,transparent 0);animation:rotation infinite 24s linear;transform-origin:15px center}.loader-curtain:after,.loader-curtain:before{position:fixed;width:100%;top:50%;margin-top:-35px;font-size:70px;text-align:center;font-family:Helvetica,Arial,sans-serif;overflow:hidden;line-height:1.2;content:"Loading"}.loader-curtain:before{color:#666}.loader-curtain:after{color:#fff;height:0;animation:curtain 1s linear infinite alternate both}.loader-curtain[data-curtain-text]:not([data-curtain-text=""]):after,.loader-curtain[data-curtain-text]:not([data-curtain-text=""]):before{content:attr(data-curtain-text)}.loader-curtain[data-brazilian]:before{color:#f1c40f}.loader-curtain[data-brazilian]:after{color:#2ecc71}.loader-curtain[data-colorful]:before{animation:maskColorful 2s linear infinite alternate both}.loader-curtain[data-colorful]:after{animation:curtain 1s linear infinite alternate both,maskColorful-front 2s 1s linear infinite alternate both;color:#000}@keyframes maskColorful{0%{color:#3498db}49.5%{color:#3498db}50.5%{color:#e74c3c}to{color:#e74c3c}}@keyframes maskColorful-front{0%{color:#2ecc71}49.5%{color:#2ecc71}50.5%{color:#f1c40f}to{color:#f1c40f}}@keyframes curtain{0%{height:0}to{height:84px}}.loader-music:after,.loader-music:before{content:"";position:fixed;width:240px;height:240px;top:50%;left:50%;margin:-120px 0 0 -120px;border-radius:50%;text-align:center;line-height:240px;color:#fff;font-size:40px;font-family:Helvetica,Arial,sans-serif;text-shadow:1px 1px 0 rgba(0,0,0,.5);letter-spacing:-1px}.loader-music:after{backface-visibility:hidden}.loader-music[data-hey-oh]:after,.loader-music[data-hey-oh]:before{box-shadow:0 0 0 10px}.loader-music[data-hey-oh]:before{background-color:#fff;color:#000;animation:coinBack 2.5s linear infinite,oh 5s 1.25s linear infinite both}.loader-music[data-hey-oh]:after{background-color:#000;animation:coin 2.5s linear infinite,hey 5s linear infinite both}.loader-music[data-no-cry]:after,.loader-music[data-no-cry]:before{background:linear-gradient(45deg,#009b3a 50%,#fed100 51%);box-shadow:0 0 0 10px #000}.loader-music[data-no-cry]:before{animation:coinBack 2.5s linear infinite,cry 5s 1.25s linear infinite both}.loader-music[data-no-cry]:after{animation:coin 2.5s linear infinite,no 5s linear infinite both}.loader-music[data-we-are]:before{animation:coinBack 2.5s linear infinite,theWorld 5s 1.25s linear infinite both;background:radial-gradient(ellipse at center,#4ecdc4 0,#556270)}.loader-music[data-we-are]:after{animation:coin 2.5s linear infinite,weAre 5s linear infinite both;background:radial-gradient(ellipse at center,#26d0ce 0,#1a2980)}.loader-music[data-rock-you]:before{animation:coinBack 2.5s linear infinite,rockYou 5s 1.25s linear infinite both;background:#444}.loader-music[data-rock-you]:after{animation:coin 2.5s linear infinite,weWill 5s linear infinite both;background:#96281b}@keyframes coin{to{transform:rotateY(359deg)}}@keyframes coinBack{0%{transform:rotateY(180deg)}50%{transform:rotateY(1turn)}to{transform:rotateY(180deg)}}@keyframes hey{0%{content:"Hey!"}50%{content:"Let's!"}to{content:"Hey!"}}@keyframes oh{0%{content:"Oh!"}50%{content:"Go!"}to{content:"Oh!"}}@keyframes no{0%{content:"No..."}50%{content:"no"}to{content:"No..."}}@keyframes cry{0%{content:"woman"}50%{content:"cry!"}to{content:"woman"}}@keyframes weAre{0%{content:"We are"}50%{content:"we are"}to{content:"We are"}}@keyframes theWorld{0%{content:"the world,"}50%{content:"the children!"}to{content:"the world,"}}@keyframes weWill{0%{content:"We will,"}50%{content:"rock you!"}to{content:"We will,"}}@keyframes rockYou{0%{content:"we will"}50%{content:"\1F918"}to{content:"we will"}}.loader-pokeball:before{content:"";position:absolute;width:100px;height:100px;top:50%;left:50%;margin:-50px 0 0 -50px;background:linear-gradient(180deg,red 42%,#000 0,#000 58%,#fff 0);background-repeat:no-repeat;background-color:#fff;border-radius:50%;z-index:1;animation:movePokeball 1s linear infinite both}.loader-pokeball:after{content:"";position:absolute;width:24px;height:24px;top:50%;left:50%;margin:-12px 0 0 -12px;background-color:#fff;border-radius:50%;z-index:2;animation:movePokeball 1s linear infinite both,flashPokeball .5s infinite alternate;border:2px solid #000;box-shadow:0 0 0 5px #fff,0 0 0 10px #000}@keyframes movePokeball{0%{transform:translateX(0) rotate(0)}15%{transform:translatex(-10px) rotate(-5deg)}30%{transform:translateX(10px) rotate(5deg)}45%{transform:translatex(0) rotate(0)}}@keyframes flashPokeball{0%{background-color:#fff}to{background-color:#fd0}}.loader-bouncing:after,.loader-bouncing:before{content:"";width:20px;height:20px;position:absolute;top:calc(50% - 10px);left:calc(50% - 10px);border-radius:50%;background-color:#fff;animation:kick .6s infinite alternate}.loader-bouncing:after{margin-left:-30px;animation:kick .6s infinite alternate}.loader-bouncing:before{animation-delay:.2s}@keyframes kick{0%{opacity:1;transform:translateY(0)}to{opacity:.3;transform:translateY(-1rem)}}



.noselect {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none; }

.dropdown-container {
  width: 100%;
  margin: auto 0;
  font-size: 14px;
  font-family: sans-serif;
  overflow: auto;
  border-radius: 5px;
  -webkit-box-shadow: 0px 10px 30px -4px rgba(0, 0, 0, 0.15);
  -moz-box-shadow: 0px 10px 30px -4px rgba(0, 0, 0, 0.15);
  box-shadow: 0px 10px 30px -4px rgba(0, 0, 0, 0.15); }

.dropdown-button {
  float: left;
  width: 100%;
  background: #fff;
  padding: 15px 20px;
  cursor: pointer;
  border: none;
  -webkit-box-sizing: border-box;
  box-sizing: border-box; }
  .dropdown-button .dropdown-label, .dropdown-button .dropdown-quantity {
    float: left;
    color: gray;
    font-weight: 700; }
  .dropdown-button .dropdown-quantity {
    margin-left: 4px;
    color: #0b5ed7; }
  .dropdown-button .fa {
    margin-top: 3px;
    float: right;
    font-size: 16px;
    color: #0b5ed7; }

.dropdown-list {
  float: left;
  width: 100%;
  border-top: none;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  padding: 10px 20px;
  background: #fff; }

  .dropdown-list-upload {
  float: left;
  width: 100%;
  border-top: none;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  padding: 10px 20px;
  background: #fff; }
  
  .dropdown-list-permissions {
  float: left;
  width: 100%;
  border-top: none;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  padding: 10px 20px;
  background: #fff; }
  .dropdown-list input[type="search"] {
    padding: 5px 10px;
    width: 100%;
    border: none;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.05); }

.dropdown-list-upload input[type="search"] {
    padding: 5px 10px;
    width: 100%;
    border: none;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.05); }
.dropdown-list-permissions input[type="search"] {
    padding: 5px 10px;
    width: 100%;
    border: none;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.05); }
    .dropdown-list input[type="search"]:focus {
      -webkit-box-shadow: none;
      box-shadow: none;
      outline: none; }

    .dropdown-list-upload input[type="search"]:focus {
      -webkit-box-shadow: none;
      box-shadow: none;
      outline: none; }

    .dropdown-list-permissions input[type="search"]:focus {
      -webkit-box-shadow: none;
      box-shadow: none;
      outline: none; }
  .dropdown-list ul {
    margin: 20px 0 0 0;
    max-height: 200px;
    overflow-y: auto;
    padding: 0; }
    .dropdown-list ul input[type="checkbox"] {
      position: relative;
      top: 2px; }
    .dropdown-list ul li {
      list-style: none; }

        .dropdown-list-upload ul {
    margin: 20px 0 0 0;
    max-height: 200px;
    overflow-y: auto;
    padding: 0; }
    .dropdown-list-upload ul input[type="checkbox"] {
      position: relative;
      top: 2px; }
    .dropdown-list-upload ul li {
      list-style: none; }

        .dropdown-list-permissions ul {
    margin: 20px 0 0 0;
    max-height: 200px;
    overflow-y: auto;
    padding: 0; }
    .dropdown-list-permissions ul input[type="checkbox"] {
      position: relative;
      top: 2px; }
    .dropdown-list-permissions ul li {
      list-style: none; }

.checkbox-wrap {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none; }

/* Hide the browser's default checkbox */
.checkbox-wrap input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0; }

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0; }

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "\f0c8";
  font-family: "FontAwesome";
  position: absolute;
  color: rgba(0, 0, 0, 0.1);
  font-size: 20px;
  margin-top: -4px;
  -webkit-transition: 0.3s;
  -o-transition: 0.3s;
  transition: 0.3s; }
  @media (prefers-reduced-motion: reduce) {
    .checkmark:after {
      -webkit-transition: none;
      -o-transition: none;
      transition: none; } }

/* Show the checkmark when checked */
.checkbox-wrap input:checked ~ .checkmark:after {
  display: block;
  content: "\f14a";
  font-family: "FontAwesome";
  color: #0b5ed7;
  border: none; }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                position: fixed;
                top: 56px; /* Adjust this value based on your navbar height */
                left: -100%;
                padding-left: 15px;
                padding-right: 15px;
                padding-bottom: 15px;
                width: 75%;
                height: 100%;
                background-color: #f8f9fa;
                transition: all 0.3s ease-in-out;
                z-index: 1000;
            }

            .navbar-collapse.show {
                left: 0;
            }

            body.menu-open {
                overflow: hidden;
            }

            .navbar-toggler {
                z-index: 1001;
            }
        }
    </style>
</head>
<body onload="navActive()">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="storage/logo_ossc.png" height="25vw" width="auto" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" id="devicesnav" href="{{ route('devices.index') }}">Dispositivo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="attendance" href="{{ route('devices.Attendance') }}">Asistencia</a>
                    </li>
                    @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" id="devices-log" href="{{ route('devices.DeviceLog') }}">Registro del Dispositivo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="finger-log" href="{{ route('devices.FingerLog') }}">Registro de Huella</a>
                    </li>
                    @endrole
                    <li class="nav-item">
                        <a class="nav-link" id="attphoto" href="{{ route('devices.AttPhoto') }}">Foto de Asistencia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="employees" href="{{ route('employees.index') }}">Empleados</a>
                    </li>
                    @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" id="users" href="{{ route('users.index') }}">Usuarios</a>
                    </li>
                    @endrole
                </ul>
            </div>
            <span class="navbar-text d-none d-lg-block">
                {{ now() }}
            </span>

            <ul class="navbar-nav ms-3">
                @auth
                    <li class="nav-item">
                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Salir</button>
                        </form>
                    </li>
                @endauth
                @guest
                    <li class="nav-item">
                        <a class="btn btn-primary" href="{{ url('/login') }}">Iniciar Sesión</a>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.navbar-toggler').on('click', function() {
                $('body').toggleClass('menu-open');
            });

            $('.nav-link').on('click', function() {
                if ($(window).width() < 992) {
                    $('.navbar-collapse').removeClass('show');
                    $('body').removeClass('menu-open');
                }
            });
        });
    </script>
    <script>
        function RestartDevice() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Reiniciar dispositivo ' + currentSN + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, reiniciarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('devices.restart') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sn: currentSN })
                    })
                    .then(function(response){ return response.json(); })
                    .then(function(data){
                        if (data && data.message) {
                            Swal.fire(data.message, '', 'success');
                        } else {
                            Swal.fire('Solicitud de reinicio enviada', '', 'success');
                        }
                    })
                    .catch(function(err){
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de reinicio', '', 'error');
                    });
                }
            });
        }
    </script>
    <script>
        function ClearAdmin() {
        
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            Swal.fire ({
                title: '¿Estás seguro?',
                text: '¿Borrar datos de administrador en el dispositivo ' + currentSN + '? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, borrarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('devices.clear-admin') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sn: currentSN })
                    })
                    .then(function(response){ return response.json(); })
                    .then(function(data){
                        if (data && data.message) {
                            Swal.fire(data.message, '', 'success');
                        } else {
                            Swal.fire('Solicitud de borrado de administrador enviada', '', 'success');
                        }
                    })
                    .catch(function(err){
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de borrado de administrador', '', 'error');
                    });
                }
            });   
        }
    </script>
    <script>
        function ClearLog() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Borrar registro en el dispositivo ' + currentSN + '? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, borrarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('devices.clear-log') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sn: currentSN })
                    })
                    .then(function(response){ return response.json(); })
                    .then(function(data){
                        if (data && data.message) {
                            Swal.fire(data.message, '', 'success');
                        } else {
                            Swal.fire('Solicitud de borrado de registro enviada', '', 'success');
                        }
                    })
                    .catch(function(err){
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de borrado de registro', '', 'error');
                    });
                }
            });
        }
    </script>
    <script>
        let currentSN = null;

        function setCurrentSN(sn) {
            currentSN = sn;
        }
    </script>
    <script>
        let currentEmployee = null;

        function setCurrentEmployee(empid,name,pri,pri_id,passwd,card,verify) {
            currentEmployee = empid;
            document.getElementById('employeeName').value = name;
            document.getElementById('employeePri').value = pri_id;
            document.getElementById('employeePasswd').value = passwd;
            document.getElementById('employeeCard').value = card;
            document.getElementById('employeeVerify').value = verify;
            document.getElementById('employeePhoto').src = '/storage/userpic/' + empid + '.jpg';
        }
    </script>
    <script>
        function EnrollEmployee() {

            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            const empid = document.getElementById('empid').value;
            const dedo = document.getElementById('dedo').value;

            if (!empid) {
                Swal.fire('Ningún ID de empleado ingresado', '', 'error');
                return;
            }

            if (!dedo) {
                Swal.fire('Por favor seleccione un dedo', '', 'error');
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Inscribir empleado ' + empid + ' con dedo ' + dedo + ' en el dispositivo ' + currentSN + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, inscribir!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('devices.enroll') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sn: currentSN, empid: empid, dedo: dedo })
                    })
                    .then(r => r.json())
                    .then(data => Swal.fire(data.message || "Solicitud de inscripción enviada", '', 'success'))
                    .then(() => {
                        CloseEnrollModal();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de inscripción', '', 'error');
                    });
                }
            });
        }
    </script>
    <script>
        function SetPhotoConfig() {

            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            const config = document.getElementById('photoConfig').value;

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Establecer configuración de foto a ' + config + ' en el dispositivo ' + currentSN + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, configurarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('devices.set-photo-config') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sn: currentSN, config: config })
                    })
                    .then(r => r.json())
                    .then(data => Swal.fire(data.message || "Solicitud de configuración de foto enviada", '', 'success'))
                    .then(() => {
                        ClosePictureModal();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de configuración de foto', '', 'error');
                    });
                }
            });
        }
    </script>
    <script>
        function setDuplicateTime() {

            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            const minutes = document.getElementById('duplicateTime').value;

            // if (!confirm('Set duplicate punch period to ' + minutes + ' minutes on device ' + currentSN + '?')) return;
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Establecer período de acceso duplicado a ' + minutes + ' minutos en el dispositivo ' + currentSN + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, configurarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('devices.set-duplicate-time') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sn: currentSN, minutes: minutes })
                    })
                    .then(r => r.json())
                    .then(data => Swal.fire(data.message || "Solicitud de período de acceso duplicado enviada", '', 'success'))
                    .then(() => {
                        CloseDuplicateTimeModal();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de período de acceso duplicado', '', 'error');
                    });
                }
            });
        }
    </script>
    <script>
        function EditEmployeeData() {

            if (!currentEmployee) {
                Swal.fire('Ningún empleado seleccionado', '', 'error');
                return;
            }

            const name = document.getElementById('employeeName').value;
            const pri = document.getElementById('employeePri').value;
            const passwd = document.getElementById('employeePasswd').value;
            const card = document.getElementById('employeeCard').value;
            const verify = document.getElementById('employeeVerify').value;
            const send = document.getElementById('send').checked;
            const devices = document.getElementById('devices').value;

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Guardar cambios en el empleado ' + currentEmployee + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, guardarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('employee.EditEmployeeData') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ empid: currentEmployee, name: name, pri: pri, passwd: passwd, card: card, verify: verify, send: send, devices: devices })
                    })
                    .then(r => r.json())
                    .then(data => Swal.fire(data.message || "Solicitud de edición de empleado enviada", '', 'success'))
                    .then(() => {
                        location.reload();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error al enviar la solicitud de edición de empleado', '', 'error');
                    });
                }
            });
        }

        function SaveDeviceConfig(){
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            name = document.getElementById('deviceName').value
            timezone = document.getElementById('timeZone').value
            delay = document.getElementById('delay').value
            realtime = document.getElementById('transfer').value
            transfertime = document.getElementById('transferTime').value
            transtimes = document.getElementById('transtimes').value

            Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Guardar la configuracion del dispositivo ' + currentSN + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, guardar!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('devices.save-device-config') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN, name: name, timezone: timezone, delay: delay, realtime: realtime, transfertime: transfertime, transtimes: transtimes })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Configuraciones guardadas", '', 'success');
                        })
                        .then(() => {
                            CloseDeviceConfigModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al guardar las configuraciones', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
                    }
                });
        }

        function EmployeeDeleteData(){
            const listEmployees = document.querySelectorAll('.dropdown-list ul li');
            const checkedEmpIds = [];
            listEmployees.forEach((item) => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    checkedEmpIds.push(checkbox.name);
                }
            });
            if(checkedEmpIds.length === 0) {
                Swal.fire('Ningún empleado seleccionado', '', 'error');
                return;
            }
            Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Eliminar empleados seleccionados del dispositivo ' + currentSN + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, eliminarlos!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('devices.delete-employee') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN, empids: checkedEmpIds })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Empleados enviados para eliminar", '', 'success');
                        })
                        .then(() => {
                            CloseDeleteEmployeeModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al enviar la solicitud de eliminacion', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
                    }
                });
        }

        function CloseEnrollModal() {
            var enrollModal = document.getElementById('enrollModal');
            var modal = bootstrap.Modal.getInstance(enrollModal);
            modal.hide();
        }

        function OpenEnrollModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            var modal = new bootstrap.Modal(document.getElementById('enrollModal'), {});
            modal.show();
        }

        function ClosePictureModal() {
            var PictureModal = document.getElementById('pictureModal');
            var modal = bootstrap.Modal.getInstance(PictureModal);
            modal.hide();
        }

        function OpenPictureModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            var modal = new bootstrap.Modal(document.getElementById('pictureModal'), {});
            modal.show();
        }

        function CloseDuplicateTimeModal() {
            var duplicateTimeModal = document.getElementById('duplicateTimeModal');
            var modal = bootstrap.Modal.getInstance(duplicateTimeModal);
            modal.hide();
        }

        function OpenDuplicateTimeModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            var modal = new bootstrap.Modal(document.getElementById('duplicateTimeModal'), {});
            modal.show();
        }

        function CloseDownloadModal() {
            var downloadModal = document.getElementById('downloadData');
            var modal = bootstrap.Modal.getInstance(downloadModal);
            modal.hide();
        }

        function OpenDownloadModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            document.getElementById('inlineRadio1').checked = true;
            document.getElementById('inlineRadio2').checked = false;
            document.getElementById('employeeSelect').classList.add('visually-hidden');
            const listEmployees = document.querySelectorAll('.dropdown-list ul li');
            listEmployees.forEach((item) => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                }
            });
            var modal = new bootstrap.Modal(document.getElementById('downloadData'), {});
            modal.show();
        }

        function CloseUploadModal() {
            var uploadModal = document.getElementById('uploadData');
            var modal = bootstrap.Modal.getInstance(uploadModal);
            modal.hide();
        }

        function OpenUploadModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            document.getElementById('allEmployeesUpload').checked = true;
            document.getElementById('specificEmployeeUpload').checked = false;
            document.getElementById('employeeSelectUpload').classList.add('visually-hidden');
            const listEmployees = document.querySelectorAll('.dropdown-list ul li');
            listEmployees.forEach((item) => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                }
            });
            var modal = new bootstrap.Modal(document.getElementById('uploadData'), {});
            modal.show();
        }

        function CloseDeleteEmployeeModal() {
            var DeleteEmployeeModal = document.getElementById('DeleteEmployee');
            var modal = bootstrap.Modal.getInstance(DeleteEmployeeModal);
            modal.hide();
        }

        function OpenDeleteEmployeeModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            const listEmployees = document.querySelectorAll('.dropdown-list ul li');
            listEmployees.forEach((item) => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                }
            });
            var modal = new bootstrap.Modal(document.getElementById('DeleteEmployee'), {});
            modal.show();
        }

        function CloseDeviceConfigModal() {
            var DeviceConfigModal = document.getElementById('DeviceConfig');
            var modal = bootstrap.Modal.getInstance(DeviceConfigModal);
            modal.hide();
        }

        function OpenDeviceConfigModal() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }
            $("#loader-lu").addClass("is-active");
            fetch("{{ route('devices.get-device-config') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            var configs = data.configs;
                            document.getElementById('deviceName').value = configs.name;
                            document.getElementById('timeZone').value = configs.timezone;
                            document.getElementById('delay').value = configs.delay;
                            document.getElementById('transfer').value = configs.realtime;
                            document.getElementById('transferTime').value = configs.transinterval;
                            document.getElementById('transtimes').value = configs.transtimes;
                            if(configs.realtime == "1")
                            {
                                document.getElementById('transferTime').disabled = true;
                                document.getElementById('transtimes').disabled = true;
                            }
                            else 
                            {
                                document.getElementById('transferTime').disabled = false;
                                document.getElementById('transtimes').disabled = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al consultar la informacion del lector', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
            var modal = new bootstrap.Modal(document.getElementById('DeviceConfig'), {});
            modal.show();
        }

        function CloseNewUserModal() {
            var newUserModal = document.getElementById('newUserModal');
            var modal = bootstrap.Modal.getInstance(newUserModal);
            modal.hide();
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.6/underscore-min.js"></script>

    <script>
        function DownloadData() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            const all = document.getElementById('inlineRadio1').checked;
            const many = document.getElementById('inlineRadio2').checked;

            if(many) {
                const listEmployees = document.querySelectorAll('.dropdown-list ul li');
                const checkedEmpIds = [];
                listEmployees.forEach((item) => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox && checkbox.checked) {
                        checkedEmpIds.push(checkbox.name);
                    }
                });
                if(checkedEmpIds.length === 0) {
                    Swal.fire('Ningún empleado seleccionado', '', 'error');
                    return;
                }
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Descargar datos para empleados seleccionados del dispositivo ' + currentSN + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, descargarlo!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('devices.download') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN, empids: checkedEmpIds })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Solicitud de descarga enviada", '', 'success');
                        })
                        .then(() => {
                            CloseDownloadModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al enviar la solicitud de descarga', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
                    }
                });
            }   
            else {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Descargar datos para ' + (all ? 'todos los empleados' : 'empleados seleccionados') + ' del dispositivo ' + currentSN + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, descargarlo!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('devices.download') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN, all: all })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Solicitud de descarga enviada", '', 'success');
                        })
                        .then(() => {
                            CloseDownloadModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al enviar la solicitud de descarga', '', 'error');
                        });
                    }
                });
            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            document.querySelectorAll(".dropdown-container").forEach(container => {

                container.addEventListener("click", e => {
                    if (e.target.classList.contains("dropdown-button")) {
                        const list = container.querySelector(".dropdown-list");
                        list.classList.toggle("show");
                    }
                });

                container.addEventListener("input", e => {
                    if (e.target.classList.contains("dropdown-search")) {
                        const search = e.target.value.toLowerCase();
                        const items = container.querySelectorAll(".dropdown-list li");

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

            function createListItem(emp) {
                const li = document.createElement("li");
                const capName = `${emp.employee_id} - ${emp.name.charAt(0).toUpperCase()}${emp.name.slice(1)}`;

                li.innerHTML = `
                    <label class="checkbox-wrap">
                        <input name="${emp.employee_id}" type="checkbox">
                        <span>${capName}</span>
                        <span class="checkmark"></span>
                    </label>
                `;
                return li;
            }

            const allEmployeeLists = document.querySelectorAll(".dropdown-list ul");

            fetch("{{ route('employee.list-employees') }}", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(r => r.json())
            .then(data => {
                const empData = data.employees;

                allEmployeeLists.forEach(ul => {
                    empData.forEach(emp => {
                        ul.appendChild(createListItem(emp));
                    });
                });
            });
        });
    </script>

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

        function newUserForm(){
            document.getElementById('newUserModalTitle').textContent = 'Nuevo usuario';
            document.getElementById('userAction').textContent = 'Crear usuario';
            document.getElementById('userName').value = '';
                document.getElementById('userEmail').value = '';
            const listPermissions = document.querySelectorAll('.dropdown-list-permissions ul li');
                listPermissions.forEach((item) => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = false;

                })
        }

        function getUserData(email) {
            $("#loader-lu").addClass("is-active");
            fetch("{{ route('users.get-user-permissions') }}?email=" + email, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(r => r.json())
            .then(data => {
                var userData = data.data;
                document.getElementById('userName').value = userData.name;
                document.getElementById('userEmail').value = userData.email;
                document.getElementById('newUserModalTitle').textContent = 'Editar usuario';
                document.getElementById('userAction').textContent = 'Editar usuario';
                //const permissions = JSON.parse(userData.permissions);
                const listPermissions = document.querySelectorAll('.dropdown-list-permissions ul li');
                listPermissions.forEach((item) => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = userData.permissions.some(p => p.name === checkbox.name);

                })
                $("#loader-lu").removeClass("is-active");
            });
        }

        function dropUser(email){
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Eliminar usuario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, eliminarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if(result.isConfirmed) {
                    fetch("{{ route('users.drop-user') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({email: email})
                    })
                    .then(r => r.json())
                    .then(data => {
                        Swal.fire(data.message || "Usuario eliminado", '', 'success');
                    })
                    .catch(err => {
                        Swal.fire('Error al eliminar el usuario', '', 'error');
                    })
                }
            });
        }

        function newUser() {
            const listPermissions = document.querySelectorAll('.dropdown-list-permissions ul li');
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
                    title: '¿Estás seguro?',
                    text: '¿Crear nuevo usuario?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, crearlo!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('users.new-user') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ name: nombre, email: email, pass: pass, perm: checkedPerIds})
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Usuario creado", '', 'success');
                        })
                        .then(() => {
                            CloseNewUserModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al crear el usuario', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
                    }
                });
        }
    </script>

    <script>
        function UploadData() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            const all = document.getElementById('allEmployeesUpload').checked;
            const many = document.getElementById('specificEmployeeUpload').checked;
            const fp = document.getElementById('fingerprints').checked;
            const face = document.getElementById('faces').checked;
            const photo = document.getElementById('Photos').checked;

            if(many) {
                const listEmployees = document.querySelectorAll('.dropdown-list ul li');
                const checkedEmpIds = [];
                listEmployees.forEach((item) => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox && checkbox.checked) {
                        checkedEmpIds.push(checkbox.name);
                    }
                });
                if(checkedEmpIds.length === 0) {
                    Swal.fire('Ningún empleado seleccionado', '', 'error');
                    return;
                }
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Subir datos para empleados seleccionados al dispositivo ' + currentSN + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, subirlo!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('devices.upload') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN, empids: checkedEmpIds, fp: fp, face: face, photo: photo })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Solicitud de subida enviada", '', 'success');
                        })
                        .then(() => {
                            CloseUploadModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al enviar la solicitud de subida', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
                    }
                });
            }   
            else {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Subir datos para ' + (all ? 'todos los empleados' : 'empleados seleccionados') + ' al dispositivo ' + currentSN + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '¡Sí, subirlo!',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#loader-lu").addClass("is-active");
                        fetch("{{ route('devices.upload') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sn: currentSN, all: all, fp: fp, face: face, photo: photo })
                        })
                        .then(r => r.json())
                        .then(data => {
                            $("#loader-lu").removeClass("is-active");
                            Swal.fire(data.message || "Solicitud de subida enviada", '', 'success');
                        })
                        .then(() => {
                            CloseUploadModal();
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error al enviar la solicitud de subida', '', 'error');
                            $("#loader-lu").removeClass("is-active");
                        });
                    }
                });
            }
        }
    </script>
    <script>
        function uploadEmployeePhoto() {
            if (!currentEmployee) {
                Swal.fire('Ningún empleado seleccionado', '', 'error');
                return;
            }

            const fileInput = document.getElementById('employeePhotoInput');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire('Ninguna foto seleccionada', '', 'error');
                return;
            }

            if (file.type !== 'image/jpeg') {
                Swal.fire('Tipo de archivo inválido. Por favor seleccione una imagen JPEG.', '', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const base64 = e.target.result.split(',')[1];
                const size = file.size;

                fetch("{{ route('employee.upload-photo') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ employee_id: currentEmployee, base64: base64, size: size })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Foto subida exitosamente', '', 'success');
                        document.getElementById('employeePhoto').src = '/storage/userpic/' + currentEmployee + '.jpg';
                    } else {
                        Swal.fire(data.message || 'Error al subir la foto', '', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error al subir la foto', '', 'error');
                });
            };
            reader.readAsDataURL(file);
        }
    </script>
    <script>
        function DeleteData() {
            if (!currentSN) {
                Swal.fire('Ningún dispositivo seleccionado', '', 'error');
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Borrar todos los datos en el dispositivo ' + currentSN + '? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Sí, borrarlo!',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Segunda confirmación',
                        text: 'Esto borrará permanentemente todos los datos en el dispositivo. ¿Estás absolutamente seguro?',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonText: '¡Sí, borrar permanentemente!',
                        cancelButtonText: 'No, cancelar'
                    }).then((secondResult) => {
                        if (secondResult.isConfirmed) {
                            fetch("{{ route('devices.delete-data') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ sn: currentSN })
                            })
                            .then(r => r.json())
                            .then(data => Swal.fire(data.message || "Solicitud de borrado enviada", '', 'success'))
                            .catch(err => {
                                console.error(err);
                                Swal.fire('Error al enviar la solicitud de borrado', '', 'error');
                            });
                        } else {
                            Swal.fire('Acción de borrado cancelada', '', 'info');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>