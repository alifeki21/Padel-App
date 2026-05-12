CREATE DATABASE IF NOT EXISTS padel_db;
USE padel_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    skill_level DECIMAL(3,1) NOT NULL,
    preferred_position ENUM('left', 'right', 'both') NOT NULL,
    playing_hand ENUM('right', 'left') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    court_number INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    duration INT NOT NULL, -- in minutes
    price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    max_teams INT NOT NULL,
    current_teams INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    level VARCHAR(50),
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming'
);

CREATE TABLE IF NOT EXISTS tournament_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    user_id INT NOT NULL,
    partner_name VARCHAR(100),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(100),
    type VARCHAR(50) NOT NULL,
    rating INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS player_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reported_player_name VARCHAR(100) NOT NULL,
    reported_player_phone VARCHAR(20),
    report_type VARCHAR(50) NOT NULL,
    indicated_level DECIMAL(3,1),
    real_level DECIMAL(3,1),
    reason TEXT,
    behavior_details TEXT,
    reporter_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Sample data for tournaments
INSERT INTO tournaments (name, description, event_date, max_teams, current_teams, price, level, status) VALUES
('Tournoi Hello Padel', 'Tournoi amical pour tous niveaux', '2026-03-15', 16, 4, 60.00, 'P25/P50/P100', 'upcoming'),
('Tournoi Amical', 'Compétition mixte de haut niveau', '2026-03-22', 8, 2, 60.00, 'P1000', 'upcoming'),
('Tournoi Féminin', 'Réservé aux joueuses de padel', '2026-04-05', 16, 0, 60.00, 'P25/50/100', 'upcoming');
