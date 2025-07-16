<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Notificações</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">🔔 Histórico de Notificações</h3>

  <?php if (empty($notifications)): ?>
    <div class="alert alert-info">Você ainda não recebeu nenhuma notificação.</div>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($notifications as $n): ?>
        <?php
          $isRead = !empty($n['is_read']);
          $statusClass = $isRead ? 'text-muted' : 'fw-bold';
          $statusIcon = $isRead ? '✅' : '🔔';
        ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span class="<?= $statusClass ?>">
            <?= $statusIcon ?> <?= $n['message'] ?>
          </span>
          <a href="/agenda-salas<?= htmlspecialchars($n['link']) ?>" class="btn btn-sm btn-outline-primary">Ver</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <a href="/agenda-salas/event/index" class="btn btn-secondary mt-4">← Voltar ao calendário</a>
</div>
</body>
</html>
