document.addEventListener('DOMContentLoaded', function() {
    // 1. Contact Form Handler
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('subject', document.getElementById('subject').value);
            formData.append('message', document.getElementById('message').value);

            const btn = contactForm.querySelector('button');
            const originalText = btn.textContent;
            btn.textContent = 'Envoi...';
            btn.disabled = true;

            fetch('../php/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') contactForm.reset();
            })
            .catch(err => console.error(err))
            .finally(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            });
        });
    }

    // 2. Feedback Form Handler
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm && window.location.pathname.includes('feedback.html')) {
        let selectedRating = 5;
        const evalItems = document.querySelectorAll('.eval_item');
        
        evalItems.forEach(item => {
            item.addEventListener('click', function() {
                evalItems.forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                selectedRating = this.getAttribute('data-eval');
            });
        });

        feedbackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('email', document.getElementById('email').value);
            formData.append('type', document.getElementById('feedbackType').value);
            formData.append('rating', selectedRating);
            formData.append('message', document.getElementById('message').value);

            const btn = feedbackForm.querySelector('button');
            btn.disabled = true;

            fetch('../php/feedback.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') window.location.href = 'acceuil.html';
            })
            .catch(err => console.error(err))
            .finally(() => btn.disabled = false);
        });
    }

    // 3. Report Player Handler
    const reportForm = document.getElementById('feedbackForm'); // Same ID used in reportlevel.html
    if (reportForm && window.location.pathname.includes('reportlevel.html')) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            // Note: HTML uses IDs with spaces, which is unusual but we must match them
            formData.append('player_name', document.getElementById('Nom et Prénom').value);
            formData.append('player_phone', document.getElementById('Numtel').value);
            formData.append('report_type', document.getElementById('feedbackType').value);
            formData.append('indicated_level', document.getElementById('Niveau indique').value);
            formData.append('real_level', document.getElementById('Niveau reel').value);
            formData.append('reason', document.getElementById('niv').value);
            formData.append('behavior_details', document.getElementById('comp').value);

            const btn = reportForm.querySelector('button');
            btn.disabled = true;

            fetch('../php/report_player.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') window.location.href = 'acceuil.html';
            })
            .catch(err => console.error(err))
            .finally(() => btn.disabled = false);
        });
    }
});
