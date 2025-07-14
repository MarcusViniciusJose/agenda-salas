<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Notificações</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3>🔔 Histórico de Notificações</h3>
  <hr>

  <?php if (empty($notifications)): ?>
    <p>Você ainda não recebeu nenhuma notificação.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($notifications as $n): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center <?= $n['read_at'] ? 'text-muted' : 'fw-bold' ?>">
          <span><?= htmlspecialchars($n['message']) ?></span>
          <a href="<?= htmlspecialchars($n['link']) ?>" class="btn btn-sm btn-outline-primary">Ver</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <a href="/agenda-salas/event/index" class="btn btn-secondary mt-4">← Voltar ao calendário</a>
</div>
</body>
</html>
