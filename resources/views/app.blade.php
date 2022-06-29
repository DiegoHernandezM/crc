<!DOCTYPE html>
<html class="h-full bg-gray-200">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/manifest.js') }}" defer></script>
    <script src="{{ asset('js/vendor.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    @routes
</head>
<body class="font-sans antialiased leading-none text-gray-800">

@inertia

</body>
</html>
