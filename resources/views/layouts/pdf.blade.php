<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    @if (sizeof(\Config::get('pdfwriter')['pdf_css']) > 0)
        @foreach (\Config::get('pdfwriter')['pdf_css'] as $script)
            <link href="<?php echo base_path() ?>/public/css/{{ $script }}" rel="stylesheet">
        @endforeach
    @endif

    <title>@yield('title')</title>
</head>
<body id="pdf-view">
<div class="col-md-12">
    @yield('content')
</div>
</body>
</html>