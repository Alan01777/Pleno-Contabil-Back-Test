<!DOCTYPE html>
<html>
<head>
    <title>Solicitação de Tarefa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        h1 {
            color: #333333;
        }
        p {
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nova Solicitação de Tarefa</h1>
        <p><strong>Título:</strong> {{ $titulo }}</p>
        <p><strong>De:</strong> {{ $razao_social }}</p>
        <p><strong>Descrição:</strong> {{ $descricao }}</p>
    </div>
</body>
</html>
