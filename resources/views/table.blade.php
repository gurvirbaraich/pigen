<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Table</title>

    <style>
        * {
            font-family: "Dank Mono", monospace;
        }

        table, tr, th, td {
            border-collapse: collapse;
            border: 1px solid #000;
        }

        td, th {
            padding: 10px;
        }
    </style>
</head>
<body>
<table>
  <thead>
    <tr>
      <th>ID:</th>
      <th>Name:</th>
    </tr>
  </thead>
    <tbody>
    @foreach($data as $key => $value)
        <tr>
            <td>
                {{ $value->id  }}
            </td>
            <td>
                {{ $value->name  }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>