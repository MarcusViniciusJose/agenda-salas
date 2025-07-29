<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agenda de Salas</title>
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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">
    <!-- Marca/logo -->
    <a class="navbar-brand fw-bold" href="#">Agendamento de Salas</a>

    <!-- Botão do menu para dispositivos móveis -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Conteúdo da navbar -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Links do menu à esquerda -->
        <li class="nav-item">
          <a class="nav-link" href="/agenda-salas/event/index">Agenda de Salas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/agenda-salas/app/views/cars">Agenda do Carro</a>
        </li>
      </ul>

      <!-- Notificações e botão de logout à direita -->
      <ul class="navbar-nav ms-auto">
        <!-- Notificações -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notif-icon">
            🔔
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count">0</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" id="notif-list">
            <li><span class="dropdown-item">Sem notificações</span></li>
          </ul>
        </li>

        <!-- Logout -->
        <li class="nav-item">
          <a class="nav-link text-danger fw-semibold" href="../auth/logout">Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="container mt-4">
  <div id="calendar"></div>
</div>

<!-- Modal de Criação -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="eventForm" class="modal-content p-3 rounded-3 shadow-sm border-0">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Novo Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="event_id" name="event_id">
        <div class="mb-3">
          <label for="title" class="form-label">Título</label>
          <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
          <label for="start" class="form-label">Início</label>
          <input type="datetime-local" class="form-control" id="start" name="start" required>
        </div>
        <div class="mb-3">
          <label for="end" class="form-label">Fim</label>
          <input type="datetime-local" class="form-control" id="end" name="end" required>
        </div>
        <div class="mb-3">
          <label for="sala" class="form-label">Sala</label>
          <select class="form-select" id="sala" name="sala">
            <option value="reuniao">Sala de Reunião</option>
            <option value="treinamento">Sala de Treinamento</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="participants" class="form-label">Participantes</label>
          <select class="form-select" id="participants" name="participants[]" multiple="multiple" style="width: 100%"></select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <button type="button" class="btn btn-danger me-auto" id="deleteEventBtn" style="display: none;">
          Excluir Evento
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  const loggedUserId = <?= $_SESSION['user']['id'] ?? 'null' ?>;

  document.addEventListener('DOMContentLoaded', async function () {
    const calendarEl = document.getElementById('calendar');
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const form = document.getElementById('eventForm');

    $('#participants').select2({
      placeholder: 'Buscar participantes...',
      ajax: {
        url: '../user/search',
        dataType: 'json',
        delay: 250,
        data: params => ({ search: params.term }),
        processResults: data => ({ results: data }),
        cache: true
      }
    });

    const feriados = [];

    try {
      const response = await axios.get(`https://brasilapi.com.br/api/feriados/v1/${new Date().getFullYear()}`);
      response.data.forEach(feriado => feriados.push(feriado.date)); // Ex: '2025-04-21'
    } catch (error) {
      console.error('Erro ao carregar feriados nacionais:', error);
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'pt-br',
      selectable: true,
      events: '../event/all',

      dayCellDidMount: function(info) {
        const day = info.date.getDay(); // 0 = domingo, 6 = sábado
        const dateStr = info.date.toISOString().split('T')[0];

        if (day === 0 || day === 6) {
          info.el.style.backgroundColor = '#f8d7da'; // Fim de semana
        }

        if (feriados.includes(dateStr)) {
          info.el.style.backgroundColor = '#ffeeba'; // Feriado nacional
          info.el.style.fontWeight = 'bold';
        }
      },

      select: function (info) {
        form.reset();
        $('#participants').val(null).trigger('change');
        document.getElementById('event_id').value = '';
        document.getElementById('start').value = info.startStr + 'T08:00';
        document.getElementById('end').value = info.startStr + 'T09:00';
        document.getElementById('deleteEventBtn').style.display = 'none';
        eventModal.show();
      },

      eventClick: function(info) {
        const event = info.event;
        axios.get(`../event/getByIdAjax?id=${event.id}`)
        .then(res => {
          const data = res.data;
          if (!data.event) {
            alert("Erro ao carregar dados do evento.");
            return;
          }

          document.getElementById('event_id').value = data.event.id;
          document.getElementById('title').value = data.event.title;
          document.getElementById('start').value = data.event.start.replace(' ', 'T');
          document.getElementById('end').value = data.event.end.replace(' ', 'T');
          document.getElementById('sala').value = data.event.sala;

          const participantsSelect = $('#participants');
          participantsSelect.val(null).trigger('change');

          if (Array.isArray(data.participants)) {
            data.participants.forEach(id => {
              const option = new Option('Carregando...', id, true, true);
              participantsSelect.append(option);
            });
          }

          participantsSelect.trigger('change');

          if (data.event.created_by == loggedUserId) {
            document.getElementById('deleteEventBtn').style.display = 'inline-block';
          } else {
            document.getElementById('deleteEventBtn').style.display = 'none';
          }

          eventModal.show();
        });
      }
    });

    calendar.render();

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(form);

      axios.post('../event/store', formData)
        .then(res => {
          if (res.data.success) {
            eventModal.hide();
            calendar.refetchEvents();
          } else {
            alert(res.data.error || 'Erro ao salvar evento');
          }
        })
        .catch(err => {
          if (err.response?.data?.error) {
            alert(err.response.data.error);
          } else {
            alert('Erro ao salvar evento');
          }
        });
    });

    document.getElementById('deleteEventBtn').addEventListener('click', function () {
      const eventId = document.getElementById('event_id').value;
      if (confirm('Tem certeza que deseja excluir este evento?')) {
        axios.post('../event/delete', { id: eventId })
        .then(res => {
          if (res.data.success) {
            alert('Evento excluído com sucesso!');
            eventModal.hide();
            calendar.refetchEvents();
          } else {
            alert(res.data.error || 'Erro ao excluir evento.');
          }
        })
        .catch(err => {
          console.error(err);
          alert('Erro ao excluir evento.');
        });
      }
    });
  });

  function fetchNotifications() {
    axios.get('../notification/get')
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
