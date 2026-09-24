
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Fuera de servicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
      body {
        font-family: Arial, sans-serif;
        background: #f7f7f7;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        justify-content: center;
        align-items: center;
      }
      .container {
        background: #fff;
        padding: 32px 24px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        text-align: center;
        max-width: 400px;
        width: 100%;
      }
      .logo {
        display: block;
        margin: 0 auto 24px auto;
        max-width: 120px;
      }
      .message {
        font-size: 1.1em;
        margin-bottom: 32px;
        color: #333;
      }
      .btn {
        display: inline-block;
        padding: 12px 32px;
        background: #0078d4;
        color: #fff;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        font-size: 1em;
        cursor: pointer;
        transition: background 0.2s;
      }
      .btn:hover {
        background: #005fa3;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <img src="{{asset('storage/logo_ossc.png')}}" alt="Logo" class="logo" />
      <div class="message">
        <h3>Temporalmente fuera de servicio</h3>
        <p>
          Estamos realizando tareas de mantenimiento. Por favor, inténtelo de nuevo más tarde.
        </p>
      </div>
    </div>
  </body>
</html>
