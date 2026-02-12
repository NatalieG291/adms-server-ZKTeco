<!DOCTYPE html>
<html>
<head>
    <title>Registros ATTLOG</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f4f4f4;
        }
    </style>
</head>
<body>

<h2>Registros ATTLOG procesados</h2>

<table>
    <thead>
        <tr>
            <th>ID Log</th>
            <th>Empleado</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Status1</th>
            <th>Status2</th>
            <th>Status3</th>
            <th>Status4</th>
            <th>Status5</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attlogs as $a)
        <tr>
            <td>{{ $a['id'] }}</td>
            <td>{{ $a['employee_id'] }}</td>
            <td>{{ $a['fecha'] }}</td>
            <td>{{ $a['hora'] }}</td>
            <td>{{ $a['status1'] }}</td>
            <td>{{ $a['status2'] }}</td>
            <td>{{ $a['status3'] }}</td>
            <td>{{ $a['status4'] }}</td>
            <td>{{ $a['status5'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>