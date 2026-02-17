let reservationState = {
    currentStep: 1,
    selectedCourt: null,
    selectedDate: null,
    selectedTime: null,
    selectedDuration: 1.5,
    playerDetails: {
        name: '',
        email: '',
        phone: '',
        players: '4',
        specialRequests: ''
    }
};

var courtPrice = 25;

const courtNames = {
    1: 'Court 1 - Cupra',
    2: 'Court 2 - Dechatlon',
    3: 'Court 3 - Codeforces'
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    initializeDatePicker();
    initializeTimeSlots();
    initializeMobileMenu();
    loadSavedState();
    updateStepDisplay();
    updateSummary();
    
    hideDurationSelector();
    
    addFormInputListeners();
    
    setupButtonListeners();
    
    setupReturnButton();
});

function setupReturnButton() {
    const returnBtn = document.getElementById('returnToStep1');
    if (returnBtn) {
        returnBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Returning to step 1');
            resetReservation();
            goToStep(1);
        });
    }
}

function goToStep(step) {
    reservationState.currentStep = step;
    updateStepDisplay();
    
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        successMessage.classList.add('hidden');
    }
}

function resetReservation() {
    reservationState.selectedCourt = null;
    reservationState.selectedDate = null;
    reservationState.selectedTime = null;
    reservationState.playerDetails = {
        name: '',
        email: '',
        phone: '',
        players: '4',
        specialRequests: ''
    };
    
    document.querySelectorAll('.court-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    const dateInput = document.getElementById('reservation-date');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
        reservationState.selectedDate = dateInput.value;
    }
    
    reservationState.selectedTime = null;
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const specialRequests = document.getElementById('special-requests');
    const playersSelect = document.getElementById('players');
    const termsCheckbox = document.getElementById('terms');
    
    if (nameInput) nameInput.value = '';
    if (emailInput) emailInput.value = '';
    if (phoneInput) phoneInput.value = '';
    if (specialRequests) specialRequests.value = '';
    if (playersSelect) playersSelect.value = '4';
    if (termsCheckbox) termsCheckbox.checked = false;
    
    updateSummary();
}

function setupButtonListeners() {
    console.log('Setting up button listeners');
    
    document.querySelectorAll('.btn-select-court').forEach((button, index) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const courtId = index + 1;
            if (courtId == 3){
                courtPrice = 15 ;
            }else{
                courtPrice = 25 ;
            }
            console.log('Court selected:', courtId);
            selectCourt(courtId);
        });
    });
    
    const backToStep1 = document.querySelector('#step2 .btn-secondary');
    if (backToStep1) {
        backToStep1.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Back to step 1');
            goToStep(1);
        });
    }
    
    const continueToStep3 = document.querySelector('#step2 .btn-primary');
    if (continueToStep3) {
        continueToStep3.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Continue from step 2 to step 3');
            if (validateStep2()) {
                goToStep(3);
            }
        });
    }
    
    const backToStep2 = document.querySelector('#step3 .btn-secondary');
    if (backToStep2) {
        backToStep2.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Back to step 2');
            goToStep(2);
        });
    }
    
    const continueToStep4 = document.getElementById('continueToStep4') || document.querySelector('#step3 .btn-primary');
    if (continueToStep4) {
        continueToStep4.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Continue from step 3 to step 4');
            if (validateStep3()) {
                goToStep(4);
            }
        });
    }
    
    const backToStep3 = document.querySelector('#step4 .btn-secondary');
    if (backToStep3) {
        backToStep3.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Back to step 3');
            goToStep(3);
        });
    }
    
    const confirmBtn = document.querySelector('#step4 .btn-primary');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Confirm booking');
            confirmBooking();
        });
    }
}

function validateStep2() {
    console.log('Validating step 2');
    
    if (!reservationState.selectedDate) {
        alert('Please select a date');
        return false;
    }
    
    if (!reservationState.selectedTime) {
        alert('Please select a time slot');
        return false;
    }
    
    console.log('Step 2 validation passed');
    return true;
}

function validateStep3() {
    console.log('Validating step 3');
    
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const termsCheckbox = document.getElementById('terms');
    
    const name = nameInput ? nameInput.value.trim() : '';
    const email = emailInput ? emailInput.value.trim() : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const terms = termsCheckbox ? termsCheckbox.checked : false;
    
    console.log('Form values:', { name, email, phone, terms });
    
    if (!name) {
        alert('Please enter your full name');
        if (nameInput) nameInput.focus();
        return false;
    }
    
    if (!email) {
        alert('Please enter your email address');
        if (emailInput) emailInput.focus();
        return false;
    }
    
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address');
        if (emailInput) emailInput.focus();
        return false;
    }
    
    if (!phone) {
        alert('Please enter your phone number');
        if (phoneInput) phoneInput.focus();
        return false;
    }
    
    if (!terms) {
        alert('Please agree to the Terms of Service and Privacy Policy');
        return false;
    }
    
    reservationState.playerDetails = {
        name: name,
        email: email,
        phone: phone,
        players: document.getElementById('players') ? document.getElementById('players').value : '4',
        specialRequests: document.getElementById('special-requests') ? document.getElementById('special-requests').value : ''
    };
    
    updateSummary();
    console.log('Step 3 validation passed');
    return true;
}

