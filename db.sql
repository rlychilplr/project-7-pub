CREATE TABLE players (
    player_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP
);

CREATE TABLE characters (
    character_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    base_hp INT NOT NULL
);

CREATE TABLE cards (
    card_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    type VARCHAR(20) CHECK (
        type IN ('Attack', 'Skill', 'Power', 'Status', 'Curse')
    ),
    rarity VARCHAR(20) CHECK (
        rarity IN ('Common', 'Uncommon', 'Rare', 'Special')
    ),
    energy_cost INT NOT NULL,
    description TEXT NOT NULL,
    character_id INT,
    is_starter BOOLEAN DEFAULT FALSE,
    starter_quantity INT DEFAULT 0,
    FOREIGN KEY (character_id) REFERENCES characters (character_id)
);

CREATE TABLE relics (
    relic_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    rarity VARCHAR(20) CHECK (
        rarity IN (
            'Common',
            'Uncommon',
            'Rare',
            'Boss',
            'Shop',
            'Event'
        )
    ),
    effect_type VARCHAR(50) NOT NULL
);

CREATE TABLE enemies (
    enemy_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    hp INT NOT NULL,
    type VARCHAR(20) CHECK (type IN ('Normal', 'Elite', 'Boss'))
);

CREATE TABLE runs (
    run_id INT PRIMARY KEY AUTO_INCREMENT,
    player_id INT NOT NULL,
    character_id INT NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP,
    floor_reached INT,
    victory BOOLEAN,
    score INT,
    ascension_level INT DEFAULT 0,
    FOREIGN KEY (player_id) REFERENCES players (player_id),
    FOREIGN KEY (character_id) REFERENCES characters (character_id)
);

CREATE TABLE run_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    run_id INT NOT NULL,
    floor INT,
    detail_type VARCHAR(20) CHECK (detail_type IN ('card', 'relic', 'encounter')),
    card_id INT,
    relic_id INT,
    encounter_type VARCHAR(20) CHECK (
        encounter_type IN (
            'Monster',
            'Elite',
            'Boss',
            'Rest',
            'Merchant',
            'Treasure',
            'Event'
        )
    ),
    quantity INT DEFAULT 1,
    result VARCHAR(20) CHECK (
        result IN ('Victory', 'Defeat', 'Fled', 'Completed')
    ),
    FOREIGN KEY (run_id) REFERENCES runs (run_id),
    FOREIGN KEY (card_id) REFERENCES cards (card_id),
    FOREIGN KEY (relic_id) REFERENCES relics (relic_id)
);
