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
<body class="main">

    <div class="wrapper center column vertical_center">
        <h1 class="logo">© Floy TYZ</h1>

        <label for="width" class="label">Width
            <input type="text" id="width" placeholder="width" required>
        </label>

        <label for="height" class="label">Height
            <input type="text" id="height" placeholder="height" required>
        </label>

        <button class="button _generate_game">Generate game</button>

        <p class="_id_container"></p>
    </div>

    <hr>

    <div class="wrapper center column vertical_center">

        <label for="id" class="label">Id
            <input type="text" id="id" placeholder="id" required>
        </label>

        <button class="button _sign_in_game">Sign in game</button>
    </div>

</body>

<script src="/js/app.js"></script>

</html>