function selectCourt(courtId) {
    console.log('Selecting court:', courtId);
    
    document.querySelectorAll('.court-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    const selectedCard = document.querySelector(`[data-court="${courtId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    reservationState.selectedCourt = courtId;
    updateSummary();
    
    goToStep(2);
}

function addFormInputListeners() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const playersSelect = document.getElementById('players');
    
    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            reservationState.playerDetails.name = e.target.value;
        });
    }
    
    if (emailInput) {
        emailInput.addEventListener('input', function(e) {
            reservationState.playerDetails.email = e.target.value;
        });
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            reservationState.playerDetails.phone = e.target.value;
        });
    }
    
    if (playersSelect) {
        playersSelect.addEventListener('change', function(e) {
            reservationState.playerDetails.players = e.target.value;
            updateSummary();
        });
    }
}

function hideDurationSelector() {
    const durationSelector = document.querySelector('.duration-selector');
    if (durationSelector) {
        durationSelector.style.display = 'none';
    }
}

function initializeMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    
    if (menuBtn && navMenu) {
        menuBtn.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
}

function initializeDatePicker() {
    const dateInput = document.getElementById('reservation-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.value = tomorrow.toISOString().split('T')[0];
        reservationState.selectedDate = dateInput.value;
        
        dateInput.addEventListener('change', function(e) {
            reservationState.selectedDate = e.target.value;
            updateTimeSlots();
        });
    }
}

function initializeTimeSlots() {
    updateTimeSlots();
}

function updateTimeSlots() {
    const slotGrid = document.getElementById('time-slots');
    if (!slotGrid) return;
    
    slotGrid.innerHTML = '';
    
    for (let hour = 0; hour < 24; hour += 1.5) {
        const startHour = Math.floor(hour);
        const startMinute = (hour % 1) * 60;
        const timeString = `${startHour.toString().padStart(2, '0')}:${startMinute.toString().padStart(2, '0')}`;
        
        const slot = document.createElement('div');
        slot.className = 'time-slot';
        slot.textContent = timeString;
        
        const isBooked = 0;
        
        if (isBooked) {
            slot.classList.add('booked');
        } else {
            slot.addEventListener('click', function() {
                document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                reservationState.selectedTime = timeString;
                updateSummary();
            });
        }
        
        slotGrid.appendChild(slot);
    }
}

function updateStepDisplay() {
    console.log('Showing step:', reservationState.currentStep);
    
    document.querySelectorAll('.reservation-step').forEach(step => {
        step.classList.add('hidden');
    });
    
    const currentStepElement = document.getElementById(`step${reservationState.currentStep}`);
    if (currentStepElement) {
        currentStepElement.classList.remove('hidden');
    }
    
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        successMessage.classList.add('hidden');
    }
    
    document.querySelectorAll('.progress-step').forEach((step, index) => {
        if (index + 1 <= reservationState.currentStep) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
    
    if (reservationState.currentStep === 4) {
        updateSummary();
    }
}

function updateSummary() {
    const courtSummary = document.getElementById('summary-court');
    if (courtSummary && reservationState.selectedCourt) {
        courtSummary.textContent = courtNames[reservationState.selectedCourt] || 'Court ' + reservationState.selectedCourt;
    }
    
    const dateSummary = document.getElementById('summary-date');
    if (dateSummary && reservationState.selectedDate) {
        const date = new Date(reservationState.selectedDate);
        dateSummary.textContent = date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    const timeSummary = document.getElementById('summary-time');
    if (timeSummary) {
        timeSummary.textContent = reservationState.selectedTime || 'Not selected';
    }
    
    const durationSummary = document.getElementById('summary-duration');
    if (durationSummary) {
        durationSummary.textContent = '90 minutes';
    }
    
    const playersSummary = document.getElementById('summary-players');
    if (playersSummary && reservationState.playerDetails.players) {
        playersSummary.textContent = reservationState.playerDetails.players;
    }
    
    const nameSummary = document.getElementById('summary-name');
    if (nameSummary) {
        nameSummary.textContent = reservationState.playerDetails.name || '-';
    }
    
    const totalSummary = document.getElementById('summary-total');
    if (totalSummary) {
        totalSummary.textContent = `€${courtPrice*reservationState.playerDetails.players}.00`;
    }
}

function confirmBooking() {
    console.log('Booking confirmed:', reservationState);
    
    document.querySelectorAll('.reservation-step').forEach(step => {
        step.classList.add('hidden');
    });
    
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        successMessage.classList.remove('hidden');
    }
    
    alert('Booking confirmed! Thank you for your reservation.');
}

function loadSavedState() {
    const saved = localStorage.getItem('padelReservation');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            reservationState = {...reservationState, ...parsed};
            reservationState.selectedDuration = 1.5;
        } catch (e) {
            console.error('Failed to load saved state', e);
        }
    }
}

function saveState() {
    localStorage.setItem('padelReservation', JSON.stringify(reservationState));
}

setInterval(saveState, 5000);