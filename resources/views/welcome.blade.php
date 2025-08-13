<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        
        <script>
            // Redirigir inmediatamente al login
            window.location.href = "{{ route('login') }}";
        </script>
    </head>
    <body>
        <!-- Mensaje opcional mientras redirije -->
        <div style="display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif;">
            <p>Redirigiendo al login...</p>
        </div>
    </body>
</html>