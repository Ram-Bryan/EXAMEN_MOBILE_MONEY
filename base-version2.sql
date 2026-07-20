-- ============================================================
-- MIGRATION V1 → V2 - MOBILE MONEY
-- ============================================================

PRAGMA foreign_keys = ON;

-- ============================================================
-- 2. MODIFICATION DE LA TABLE operateur_prefixes (ajout colonnes)
-- ============================================================
ALTER TABLE operateur_prefixes ADD COLUMN nom TEXT;
ALTER TABLE operateur_prefixes ADD COLUMN est_notre_operateur INTEGER NOT NULL DEFAULT 0;

-- ============================================================
-- 3. MISE À JOUR DES OPÉRATEURS EXISTANTS
-- ============================================================
-- Notre opérateur (033 et 037)
UPDATE operateur_prefixes
SET nom = 'Mobile Money (Notre Opérateur)', est_notre_operateur = 1
WHERE id = 1;

-- Opérateurs externes
UPDATE operateur_prefixes SET nom = 'Airtel' WHERE id = 2;
UPDATE operateur_prefixes SET nom = 'Telma'  WHERE id = 3;
UPDATE operateur_prefixes SET nom = 'Bip'    WHERE id = 4;

-- ============================================================
-- 4. AJOUT DE NOUVEAUX OPÉRATEURS EXTERNES
-- ============================================================
INSERT INTO operateur_prefixes (id, nom, est_notre_operateur) VALUES
(5, 'Vodacom', 0);

-- (Si besoin, on peut en ajouter d'autres)

-- ============================================================
-- 5. CRÉATION DE LA TABLE historique_operateur_prefixes
-- ============================================================
CREATE TABLE IF NOT EXISTS historique_operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_prefixe_id INTEGER NOT NULL,
    prefixe TEXT NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_prefixe_id) REFERENCES operateur_prefixes(id)
);

-- ============================================================
-- 6. INSERTION DES PRÉFIXES HISTORIQUES (version initiale)
-- ============================================================
-- On vide la table si elle contient déjà des données (pour éviter les doublons)
DELETE FROM historique_operateur_prefixes;

INSERT INTO historique_operateur_prefixes (operateur_prefixe_id, prefixe, date_modif) VALUES
(1, '033', '2026-07-01 00:00:00'),
(1, '037', '2026-07-01 00:00:00'),
(2, '034', '2026-07-01 00:00:00'),
(3, '038', '2026-07-01 00:00:00'),
(4, '032', '2026-07-01 00:00:00'),
(5, '031', '2026-07-01 00:00:00');

-- ============================================================
-- 7. CRÉATION DES TABLES commissions ET commissions_historique
-- ============================================================
CREATE TABLE IF NOT EXISTS commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_destination_id INTEGER NOT NULL,
    FOREIGN KEY (operateur_destination_id) REFERENCES operateur_prefixes(id)
);

CREATE TABLE IF NOT EXISTS commissions_historique (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commission_id INTEGER NOT NULL,
    pourcentage REAL NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commission_id) REFERENCES commissions(id)
);

-- ============================================================
-- 8. INSERTION DES COMMISSIONS (pour les opérateurs externes)
-- ============================================================
-- On vide les tables avant insertion (si déjà remplies)
DELETE FROM commissions_historique;
DELETE FROM commissions;

INSERT INTO commissions (operateur_destination_id) VALUES
(2), -- Airtel
(3), -- Telma
(4), -- Bip
(5); -- Vodacom

-- Commission initiale (1.5% pour tous, on peut personnaliser par la suite)
INSERT INTO commissions_historique (commission_id, pourcentage) VALUES
(1, 1.5),
(2, 1.5),
(3, 1.5),
(4, 1.5);

-- ============================================================
-- 9. AJOUT DE BARÈMES POUR LES NOUVEAUX OPÉRATEURS (id 5)
-- ============================================================
-- On insère de nouvelles lignes dans baremes_frais pour l'opérateur 5
-- On va dupliquer les barèmes de l'opérateur 1 (type_operation_id 1,2,3)
-- avec des IDs qui continuent après les existants.
-- On suppose que les IDs existants vont jusqu'à 12. On commence à 13.
INSERT INTO baremes_frais (id, type_operation_id, operateur_id) VALUES
(13, 1, 5),  -- Dépôt
(14, 2, 5),  -- Retrait
(15, 3, 5);  -- Transfert

