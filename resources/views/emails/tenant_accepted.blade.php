<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Solicitud Aprobada</title>
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
        }

        .email-footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .congrats-icon {
            color: #8f754f;
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }

        .congrats-message {
            color: #8f754f;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }

        .next-steps {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
        }

        .contact-button {
            display: inline-block;
            background-color: #8f754f;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 25px;
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
                <div class="congrats-icon">🏠</div>

                <p class="congrats-message">¡Felicitaciones {{ $fullName }}!</p>

                <p>Has sido seleccionado para el arriendo del inmueble.</p>

                <div class="next-steps">
                    <p><strong>Próximos pasos:</strong></p>
                    <p>Nuestro equipo se pondrá en contacto contigo dentro de las próximas 48 horas para continuar con el proceso de contrato.</p>
                </div>

                <!-- Optional contact button -->
                <!--
                <center>
                    <a href="mailto:contacto@malgo.com" class="contact-button">
                        Contactar Ahora
                    </a>
                </center>
                -->

                <p>Gracias por confiar en nosotros. Estamos emocionados de tenerte como parte de nuestra comunidad.</p>
            </div>

            <div class="email-footer">
                <p>Atentamente,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
