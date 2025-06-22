<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agenda de Salas</title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    #calendar { max-width: 900px; margin: 40px auto; }
  </style>
</head>
<body>

  <div class="container">
    <h2 class="mt-4">Agendamento de Salas</h2>
    <div id='calendar'></div>
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
            <select multiple class="form-select" id="participants" name="participants[]">
              <!-- Estes valores podem ser populados dinamicamente -->
              <option value="1">Marcus José</option>
              <option value="2">Usuário Exemplo</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
      const form = document.getElementById('eventForm');

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        locale: 'pt-br',
        select: function(info) {
          document.getElementById('start').value = info.startStr + 'T08:00';
          document.getElementById('end').value = info.endStr + 'T09:00';
          eventModal.show();
        },
        events: {
          url: '../event/all',
          method: 'GET',
          failure: () => alert('Erro ao carregar eventos')
        }
      });

      calendar.render();

      form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        fetch('../event/store', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            eventModal.hide();
            calendar.refetchEvents();
          } else {
            alert('Erro ao salvar evento');
          }
        });
      });
    });
  </script>
</body>
</html>