-- On insère les tranches pour ces nouveaux barèmes (en copiant celles de l'opérateur 1)
-- On peut utiliser des sous‑requêtes pour récupérer les tranches de l'opérateur 1.
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe)
SELECT
    new.id AS bareme_id,
    old.montant_min,
    old.montant_max,
    old.frais_fixe
FROM baremes_frais new
JOIN baremes_frais old ON old.type_operation_id = new.type_operation_id AND old.operateur_id = 1
WHERE new.operateur_id = 5;

-- ============================================================
-- 10. AJOUT DE LA COLONNE frais_inclus DANS transactions
-- ============================================================
ALTER TABLE transactions ADD COLUMN frais_inclus INTEGER NOT NULL DEFAULT 0;

-- ============================================================
-- 11. CORRECTION DES CLIENTS (si nécessaire)
-- ============================================================
-- Les clients existants ont déjà des operateur_id corrects (1,2,3,4)
-- On s'assure que le client 3 (Pierre) avec 037 est bien sur l'opérateur 1 (notre)
-- Le client 4 (Sophie) avec 038 est sur l'opérateur 3 (Telma) → OK
-- Si besoin, on peut faire des UPDATE pour corriger d'éventuelles incohérences.
-- Exemple : si un client a un téléphone qui ne correspond pas à son operateur_id, on le corrige.
UPDATE clients SET operateur_id = 1 WHERE telephone LIKE '033%' OR telephone LIKE '037%';
UPDATE clients SET operateur_id = 2 WHERE telephone LIKE '034%';
UPDATE clients SET operateur_id = 3 WHERE telephone LIKE '038%';
UPDATE clients SET operateur_id = 4 WHERE telephone LIKE '032%';
UPDATE clients SET operateur_id = 5 WHERE telephone LIKE '031%';

-- ============================================================
-- 12. AJUSTEMENT DES TRANSACTIONS POUR SOLDES POSITIFS
-- ============================================================
-- On ajoute des dépôts supplémentaires pour les clients qui auraient un solde insuffisant.
-- On va insérer des dépôts pour chaque client (s'ils n'en ont pas assez)
-- On peut le faire en vérifiant les soldes, mais on va simplement ajouter des dépôts forfaitaires.

-- Dépôts de sécurité pour tous les clients (pour éviter les soldes négatifs)
INSERT INTO transactions (type_operation_id, expediteur_id, destinataire_id, montant_brut, date_transaction, frais_inclus)
SELECT
    (SELECT id FROM types_operation WHERE code = 'DEPOT'),
    NULL,
    c.id,
    50000,  -- Montant de sécurité
    '2026-07-10 08:00:00',
    0
FROM clients c
WHERE NOT EXISTS (
    SELECT 1 FROM transactions t
    WHERE t.destinataire_id = c.id AND t.type_operation_id = (SELECT id FROM types_operation WHERE code = 'DEPOT')
);

-- On corrige les retraits/transferts trop élevés (on réduit les montants si nécessaire)
-- Exemple : le retrait de Marie (client 2) de 75000 dépasse son solde, on le réduit à 5000
UPDATE transactions
SET montant_brut = 5000
WHERE id = (SELECT id FROM transactions WHERE expediteur_id = 2 AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'RETRAIT') AND montant_brut > 50000);

-- Autre exemple : transfert de Jean (client 1) de 300000 → on réduit à 5000
UPDATE transactions
SET montant_brut = 5000
WHERE id = (SELECT id FROM transactions WHERE expediteur_id = 1 AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'TRANSFERT') AND montant_brut > 10000 AND date_transaction > '2026-07-18');

-- ============================================================
-- 13. RECRÉATION DES VUES (VERSION 2)
-- ============================================================

