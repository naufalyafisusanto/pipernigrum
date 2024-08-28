<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        
        @yield('title')

        <link rel="icon" href="/assets/img/pipernigrum-icon.png" type="image/icon type">
        
        @stack('stylesheet')

        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="/assets/font-awesome-pro/css/all.css">
        <link rel="stylesheet" href="/assets/font-awesome-pro/css/all-pro.css">
        <link rel="stylesheet" href="/assets/css/style.css">
        <link rel="stylesheet" href="/assets/css/components.css">
        <link rel="stylesheet" href="/assets/css/iziToast.css">

        <style>
            .color-pipernigrum {
                color: #404040;
            }
        </style>

        @stack('style')
    </head>

    <body>
        @yield('body')

        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/popper.min.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        <script src="/assets/js/jquery.nicescroll.min.js"></script>
        <script src="/assets/js/moment.min.js"></script>
        <script src="/assets/js/iziToast/iziToast.min.js"></script>
        <script src="/assets/js/stisla.js"></script>
        <script src="/assets/js/scripts.js"></script>

        @stack('script')
    </body>
</html>
