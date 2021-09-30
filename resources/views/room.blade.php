<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/app.css">
    <title>Document</title>
</head>
<body class="wrapper room">
    <div class="container vertical_center _cells_container">
        @include('room_cells')
    </div>

    <div>
        @for ($i = 0; $i < count($colors); $i++)
            <button class="room__button _put_color" data-game-id="{{ $id }}" data-color="{{ $colors[$i] }}">
                <span class="block" style="background-color: {{ $colors[$i] }}"></span>
            </button>
        @endfor
    </div>

</body>

<script src="/js/app.js"></script>

</html>
