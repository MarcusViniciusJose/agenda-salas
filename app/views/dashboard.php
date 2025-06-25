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
<div class="container">
  <h2 class="mt-4">Agendamento de Salas</h2>
  <div id="calendar"></div>
</div>

<!-- Modal de Criação -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="eventForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">Novo Evento</h5>
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
      </div>
    </form>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    const form = document.getElementById('eventForm');
    const title = document.getElementById('title');
    const start = document.getElementById('start');
    const end = document.getElementById('end');
    const sala = document.getElementById('sala');
    const participants = document.getElementById('participants');

    
    $('#participants').select2({
      placeholder: 'Buscar participantes...',
      ajax: {
        url: '../user/search',
        dataType: 'json',
        delay: 250,
        data: params => ({ term: params.term }),
        processResults: data => ({
          results: data.map(user => ({ id: user.id, text: user.nome + ' (' + user.email + ')' }))
        }),
        cache: true
      }
    });

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'pt-br',
      selectable: true,
      events: '../event/all',
      select: function (info) {
        form.reset();
        $('#participants').val(null).trigger('change');
        start.value = info.startStr + 'T08:00';
        end.value = info.startStr + 'T09:00';
        eventModal.show();
      },
      eventClick: function(info) {
        const event = info.event;
        alert(`Evento: ${event.title}\nInício: ${event.start}\nFim: ${event.end}`);
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
            alert('Erro ao salvar evento');
          }
        })
        .catch(err => console.error(err));
    });
  });
</script>
</body>
</html>
