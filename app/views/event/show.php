<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h2><?php echo htmlspecialchars($eventData['title']) ?></h2>
  <p><strong>Início:</strong> <?php echo $eventData['start'] ?></p>
  <p><strong>Fim:</strong> <?php echo $eventData['end'] ?></p>
  <p><strong>Sala:</strong> <?php echo $eventData['sala'] ?></p>

  <h4>Participantes</h4>
  <ul>
    <?php foreach ($participants as $p): ?>
      <li><?php echo htmlspecialchars($p['nome']) ?> (<?php echo $p['email'] ?>)</li>
    <?php endforeach; ?>
  </ul>
  <a href="../event/index" class="btn btn-secondary mt-3">Voltar</a>
</body>
</html>