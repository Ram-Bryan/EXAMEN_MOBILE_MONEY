-- ============================================================
-- SCRIPT COMPLET - BASE DE DONNÉES MOBILE MONEY
-- ============================================================

-- Active le support des clés étrangères dans SQLite
PRAGMA foreign_keys = ON;

-- ============================================================
-- 1. CRÉATION DES TABLES
-- ============================================================

-- Table : Configurations des préfixes de l'opérateur

CREATE TABLE operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT,
    est_notre_operateur INTEGER NOT NULL DEFAULT 0, -- 1 = c'est nous, 0 = opérateur externe
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE historique_operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_prefixe_id INTEGER NOT NULL,
    prefixe TEXT NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_prefixe_id) REFERENCES operateur_prefixes(id)
);

-- Table : Types d'opérations
CREATE TABLE types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    nom TEXT NOT NULL
);

CREATE TABLE commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_destination_id INTEGER NOT NULL, -- l'opérateur externe concerné
    FOREIGN KEY (operateur_destination_id) REFERENCES operateur_prefixes(id)
);

CREATE TABLE commissions_historique (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commission_id INTEGER NOT NULL,
    pourcentage REAL NOT NULL, -- ex: 1.5 pour 1.5%
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commission_id) REFERENCES commissions(id)
);

-- Table : Barèmes des frais
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    operateur_id INTEGER NOT NULL,
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id),
    FOREIGN KEY (operateur_id) REFERENCES operateur_prefixes(id)
);

-- Table : Historique des barèmes
CREATE TABLE baremes_frais_historique (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    bareme_id INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL,
    frais_fixe REAL DEFAULT 0.0,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bareme_id) REFERENCES baremes_frais(id)
);

-- Table : Administrateur
CREATE TABLE admin(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table : Clients
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    telephone TEXT NOT NULL UNIQUE,
    code TEXT NOT NULL UNIQUE,
    operateur_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_id) REFERENCES operateur_prefixes(id)
);

-- Table : Transactions
CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    expediteur_id INTEGER,
    destinataire_id INTEGER,
    montant_brut REAL NOT NULL,
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id),
    FOREIGN KEY (expediteur_id) REFERENCES clients(id),
    FOREIGN KEY (destinataire_id) REFERENCES clients(id),
    CHECK (montant_brut > 0)
);

-- ============================================================
-- 2. INDEX
-- ============================================================

CREATE INDEX idx_transactions_expediteur ON transactions(expediteur_id);
CREATE INDEX idx_transactions_destinataire ON transactions(destinataire_id);
CREATE INDEX idx_transactions_created ON transactions(date_transaction);

-- ============================================================
-- 3. VUES
-- ============================================================

-- Vue : Opérateur de chaque transaction
CREATE VIEW v_transactions_operateur AS
SELECT
    tr.id AS transaction_id,
    tr.type_operation_id,
    tr.expediteur_id,
    tr.destinataire_id,
    tr.montant_brut,
    tr.date_transaction,
    COALESCE(ce.operateur_id, cd.operateur_id) AS operateur_id
FROM transactions tr
LEFT JOIN clients ce ON ce.id = tr.expediteur_id
LEFT JOIN clients cd ON cd.id = tr.destinataire_id;

-- Vue : Frais de chaque transaction
CREATE VIEW v_transactions_frais AS
SELECT
    tc.transaction_id,
    tc.type_operation_id,
    tc.expediteur_id,
    tc.destinataire_id,
    tc.montant_brut,
    tc.date_transaction,
    tc.operateur_id,
    (
        SELECT h.frais_fixe
        FROM baremes_frais b
        JOIN baremes_frais_historique h ON h.bareme_id = b.id
        WHERE b.type_operation_id = tc.type_operation_id
          AND b.operateur_id      = tc.operateur_id
          AND tc.montant_brut >= h.montant_min
          AND (h.montant_max IS NULL OR tc.montant_brut <= h.montant_max)
          AND h.date_modif = (
              SELECT MAX(h2.date_modif)
              FROM baremes_frais_historique h2
              WHERE h2.bareme_id = h.bareme_id
                AND h2.date_modif <= tc.date_transaction
          )
        LIMIT 1
    ) AS frais_applique
FROM v_transactions_operateur tc;

-- ============================================================
-- 4. INSERTIONS DES DONNÉES
-- ============================================================

-- 4.1 Opérateurs
INSERT INTO operateur_prefixes (id, prefixe) VALUES 
(1, '033'),
(2, '034'),
(3, '037'),
(4, '038');

-- 4.2 Types d'opérations
INSERT INTO types_operation (id, code, nom) VALUES 
(1, 'DEPOT', 'Dépôt'),
(2, 'RETRAIT', 'Retrait'),
(3, 'TRANSFERT', 'Transfert');

