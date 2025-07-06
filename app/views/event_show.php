<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes do Evento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>📋 Detalhes do Evento</h2>
  <hr>
  <p><strong>Título:</strong> <?= htmlspecialchars($event['title']) ?></p>
  <p><strong>Início:</strong> <?= date('d/m/Y H:i', strtotime($event['start'])) ?></p>
  <p><strong>Fim:</strong> <?= date('d/m/Y H:i', strtotime($event['end'])) ?></p>
  <p><strong>Sala:</strong> <?= $event['sala'] == 'reuniao' ? 'Sala de Reunião' : 'Sala de Treinamento' ?></p>

  <h4 class="mt-4">👥 Participantes</h4>
  <ul class="list-group">
    <?php foreach ($participants as $p): ?>
      <li class="list-group-item"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>)</li>
    <?php endforeach; ?>
  </ul>

  <a href="../event/index" class="btn btn-secondary mt-4">← Voltar ao calendário</a>
</div>
</body>
</html>
