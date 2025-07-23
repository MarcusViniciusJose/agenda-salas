<!-- View simplificada -->
<?php include '../layout/header.php'; ?>

<div class="container mt-4">
    <h2>Agenda do Carro</h2>
    <div id="calendar"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        events: '/vehicle/all',
        selectable: true,
        editable: true,
        eventClick: function(info) {
            // abrir modal de edição
        },
        select: function(info) {
            // abrir modal de criação
        }
    });
    calendar.render();
});
</script>

<?php include '../app/views/layout/footer.php'; ?>