-- Vue v_transactions_operateur (ajout frais_inclus)
CREATE OR REPLACE VIEW v_transactions_operateur AS
SELECT
    tr.id AS transaction_id,
    tr.type_operation_id,
    tr.expediteur_id,
    tr.destinataire_id,
    tr.montant_brut,
    tr.date_transaction,
    tr.frais_inclus,
    COALESCE(ce.operateur_id, cd.operateur_id) AS operateur_id
FROM transactions tr
LEFT JOIN clients ce ON ce.id = tr.expediteur_id
LEFT JOIN clients cd ON cd.id = tr.destinataire_id;

-- Vue v_transactions_frais (avec commission inter-opérateur)
CREATE OR REPLACE VIEW v_transactions_frais AS
WITH frais_base AS (
    SELECT
        tc.transaction_id,
        tc.type_operation_id,
        tc.expediteur_id,
        tc.destinataire_id,
        tc.montant_brut,
        tc.date_transaction,
        tc.operateur_id,
        tc.frais_inclus,
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
        ) AS frais_fixe
    FROM v_transactions_operateur tc
)
SELECT
    fb.*,
    CASE
        WHEN fb.type_operation_id = (SELECT id FROM types_operation WHERE code = 'TRANSFERT')
             AND EXISTS (
                 SELECT 1 FROM clients c
                 JOIN operateur_prefixes o ON o.id = c.operateur_id
                 WHERE c.id = fb.destinataire_id AND o.est_notre_operateur = 0
             )
        THEN (
            SELECT COALESCE(ch.pourcentage, 0) * fb.montant_brut / 100
            FROM commissions_historique ch
            JOIN commissions c ON c.id = ch.commission_id
            WHERE c.operateur_destination_id = (
                SELECT o.id FROM clients c2
                JOIN operateur_prefixes o ON o.id = c2.operateur_id
                WHERE c2.id = fb.destinataire_id
            )
            AND ch.date_modif = (
                SELECT MAX(ch2.date_modif)
                FROM commissions_historique ch2
                WHERE ch2.commission_id = ch.commission_id
                  AND ch2.date_modif <= fb.date_transaction
            )
            ORDER BY ch.date_modif DESC LIMIT 1
        )
        ELSE 0
    END AS commission,
    COALESCE(fb.frais_fixe, 0) + COALESCE(commission, 0) AS frais_applique
FROM frais_base fb;

-- Vue v_situation_gains (séparés nous / autres)
CREATE OR REPLACE VIEW v_situation_gains AS
SELECT
    o.est_notre_operateur,
    t.code AS type_operation,
    SUM(tf.frais_applique) AS total_gains
FROM v_transactions_frais tf
JOIN types_operation t ON t.id = tf.type_operation_id
JOIN clients cd ON cd.id = tf.destinataire_id
JOIN operateur_prefixes o ON o.id = cd.operateur_id
WHERE tf.frais_applique IS NOT NULL
GROUP BY o.est_notre_operateur, t.code;

-- Vue v_montants_a_envoyer (settlement inter-opérateurs)
CREATE OR REPLACE VIEW v_montants_a_envoyer AS
SELECT
    o.id AS operateur_id,
    o.nom,
    SUM(tf.montant_brut) AS montant_total_a_envoyer
FROM v_transactions_frais tf
JOIN clients cd ON cd.id = tf.destinataire_id
JOIN operateur_prefixes o ON o.id = cd.operateur_id
WHERE o.est_notre_operateur = 0
  AND tf.type_operation_id = (SELECT id FROM types_operation WHERE code = 'TRANSFERT')
GROUP BY o.id, o.nom;

-- ============================================================
-- 14. VÉRIFICATIONS (optionnelles)
-- ============================================================
-- SELECT * FROM operateur_prefixes;
-- SELECT * FROM historique_operateur_prefixes;
-- SELECT * FROM commissions;
-- SELECT * FROM commissions_historique;
-- SELECT * FROM baremes_frais WHERE operateur_id = 5;
-- SELECT * FROM v_transactions_frais;
-- SELECT * FROM v_situation_gains;
-- SELECT * FROM v_montants_a_envoyer;