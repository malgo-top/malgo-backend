<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Felicidades! Tu contrato ha sido firmado</title>
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

        .credentials-box {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }

        .credential-item {
            display: flex;
            margin-bottom: 10px;
        }

        .credential-label {
            font-weight: 500;
            color: #8f754f;
            min-width: 100px;
        }

        .credential-value {
            font-weight: 400;
        }

        .login-button {
            display: inline-block;
            background-color: #8f754f;
            color: white !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
        }

        .welcome-message {
            font-size: 18px;
            color: #8f754f;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">Malgo</div>

        <div class="email-card">
            <div class="email-content">
                <p>Hola {{ $fullName }},</p>

                <p class="welcome-message">¡Felicitaciones! Has firmado con éxito el contrato de arrendamiento.</p>

                <p>Te damos la bienvenida a nuestra plataforma. Aquí están tus credenciales para acceder al sistema:</p>

                <div class="credentials-box">
                    <div class="credential-item">
                        <span class="credential-label">Email:</span>
                        <span class="credential-value">{{ $email }}</span>
                    </div>
                    <div class="credential-item">
                        <span class="credential-label">Contraseña:</span>
                        <span class="credential-value">{{ $password }}</span>
                    </div>
                </div>

                <center>
                    <a href="{{ $loginUrl }}" class="login-button">
                        Iniciar sesión
                    </a>
                </center>

                <p>Te recomendamos cambiar tu contraseña después de tu primer ingreso al sistema.</p>
            </div>

            <div class="email-footer">
                <p>Atentamente,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
