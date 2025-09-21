<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description"
        content="Smart loans built for Nigerians. Get fast, secure, and transparent loans without hidden fees.">
    <meta name="keywords" content="loans, nigeria, finance, quick loans, personal loans">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('assets/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('assets/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('assets/favicon-16x16.png')}}">
    <link rel="manifest" href="{{asset('assets/site.webmanifest')}}">
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700&display=swap" rel="stylesheet" />
    {{-- <link href="{{asset('build/assets/app.css')}}" rel="stylesheet"> --}}

    <!-- Vite Assets -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Assets -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @stack('styles')
</head>
