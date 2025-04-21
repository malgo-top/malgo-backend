<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recordatorio de Factura Vencida</title>
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

        .bill-details {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #d9534f;
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

        .alert-message {
            color: #d9534f;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .payment-button {
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
                <p>Hola {{ $name }},</p>

                <p class="alert-message">Tiene una factura pendiente que ya venció:</p>

                <div class="bill-details">
                    <div class="detail-item">
                        <span class="detail-label">Tipo de factura:</span>
                        <span class="detail-value">{{ $bill->billType->name }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fecha de vencimiento:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Monto adeudado:</span>
                        <span class="detail-value">${{ number_format($bill->amount, 2) }}</span>
                    </div>
                </div>

                <p>Por favor realice el pago lo antes posible para evitar recargos o inconvenientes.</p>

                <!-- Optional payment button -->
                <!--
                <center>
                    <a href="{{ $paymentUrl }}" class="payment-button">
                        Pagar Ahora
                    </a>
                </center>
                -->

                {{-- <p>Si ya realizó el pago, por favor ignore este mensaje.</p> --}}
            </div>

            <div class="email-footer">
                <p>Gracias por su atención,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
