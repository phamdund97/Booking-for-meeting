<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet" />
 @include('auth.cdnAuth.cdn')
    <meta name="csrf-token" value="{{ csrf_token() }}" />
    <title>Forgot Password</title>
    <style>
        .mainBody{
            background-color: #f0f0f0;
            width: 100%;
            height: 100%;
            /*ngăn ko cho scrollbar bug @@*/
            overflow:hidden;
        }
    </style>
</head>
<body class="mainBody">

<div id="app">

    <example-component></example-component>
</div>
<script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
</body>
</html>
