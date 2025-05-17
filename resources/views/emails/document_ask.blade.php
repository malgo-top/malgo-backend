<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Documentación Requerida</title>
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

        .info-icon {
            color: #5bc0de;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .important-message {
            color: #5bc0de;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
        }

        .documentation-box {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }

        .action-link {
            color: #8f754f;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">Malgo</div>

        <div class="email-card">
            <div class="email-content">
                <div class="info-icon">ℹ</div>

                <p>Hola {{ $fullName }},</p>

                <p class="important-message">Para poder proceder con la evaluación de tu solicitud para la propiedad</p>

                <div class="documentation-box">
                    <p>Es necesario que proporciones: {{ $customMessage }}</p>
                    <p>Por favor, accede a este enlace: <a href="{{ $link }}" class="action-link">{{ $link }}</a> y envía todos los documentos solicitados en un único archivo PDF.</p>
                </div>

                <p>¡Muchas gracias por tu colaboración!</p>
            </div>

            <div class="email-footer">
                <p>Atentamente,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>