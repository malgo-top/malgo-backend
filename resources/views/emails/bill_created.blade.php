<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Recibo</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f5f0;
            margin: 0;
            padding: 0;
            color: #333;
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
            line-height: 1.6;
            font-size: 16px;
        }

        .email-footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .bill-details {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }

        .detail-item {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 500;
            color: #8f754f;
            min-width: 150px;
        }

        .detail-value {
            font-weight: 400;
        }

        .button {
            display: inline-block;
            background-color: #8f754f;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 25px;
            margin-top: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">Malgo</div>

        <div class="email-card">
            <div class="email-content">
                <p>Hola {{ $user->name }},</p>

                <p>Ya está disponible tu nuevo recibo de <strong>{{ $billTypeName }}</strong>.</p>

                <div class="bill-details">
                    <div class="detail-item">
                        <span class="detail-label">Fecha límite de pago:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Monto:</span>
                        <span class="detail-value">${{ number_format($bill->amount, 2) }}</span>
                    </div>
                </div>

                <p>Por favor realiza el pago a tiempo para evitar recargos.</p>

            </div>

            <div class="email-footer">
                <p>Gracias por confiar en nosotros,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
