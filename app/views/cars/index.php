<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agenda do Carro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <style>
    #calendar { max-width: 900px; margin: 40px auto; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-brand">Agenda do Carro</span>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <a class="nav-link" href="/agenda-salas/event/index">Ir para Agenda de Salas</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-4">
  <div id="calendar"></div>
</div>

<!-- Modal Agendamento Carro -->
<div class="modal fade" id="carModal" tabindex="-1" aria-labelledby="carModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="carForm" class="modal-content p-3 rounded-3 shadow-sm border-0">
      <div class="modal-header">
        <h5 class="modal-title" id="carModalLabel">Novo Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="car_id">

        <div class="mb-3">
          <label for="responsavel" class="form-label">Responsável</label>
          <input type="text" name="responsavel" id="responsavel" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="saida" class="form-label">Data/Hora de Saída</label>
          <input type="datetime-local" name="saida" id="saida" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="retorno" class="form-label">Data/Hora de Retorno</label>
          <input type="datetime-local" name="retorno" id="retorno" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="destino" class="form-label">Destino</label>
          <input type="text" name="destino" id="destino" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="motivo" class="form-label">Motivo</label>
          <textarea name="motivo" id="motivo" class="form-control" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <button type="button" id="deleteCarBtn" class="btn btn-danger me-auto" style="display: none;">Excluir</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const carModal = new bootstrap.Modal(document.getElementById('carModal'));
  const form = document.getElementById('carForm');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'pt-br',
    selectable: true,
    events: '../cars/all',

    select: function (info) {
      form.reset();
      document.getElementById('car_id').value = '';
      document.getElementById('saida').value = info.startStr + 'T08:00';
      document.getElementById('retorno').value = info.startStr + 'T18:00';
      document.getElementById('deleteCarBtn').style.display = 'none';
      carModal.show();
    },

    eventClick: function (info) {
      const event = info.event;
      axios.get(`../cars/getById?id=${event.id}`)
        .then(res => {
          const car = res.data;
          document.getElementById('car_id').value = car.id;
          document.getElementById('responsavel').value = car.responsavel;
          document.getElementById('saida').value = car.saida.replace(' ', 'T');
          document.getElementById('retorno').value = car.retorno.replace(' ', 'T');
          document.getElementById('destino').value = car.destino;
          document.getElementById('motivo').value = car.motivo;
          document.getElementById('deleteCarBtn').style.display = 'inline-block';
          carModal.show();
        });
    }
  });

  calendar.render();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    axios.post('../../../carevent/store', formData)
      .then(res => {
        if (res.data.success) {
          carModal.hide();
          calendar.refetchEvents();
        } else {
          alert(res.data.error || 'Erro ao salvar agendamento');
        }
      });
  });

  document.getElementById('deleteCarBtn').addEventListener('click', function () {
    const id = document.getElementById('car_id').value;
    if (confirm('Deseja realmente excluir este agendamento?')) {
      axios.post('../../../carevent/delete', { id })
        .then(res => {
          if (res.data.success) {
            carModal.hide();
            calendar.refetchEvents();
          } else {
            alert(res.data.error || 'Erro ao excluir');
          }
        });
    }
  });
});
</script>
</body>
</html>
