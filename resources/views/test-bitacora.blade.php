<!DOCTYPE html>
<html>
<head>
    <title>Prueba Bitácora</title>
</head>
<body>
    <h1>Prueba de Enlaces Bitácora</h1>
    
    <div style="padding: 20px;">
        <h2>Enlaces de prueba:</h2>
        
        <p><a href="{{ route('bitacora.index') }}" style="color: blue; text-decoration: underline;">
            Enlace 1: route('bitacora.index')
        </a></p>
        
        <p><a href="/bitacora" style="color: green; text-decoration: underline;">
            Enlace 2: /bitacora (directo)
        </a></p>
        
        <p><a href="{{ url('bitacora') }}" style="color: red; text-decoration: underline;">
            Enlace 3: url('bitacora')
        </a></p>
        
        <br>
        
        <form action="{{ route('bitacora.index') }}" method="GET">
            <button type="submit" style="padding: 10px; background: orange; color: white; border: none;">
                Botón: POST a bitacora.index
            </button>
        </form>
        
        <br><br>
        
        <p><a href="{{ route('dashboard') }}" style="color: purple; text-decoration: underline;">
            Volver al Dashboard (para probar que otros enlaces funcionan)
        </a></p>
    </div>

    <script>
        console.log('Página cargada correctamente');
        
        // Agregar listeners a todos los enlaces para debug
        document.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                console.log('Enlace clickeado:', this.href);
                console.log('Evento:', e);
            });
        });
    </script>
</body>
</html>