-- 4.3 Barèmes (12 combinaisons)
INSERT INTO baremes_frais (id, type_operation_id, operateur_id) VALUES
-- DEPOT (type=1)
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
-- RETRAIT (type=2)
(5, 2, 1),
(6, 2, 2),
(7, 2, 3),
(8, 2, 4),
-- TRANSFERT (type=3)
(9, 3, 1),
(10, 3, 2),
(11, 3, 3),
(12, 3, 4);

-- 4.4 Historique des frais - DÉPÔT (barèmes 1 à 4)
-- Opérateur 1 (033) : 0.5% du montant, min 100 Ar
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(1, 0, 100000, 100),
(1, 100001, NULL, 500);

-- Opérateur 2 (034) : 1% du montant, min 200 Ar
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(2, 0, 100000, 200),
(2, 100001, NULL, 1000);

-- Opérateur 3 (037) : 0.75% du montant, min 150 Ar
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(3, 0, 100000, 150),
(3, 100001, NULL, 750);

-- Opérateur 4 (038) : Frais fixes 100 Ar
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(4, 0, NULL, 100);

-- 4.5 Historique des frais - RETRAIT (barèmes 5 à 8)
-- Opérateur 1 (033) : 1% → 2% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(5, 0, 5000, 50),
(5, 5001, 50000, 500),
(5, 50001, 200000, 1000),
(5, 200001, NULL, 2000);

-- Opérateur 2 (034) : 0.5% → 1.5% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(6, 0, 5000, 25),
(6, 5001, 50000, 250),
(6, 50001, 200000, 750),
(6, 200001, NULL, 1500);

-- Opérateur 3 (037) : 0.75% → 1.75% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(7, 0, 5000, 38),
(7, 5001, 50000, 375),
(7, 50001, 200000, 875),
(7, 200001, NULL, 1750);

-- Opérateur 4 (038) : Barème avec paliers
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(8, 0, 5000, 50),
(8, 5001, 50000, 200),
(8, 50001, 200000, 500),
(8, 200001, NULL, 1000);

-- 4.6 Historique des frais - TRANSFERT (barèmes 9 à 12)
-- Opérateur 1 (033) : 0.5% → 1.5% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(9, 0, 5000, 25),
(9, 5001, 50000, 250),
(9, 50001, 200000, 750),
(9, 200001, NULL, 1500);

-- Opérateur 2 (034) : 0.25% → 1% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(10, 0, 5000, 13),
(10, 5001, 50000, 125),
(10, 50001, 200000, 500),
(10, 200001, NULL, 1000);

-- Opérateur 3 (037) : 0.5% → 2% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(11, 0, 5000, 25),
(11, 5001, 50000, 250),
(11, 50001, 200000, 1000),
(11, 200001, NULL, 2000);

-- Opérateur 4 (038) : 0.3% → 1.2% progressif
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe) VALUES
(12, 0, 5000, 15),
(12, 5001, 50000, 150),
(12, 50001, 200000, 600),
(12, 200001, NULL, 1200);

-- 4.7 Administrateur (mot de passe : admin123)
INSERT INTO admin (email, password) VALUES 
('admin@gmail.com', '$2y$10$UTMMoAhVoCxpxMtQlLIB9eu9YOdhfch0IYWfk9tkCf9ToIIW8tqjK');

-- 4.8 Clients
INSERT INTO clients (nom, telephone, code, operateur_id) VALUES 
('Jean Rakoto',  '0331234567', '1234', 1),
('Marie Rabe',   '0349876543', '5678', 2),
('Pierre Randria','0371122334', '9012', 3),
('Sophie Rasoa', '0385566778', '3456', 4);

-- 4.9 Transactions
-- Dépôts automatiques
INSERT INTO transactions (type_operation_id, expediteur_id, destinataire_id, montant_brut, date_transaction)
VALUES
(
    (SELECT id FROM types_operation WHERE code = 'DEPOT'),
    NULL,
    (SELECT id FROM clients WHERE telephone = '0331234567'),
    5000,
    '2026-07-10 10:00:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'DEPOT'),
    NULL,
    (SELECT id FROM clients WHERE telephone = '0349876543'),
    12000,
    '2026-07-11 14:30:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'DEPOT'),
    NULL,
    (SELECT id FROM clients WHERE telephone = '0371122334'),
    250000,
    '2026-07-15 09:15:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'DEPOT'),
    NULL,
    (SELECT id FROM clients WHERE telephone = '0385566778'),
    3000,
    '2026-07-16 11:00:00'
);

