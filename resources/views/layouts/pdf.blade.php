<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        html, body {
            width: 100%;
        }
    </style>


    @if (sizeof(\Config::get('pdfwriter')['pdf_css']) > 0)
        @foreach (\Config::get('pdfwriter')['pdf_css'] as $script)
            <link href="{{ url('/css/' . $script) }}" rel="stylesheet">
        @endforeach
    @endif

    <title>@yield('title')</title>
</head>
<body id="pdf-view">
<div class="container-fluid">
    @yield('content')
</div>
</body>
</html>
