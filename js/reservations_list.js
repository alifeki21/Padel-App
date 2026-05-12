document.addEventListener('DOMContentLoaded', function() {
    const listContainer = document.getElementById('reservationsList');

    fetch('../php/get_reservations.php')
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            const reservations = result.reservations;
            
            if (reservations.length === 0) {
                listContainer.innerHTML = `
                    <div class="no-res">
                        <i class="fas fa-calendar-times fa-3x mb-3 text-muted"></i>
                        <p>Vous n'avez aucune réservation pour le moment.</p>
                        <a href="reservation.html" class="btn btn-primary mt-3">Réserver un terrain</a>
                    </div>
                `;
                return;
            }

            let html = '';
            reservations.forEach(res => {
                const date = new Array(res.reservation_date).join(''); // Keep raw date or format it
                const statusClass = `status-${res.status.toLowerCase()}`;
                const statusText = res.status.charAt(0).toUpperCase() + res.status.slice(1);

                html += `
                    <div class="res-card">
                        <div class="res-info">
                            <h4>Terrain #${res.court_number}</h4>
                            <p><i class="far fa-calendar-alt me-2"></i> ${res.reservation_date}</p>
                            <p><i class="far fa-clock me-2"></i> ${res.reservation_time} (${res.duration} min)</p>
                            <p><i class="fas fa-tag me-2"></i> ${res.price} DT</p>
                        </div>
                        <div class="res-status ${statusClass}">
                            ${statusText}
                        </div>
                    </div>
                `;
            });
            listContainer.innerHTML = html;
        } else {
            window.location.href = 'login.html';
        }
    })
    .catch(error => {
        console.error('Error fetching reservations:', error);
        listContainer.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des réservations.</div>';
    });
});
