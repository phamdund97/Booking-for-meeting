<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="{{ mix('css/app.css') }}" type="text/css" rel="stylesheet" />
    @include('auth.cdnAuth.cdn')
    <meta name="csrf-token" value="{{ csrf_token() }}" />
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
