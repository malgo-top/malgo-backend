<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Solicitud Rechazada</title>
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

        .rejection-icon {
            color: #d9534f;
            font-size: 48px;
            margin-bottom: 20px;
        }

        .rejection-message {
            color: #d9534f;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
        }

        .feedback-box {
            background-color: #f8f5f0;
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }

        .contact-link {
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
                <div class="rejection-icon">✕</div>

                <p>Hola <?php echo e($fullName); ?>,</p>

                <p class="rejection-message">Lamentamos informarte que tu solicitud de arrendamiento no ha sido aprobada</p>

                

                <p>¡Te deseamos mucho éxito en tu búsqueda de vivienda!</p>
            </div>

            <div class="email-footer">
                <p>Atentamente,</p>
                <p><strong>El equipo de Malgo</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\jeibe\Documents\todo\github\backend-20250418T224527Z-001\backend\resources\views/emails/tenant_denied.blade.php ENDPATH**/ ?>