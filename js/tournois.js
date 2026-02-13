(function() {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Tournois.js loaded successfully');
    
        const track = document.getElementById('roundTrack');
        const prevBtn = document.getElementById('prevRound');
        const nextBtn = document.getElementById('nextRound');
        const dots = document.querySelectorAll('.dot');
        const roundCards = track.children; 
        const totalRounds = roundCards.length; 
        let currentIndex = 0; 
        function updateCarousel(index) {
            if (index < 0) index = 0;
            if (index >= totalRounds) index = totalRounds - 1;
            const translateX = -index * 100; 
            track.style.transform = `translateX(${translateX}%)`;
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
            
            if (prevBtn) prevBtn.disabled = (index === 0);
            if (nextBtn) nextBtn.disabled = (index === totalRounds - 1);
            
            currentIndex = index;
            console.log('Current round index:', index);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                updateCarousel(currentIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                updateCarousel(currentIndex + 1);
            });
        }

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', function() {
                updateCarousel(idx);
            });
        });

        updateCarousel(0);

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                updateCarousel(currentIndex);
            }, 150);
        });
    });
})();