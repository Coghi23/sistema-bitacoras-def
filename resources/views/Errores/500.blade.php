<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Error 500 - Error Interno del Servidor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .topbar {
            background: #134496;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .yellow-line {
            height: 5px;
            background-color: #f5c002;
            width: 100%;
        }

        .error-container {
            text-align: center;
            padding: 60px 20px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: #134496;
        }

        .error-message {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .error-description {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 40px;
        }

        .btn-regresar {
            background-color: #134496;
            color: white;
            border-radius: 50px;
            padding: 10px 30px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .btn-regresar:hover {
            background-color: #0d336f;
        }

        .error-img {
            max-width: 350px;
            margin-bottom: 40px;
        }

        @media (max-width: 767px) {
            .error-code {
                font-size: 5rem;
            }

            .error-message {
                font-size: 1.5rem;
            }

            .error-description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

  
    <div class="error-container">
        <img src="https://cdn-icons-png.flaticon.com/512/564/564619.png" alt="Error 500" class="error-img" />
        <div class="error-code">500</div>
        <div class="error-message">Error interno del servidor</div>
        <div class="error-description">
            Algo salió mal en el servidor. Estamos trabajando para solucionarlo lo antes posible.
        </div>
        <a href="{{ url('/') }}" class="btn btn-regresar">
            <i class="bi bi-arrow-left"></i> Volver al inicio
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
