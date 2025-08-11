<?php
$eventData = $eventData ?? null;
$participants = $participants ?? [];
?>

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

  <?php if ($eventData): ?>
    <p><strong>Criado por:</strong> <?= htmlspecialchars($eventData['creator_name']) ?> (<?= htmlspecialchars($eventData['creator_email']) ?>)</p>
    <p><strong>Título:</strong> <?= htmlspecialchars($eventData['title']) ?></p>
    <p><strong>Início:</strong> <?= date('d/m/Y H:i', strtotime($eventData['start'])) ?></p>
    <p><strong>Fim:</strong> <?= date('d/m/Y H:i', strtotime($eventData['end'])) ?></p>
    <p><strong>Sala:</strong> <?= $eventData['sala'] == 'reuniao' ? 'Sala de Reunião' : 'Sala de Treinamento' ?></p>
    
    <h4 class="mt-4">👥 Participantes</h4>
    <ul class="list-group">
      <?php if (!empty($participants)): ?>
        <?php foreach ($participants as $p): ?>
          <li class="list-group-item"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>)</li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="list-group-item">Nenhum participante cadastrado.</li>
      <?php endif; ?>
    </ul>
  <?php else: ?>
    <div class="alert alert-danger mt-3">Evento não encontrado.</div>
  <?php endif; ?>
  <a href="/agenda-salas/event/index" class="btn btn-secondary mt-4">← Voltar ao calendário</a>
</div>
</body>
</html>
