<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMS Server</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" fetchpriority="high">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
        .blink {
            animation: blinker 2s linear infinite;
            color: red;
            font-family: sans-serif;
        }
            @keyframes blinker {
            50% { opacity: 0; }
        }
    </style>

    <style data-eqcss-read="true">
    @import url("https://fonts.googleapis.com/css?family=Lato:400,400i,700");
    @import url("https://fonts.googleapis.com/css?family=Inconsolata:400,700");

    input, button {
        color: inherit;
        font: inherit;
    }
    strong { font-weight: bold; }
    p { margin-bottom: 1em; }
    p:last-child { margin-bottom: 0; }

    .hands .finger, .value {
        font-family: Inconsolata, Consolas, monospace;
    }

    .info {
        font-size: 2vmin;
        padding: .5em;
        text-align: center;
        background: #FFF3E0;
        border: .5vmin solid rgba(0, 0, 0, .1);
        border-left: 0;
        border-right: 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
    }

    /* Hand */
    .left-hand {
        margin-right: 13.5% !important;
        margin-top: 25% !important;
    }

    .right-hand {
        margin-left: 13.5% !important;
        margin-top: 25% !important;
    }
    .hands {
        text-align: center;
    }
    .hands .hand {
        background: #CFD8DC;
        border-radius: 60% 60% 80% 80%;
        width: 20vmin;
        height: 25vmin;
        margin: 1vmin;
        position: relative;
        display: inline-grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(4, 1fr);
    }
    .hands .hand#hand_2 {
        transform: scaleX(-1);
    }
    .hands .hand > label {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        cursor: pointer;
    }

    /* All Fingers */
    .hands .finger {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
        position: relative;
        
        background-color: #CFD8DC;
        transition: background 0.2s, transform 0.2s;
    }
    .hands .finger:checked {
        background-color: #DCEDC8;
    }
    .hands .finger:after {
        display: block;
        font-size: 2.4vmin;
        padding: 1vmin 0;
        text-align: center;
    }

    /* Specific Fingers */
    .hands .finger.thumb {
        border-radius: 30% 30% 30% 30%;
        grid-row: 3;
        grid-column: 4;
        margin: .5vmin auto;
        margin-left: 105%;
        margin-right: -150%;
        top: -30%;
        transform-origin: center right;
    }
    .hands .finger.index,
    .hands .finger.middle,
    .hands .finger.ring,
    .hands .finger.pinky {
        border-radius: 30% 30% 30% 30%;
        grid-row: 1;
        margin: auto .5vmin;
        margin-bottom: 130%;
        transform-origin: bottom left;
    }
    .hands .finger.index { margin-top: -260%; grid-column: 4; }
    .hands .finger.middle { margin-top: -290%; grid-column: 3; }
    .hands .finger.ring { margin-top: -240%; grid-column: 2; }
    .hands .finger.pinky { margin-top: -180%; grid-column: 1; }

    /* Specific Finger states */
    .hands .finger { */
        /* Transformations */
        --checked-true: scaleY(1);
        --checked-false: scaleY(1);
        --checked: var(--checked-false);
        --flipped-true: scaleX(-1);
        --flipped-false: scaleX(1);
        --flipped: var(--flipped-false);
        transform: var(--checked);
    }
    .hands #hand_2 .finger {
        --flipped: var(--flipped-true);
    }

    .hands {
        /* Assign values */
        --value-thumb_1: 4;
        --value-index_1: 3;
        --value-middle_1: 2;
        --value-ring_1: 1;
        --value-pinky_1: 0;
        --value-pinky_2: 9;
        --value-ring_2: 8;
        --value-middle_2: 7;
        --value-index_2: 6;
        --value-thumb_2: 5;
        
        /* Initial added values */
        --added-thumb_1: 	0;
        --added-index_1: 	0;
        --added-middle_1: 0;
        --added-ring_1: 	0;
        --added-pinky_1: 	0;
        --added-pinky_2: 	0;
        --added-ring_2: 	0;
        --added-middle_2: 0;
        --added-index_2: 	0;
        --added-thumb_2: 	0;
        
        /* Total */
        --value-total: calc(
            var(--added-thumb_1) + var(--added-index_1) + var(--added-middle_1) + var(--added-ring_1) + var(--added-pinky_1) + var(--added-pinky_2) + var(--added-ring_2) + var(--added-middle_2) + var(--added-index_2) + var(--added-thumb_2)
        );
        
        /* Convert values to strings */
        counter-reset:
            value-thumb_1 var(--value-thumb_1)
            value-index_1 var(--value-index_1)
            value-middle_1 var(--value-middle_1)
            value-ring_1 var(--value-ring_1)
            value-pinky_1 var(--value-pinky_1)
            value-thumb_2 var(--value-thumb_2)
            value-index_2 var(--value-index_2)
            value-middle_2 var(--value-middle_2)
            value-ring_2 var(--value-ring_2)
            value-pinky_2 var(--value-pinky_2)
            value-total var(--value-total)
            
            added-thumb_1 var(--added-thumb_1)
            added-index_1 var(--added-index_1)
            added-middle_1 var(--added-middle_1)
            added-ring_1 var(--added-ring_1)
            added-pinky_1 var(--added-pinky_1)
            added-thumb_2 var(--added-thumb_2)
            added-index_2 var(--added-index_2)
            added-middle_2 var(--added-middle_2)
            added-ring_2 var(--added-ring_2)
            added-pinky_2 var(--added-pinky_2)
        ;
    }

    /* Have fingers display values */
    .hands .finger.thumb { --content: counter(value-thumb_1); }
    .hands .finger.index { --content: counter(value-index_1); }
    .hands .finger.middle { --content: counter(value-middle_1); }
    .hands .finger.ring { --content: counter(value-ring_1); }
    .hands .finger.pinky { --content: counter(value-pinky_1); }
    .hands #hand_2 .finger.thumb { --content: counter(value-thumb_2); }
    .hands #hand_2 .finger.index { --content: counter(value-index_2); }
    .hands #hand_2 .finger.middle { --content: counter(value-middle_2); }
    .hands #hand_2 .finger.ring { --content: counter(value-ring_2); }
    .hands #hand_2 .finger.pinky { --content: counter(value-pinky_2); }
    .hands .finger:after {
        content: var(--content, "0");
        transform: var(--checked) var(--flipped);
    }
    </style>

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
  /* -webkit-box-shadow: 0px 10px 30px -4px rgba(0, 0, 0, 0.15); */
  /* -moz-box-shadow: 0px 10px 30px -4px rgba(0, 0, 0, 0.15);
  box-shadow: 0px 10px 30px -4px rgba(0, 0, 0, 0.15);  */
}

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
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top pb-0">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="storage/logo_ossc.png" height="25vw" width="auto" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="nav nav-tabs">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" id="devicesnav" href="{{ route('devices.index') }}">Dispositivo</a>
                    </li>
                    @can('view-attendance')
                    <li class="nav-item">
                        <a class="nav-link" id="attendance" href="{{ route('devices.Attendance') }}?start_date={{ date('Y-m-d', strtotime('-30 days')) }}&end_date={{ date('Y-m-d') }}">Asistencia</a>
                    </li>
                    @endcan
                    @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" id="devices-log" href="{{ route('devices.DeviceLog') }}">Registro del Dispositivo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="finger-log" href="{{ route('devices.FingerLog') }}">Registro de Huella</a>
                    </li>
                    @endrole
                    @can('view-attendance-photos')
                    <li class="nav-item">
                        <a class="nav-link" id="attphoto" href="{{ route('devices.AttPhoto') }}">Foto de Asistencia</a>
                    </li>
                    @endcan
                    @can('view-employees')
                    <li class="nav-item">
                        <a class="nav-link" id="employees" href="{{ route('employees.index') }}">Empleados</a>
                    </li>
                    @endcan
                    @role('admin')
                    <li class="nav-item">
                        <a class="nav-link" id="users" href="{{ route('users.index') }}">Usuarios</a>
                    </li>
                    @endrole
                    @endauth
                </ul>
            </div>
            <div class="navbar-nav ms-auto">
                <div class="nav-item">
                    <span class="navbar-text d-none d-lg-block p-0">
                        <strong>
                        @auth
                            {{ Auth::user()->name }}
                        @endauth
                        </strong>
                    </span>
                    <span class="navbar-text d-none d-lg-block p-0">
                        <span id="realtime-clock"></span>
                    </span>
                    <script>
                        function updateRealtimeClock() {
                            const now = new Date();
                            const formatted = now.toLocaleString('es-ES', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            document.getElementById('realtime-clock').textContent = formatted;
                        }
                        updateRealtimeClock();
                        setInterval(updateRealtimeClock, 1000);
                    </script>
                </div>
            </div>

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

    <!-- <script src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.6/underscore-min.js"></script>
</body>
</html>