<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nueva Solicitud de Arrendamiento</title>
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

        .notification-icon {
            color: #5cb85c;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .notification-message {
            color: #5cb85c;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
        }

        .property-box {
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">Malgo</div>

        <div class="email-card">
            <div class="email-content">
                <div class="notification-icon">✉</div>

                <p>Hola administradores de Malgo,</p>

                <p class="notification-message">Se ha recibido una nueva solicitud de arrendamiento</p>

                <div class="property-box">
                    <p>Para la propiedad: <span class="property-name">{{ $property }}</span></p>
                </div>

                <p>Por favor revisen el sistema para evaluar esta solicitud.</p>
            </div>

            <div class="email-footer">
                <p>Atentamente,</p>
                <p><strong>El sistema de notificaciones de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>