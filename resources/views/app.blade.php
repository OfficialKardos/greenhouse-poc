<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Greenhouse POC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Hardcoded paths from your build -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-DU33UtcU.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-BhDDsmCo.css') }}">
</head>
<body>
    <div id="app"></div>
    <script src="{{ asset('build/assets/app-CzTR8XmS.js') }}" defer></script>
    
    <!-- NUKE ANY VITE SCRIPTS -->
    <script>
        (function() {
            const killVite = function() {
                document.querySelectorAll('script[src*=":5173"]').forEach(el => el.remove());
                document.querySelectorAll('link[href*=":5173"]').forEach(el => el.remove());
            };
            killVite();
            setInterval(killVite, 10);
        })();
    </script>
</body>
</html>