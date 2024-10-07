<!DOCTYPE html>
<html>
<head>
    <title>Recuperação de Senha</title>
</head>
<body class="container" style="font-family: Arial, sans-serif; align-items: center; justify-content: center; text-align: center;">
    <h1 style="color: #333;">Recuperação de Senha</h1>
    <p style="color: #666;">Olá {{ $razao_social }}, aqui está o seu código para recuperar sua senha no App Pleno Contabilidade.</p>
    <h3 class="token" style="font-weight: bold; color: #333;">{{ $token }}</h3>
    <p style="color: #666;">Este código expira em aproximadamente 15 minutos a partir do envio deste email.</p>
    <p style="color: #666;">Este email não é monitorado. Para suporte ou contato com nossa equipe técnica, envie um email para:</p>
    <p style="color: #666;">plenocontabilidadesc1@gmail.com</p>
    <p style="color: #666;">Obrigado,</p>
    <p style="color: #666;">Equipe Pleno Contabilidade.</p>
</body>
</html>