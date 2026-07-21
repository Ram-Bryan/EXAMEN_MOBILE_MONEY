CREATE TABLE epargne(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER,
    montant REAL,
    pourcentage REAL NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

INSERT INTO epargne(client_id, montant, pourcentage, date_modif) VALUES 
(1, 0, 0.5, '2026-01-01 00:00:00'),
(2, 0,  0.2, '2026-01-01 00:00:00'),
(3, 0,  0.3, '2026-01-01 00:00:00'),
(4, 0,  0.5, '2026-01-01 00:00:00'),