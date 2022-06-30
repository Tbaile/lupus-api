<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css'])
    <title>{{ config('app.name') }}</title>
</head>
<body class="h-screen flex bg-black">
<img alt="Sax Gandalf" class="object-contain w-full" src="img/sax_gandalf.gif">
</body>
</html>
