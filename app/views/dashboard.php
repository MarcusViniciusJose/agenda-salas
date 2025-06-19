<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Salas</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Agenda de Salas</h2>
        <div id='calendar'></div>
    </div>

    <!-- Modal de criação de evento -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="eventForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Evento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="title" class="form-control mb-2" placeholder="Título" required>
                        <select name="sala" class="form-select mb-2" required>
                            <option value="reuniao">Sala de Reunião</option>
                            <option value="treinamento">Sala de Treinamento</option>
                        </select>
                        <label>Início</label>
                        <input type="datetime-local" name="start" class="form-control mb-2" required>
                        <label>Fim</label>
                        <input type="datetime-local" name="end" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            events: '/event/all',
            eventDidMount: function(info) {
                if (info.event.extendedProps.sala === 'reuniao') {
                    info.el.style.backgroundColor = '#28a745';
                } else {
                    info.el.style.backgroundColor = '#007bff';
                }
            },
            dateClick: function(info) {
                let modal = new bootstrap.Modal(document.getElementById('eventModal'));
                document.querySelector('[name=start]').value = info.dateStr + 'T08:00';
                document.querySelector('[name=end]').value = info.dateStr + 'T09:00';
                modal.show();
            }
        });

        calendar.render();

        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();

            fetch('/event/store', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                    calendar.refetchEvents();
                }
            });
        });
    });
    </script>
</body>
</html>
