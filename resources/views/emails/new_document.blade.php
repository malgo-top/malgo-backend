<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo Documento Subido</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f5f0;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-header {
            color: #8f754f;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 20px;
            text-align: center;
        }

        .email-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .email-content {
            font-size: 16px;
            text-align: center;
        }

        .email-footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .document-icon {
            color: #8f754f;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .alert-message {
            color: #8f754f;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
        }

        .info-box {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .property-name {
            font-weight: 600;
            color: #8f754f;
        }

        .action-button {
            display: inline-block;
            background-color: #8f754f;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            margin-top: 15px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">Malgo</div>

        <div class="email-card">
            <div class="email-content">
                <div class="document-icon">📄</div>

                <p>Hola administradores de Malgo,</p>

                <p class="alert-message">Se ha subido un nuevo documento</p>

                <div class="info-box">
                    <p>En la aplicacion numero: <span class="property-name">{{ $property }}</span></p>
                    <p>El documento está disponible en el sistema para su revisión.</p>
                </div>
            </div>

            <div class="email-footer">
                <p>Atentamente,</p>
                <p><strong>El sistema de notificaciones de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>