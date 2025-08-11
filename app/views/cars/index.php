<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agenda do Carro</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <style>
    #calendar {
      max-width: 900px;
      margin: 40px auto;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Agendamento do Carro</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/agenda-salas/event/index">Agenda de Salas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/agenda-salas/app/views/cars">Agenda do Carro</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notif-icon">
            🔔
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count">0</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" id="notif-list">
            <li><span class="dropdown-item">Sem notificações</span></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link text-danger fw-semibold" href="../../../auth/logout">Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="container mt-4">
  <div id="calendar"></div>
</div>

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
          <label for="title" class="form-label">Responsável *</label>
          <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="start" class="form-label">Data/Hora de Saída *</label>
          <input type="datetime-local" name="start" id="start" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="end" class="form-label">Data/Hora de Retorno *</label>
          <input type="datetime-local" name="end" id="end" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Destino *</label>
          <input type="text" name="description" id="description" class="form-control" required>
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
document.addEventListener('DOMContentLoaded', async function () {
  const calendarEl = document.getElementById('calendar');
  const carModal = new bootstrap.Modal(document.getElementById('carModal'));
  const form = document.getElementById('carForm');

  const feriados = [];

  try {
    const response = await axios.get(`https://brasilapi.com.br/api/feriados/v1/${new Date().getFullYear()}`);
    response.data.forEach(feriado => feriados.push(feriado.date)); 
  } catch (error) {
    console.error('Erro ao carregar feriados nacionais:', error);
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'pt-br',
    selectable: true,
    events: '../../../carevent/all',

    dayCellDidMount: function(info) {
      const day = info.date.getDay(); 
      const dateStr = info.date.toISOString().split('T')[0];

      if (day === 0 || day === 6) {
        info.el.style.backgroundColor = '#f8d7da'; 
      }

      if (feriados.includes(dateStr)) {
        info.el.style.backgroundColor = '#ffeeba'; 
        info.el.style.fontWeight = 'bold';
      }
    },

    select: function (info) {
      form.reset();
      document.getElementById('car_id').value = '';
      document.getElementById('start').value = info.startStr + 'T08:00';
      document.getElementById('end').value = info.startStr + 'T18:00';
      document.getElementById('deleteCarBtn').style.display = 'none';
      carModal.show();
    },

    eventClick: function (info) {
      const event = info.event;
      console.log('Evento clicado:', event.id);

      axios.get(`../../../carevent/getByIdAjax?id=${event.id}`)
        .then(res => {
          const car = res.data;
          document.getElementById('car_id').value = car.event.id;
          document.getElementById('title').value = car.event.title;
          document.getElementById('start').value = car.event.start.replace(' ', 'T');
          document.getElementById('end').value = car.event.end.replace(' ', 'T');
          document.getElementById('description').value = car.event.description;
          document.getElementById('deleteCarBtn').style.display = 'inline-block';
          carModal.show();
        });
    }
  });

  calendar.render();

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    console.log('Formulário enviado');

    axios.post('../../../carevent/store', formData)
      .then(res => {
        console.log('Resposta:', res);
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

function fetchNotifications() {
  axios.get('../../../notification/get')
    .then(res => {
      const notifList = document.getElementById('notif-list');
      const notifCount = document.getElementById('notif-count');
      notifList.innerHTML = '';

      if (!Array.isArray(res.data) || res.data.length === 0) {
        notifList.innerHTML = '<li><span class="dropdown-item">Sem notificações</span></li>';
        notifCount.textContent = '0';
      } else {
        notifCount.textContent = res.data.length;

        res.data.forEach(n => {
          const li = document.createElement('li');
          li.innerHTML = `
            <a class="dropdown-item" href="../notification/markAndRedirect?id=${n.id}&link=${encodeURIComponent('/agenda-salas' + (n.link || '/event/index'))}">
              ${n.message}
            </a>`;
          notifList.appendChild(li);
        });
      }

      const divider = document.createElement('li');
      divider.innerHTML = `<hr class="dropdown-divider">`;
      notifList.appendChild(divider);

      const viewAll = document.createElement('li');
      viewAll.innerHTML = `<a class="dropdown-item text-center" href="/agenda-salas/notification/history">Ver todas as notificações</a>`;
      notifList.appendChild(viewAll);
    })
    .catch(err => {
      console.error("Erro ao buscar notificações:", err);
    });
}

setInterval(fetchNotifications, 10000);
fetchNotifications();
</script>

</body>
</html>