-- Retraits
INSERT INTO transactions (type_operation_id, expediteur_id, destinataire_id, montant_brut, date_transaction)
VALUES
(
    (SELECT id FROM types_operation WHERE code = 'RETRAIT'),
    (SELECT id FROM clients WHERE telephone = '0331234567'),
    NULL,
    2000,
    '2026-07-12 16:45:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'RETRAIT'),
    (SELECT id FROM clients WHERE telephone = '0349876543'),
    NULL,
    75000,
    '2026-07-18 11:20:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'RETRAIT'),
    (SELECT id FROM clients WHERE telephone = '0371122334'),
    NULL,
    100000,
    '2026-07-19 14:00:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'RETRAIT'),
    (SELECT id FROM clients WHERE telephone = '0385566778'),
    NULL,
    5000,
    '2026-07-20 09:30:00'
);

-- Transferts
INSERT INTO transactions (type_operation_id, expediteur_id, destinataire_id, montant_brut, date_transaction)
VALUES
(
    (SELECT id FROM types_operation WHERE code = 'TRANSFERT'),
    (SELECT id FROM clients WHERE telephone = '0331234567'),
    (SELECT id FROM clients WHERE telephone = '0349876543'),
    1500,
    '2026-07-13 08:30:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'TRANSFERT'),
    (SELECT id FROM clients WHERE telephone = '0371122334'),
    (SELECT id FROM clients WHERE telephone = '0385566778'),
    8000,
    '2026-07-14 12:10:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'TRANSFERT'),
    (SELECT id FROM clients WHERE telephone = '0331234567'),
    (SELECT id FROM clients WHERE telephone = '0385566778'),
    300000,
    '2026-07-19 17:00:00'
),
(
    (SELECT id FROM types_operation WHERE code = 'TRANSFERT'),
    (SELECT id FROM clients WHERE telephone = '0349876543'),
    (SELECT id FROM clients WHERE telephone = '0371122334'),
    2500,
    '2026-07-21 10:00:00'
);

-- 4.10 Modifications d'historique (pour tester les versions)
-- Modification RETRAIT opérateur 033 à partir du 20/07
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe, date_modif)
VALUES
(5, 0, 5000, 75, '2026-07-20 00:00:00'),
(5, 5001, 50000, 750, '2026-07-20 00:00:00'),
(5, 50001, 200000, 1500, '2026-07-20 00:00:00'),
(5, 200001, NULL, 3000, '2026-07-20 00:00:00');

-- Modification TRANSFERT opérateur 034 à partir du 21/07
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe, date_modif)
VALUES
(10, 0, 5000, 20, '2026-07-21 00:00:00'),
(10, 5001, 50000, 200, '2026-07-21 00:00:00'),
(10, 50001, 200000, 800, '2026-07-21 00:00:00'),
(10, 200001, NULL, 1600, '2026-07-21 00:00:00');

-- ============================================================
-- 5. REQUÊTES UTILES (commentaires)
-- ============================================================

-- Calculer le frais pour une transaction spécifique
-- SELECT h.frais_fixe
-- FROM baremes_frais b
-- JOIN baremes_frais_historique h ON h.bareme_id = b.id
-- WHERE b.type_operation_id = :type_operation_id
--   AND b.operateur_id      = :operateur_id
--   AND :montant_brut >= h.montant_min
--   AND (h.montant_max IS NULL OR :montant_brut <= h.montant_max)
--   AND h.date_modif = (
--       SELECT MAX(h2.date_modif)
--       FROM baremes_frais_historique h2
--       WHERE h2.bareme_id = h.bareme_id
--         AND h2.date_modif <= :date_transaction
--   );

-- Calculer le solde d'un client entre deux dates
-- SELECT
--     c.id AS client_id,
--     c.telephone,
--     COALESCE(SUM(
--         CASE
--             WHEN tf.destinataire_id = c.id THEN tf.montant_brut
--             WHEN tf.expediteur_id  = c.id THEN -(tf.montant_brut + COALESCE(tf.frais_applique, 0))
--         END
--     ), 0) AS solde
-- FROM clients c
-- LEFT JOIN v_transactions_frais tf
--        ON (tf.expediteur_id = c.id OR tf.destinataire_id = c.id)
--       AND (:date_debut IS NULL OR tf.date_transaction >= :date_debut)
--       AND (:date_fin   IS NULL OR tf.date_transaction <= :date_fin)
-- WHERE c.id = :client_id
-- GROUP BY c.id, c.telephone;

-- ============================================================
-- 6. VÉRIFICATIONS (optionnelles)
-- ============================================================
-- SELECT * FROM operateur_prefixes;
-- SELECT * FROM types_operation;
-- SELECT * FROM baremes_frais;
-- SELECT * FROM baremes_frais_historique;
-- SELECT * FROM admin;
-- SELECT * FROM clients;
-- SELECT * FROM transactions;
-- SELECT * FROM v_transactions_operateur;
-- SELECT * FROM v_transactions_frais;