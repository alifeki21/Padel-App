document.addEventListener('DOMContentLoaded', function() {
    fetch('../php/get_profile.php')
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            const user = result.user;
            document.getElementById('userName').textContent = `${user.first_name} ${user.last_name}`;
            document.getElementById('userEmailTag').textContent = user.email;
            
            document.getElementById('firstName').textContent = user.first_name;
            document.getElementById('lastName').textContent = user.last_name;
            document.getElementById('email').textContent = user.email;
            document.getElementById('phone').textContent = user.phone || 'Non renseigné';
            document.getElementById('skillLevel').textContent = user.skill_level;
            
            const positionMap = { 'left': 'Gauche', 'right': 'Droite', 'both': 'Les deux' };
            document.getElementById('position').textContent = positionMap[user.preferred_position] || user.preferred_position;
            
            const handMap = { 'left': 'Gaucher', 'right': 'Droitier' };
            document.getElementById('hand').textContent = handMap[user.playing_hand] || user.playing_hand;
        } else {
            window.location.href = 'login.html';
        }
    })
    .catch(error => {
        console.error('Error fetching profile:', error);
        alert('Erreur lors du chargement du profil.');
    });
});
