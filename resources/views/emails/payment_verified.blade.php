<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pago Verificado</title>
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

        .success-icon {
            color: #5cb85c;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .success-message {
            color: #5cb85c;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
        }

        .receipt-link {
            display: inline-block;
            background-color: #8f754f;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">Malgo</div>

        <div class="email-card">
            <div class="email-content">
                <div class="success-icon">✓</div>

                <p>Hola {{ $user->name }},</p>

                <p class="success-message">¡Tu pago ha sido verificado exitosamente!</p>

                <p>Hemos recibido y procesado tu pago correctamente.</p>

                <!-- Optional receipt download link -->
                <!--
                <center>
                    <a href="{{ $receiptUrl }}" class="receipt-link">
                        Descargar Comprobante
                    </a>
                </center>
                -->

                <p>Gracias por tu puntualidad. No dudes en contactarnos si tienes alguna pregunta.</p>
            </div>

            <div class="email-footer">
                <p>Saludos cordiales,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
