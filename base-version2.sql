-- ============================================================
-- MIGRATION V1 → V2 - MOBILE MONEY (COMPLÈTE)
-- À exécuter sur une base déjà en version 1
-- ============================================================

PRAGMA foreign_keys = ON;

-- ============================================================
-- 1. SUPPRESSION DES ANCIENNES VUES (elles seront recréées)
-- ============================================================
DROP VIEW IF EXISTS v_transactions_frais;
DROP VIEW IF EXISTS v_transactions_operateur;
DROP VIEW IF EXISTS v_situation_gains;
DROP VIEW IF EXISTS v_montants_a_envoyer;

-- ============================================================
-- 2. AJOUT DES COLONNES DANS operateur_prefixes
--    (Les erreurs "duplicate column" peuvent être ignorées)
-- ============================================================
ALTER TABLE operateur_prefixes ADD COLUMN nom TEXT;
ALTER TABLE operateur_prefixes ADD COLUMN est_notre_operateur INTEGER NOT NULL DEFAULT 0;

-- ============================================================
-- 3. MISE À JOUR DES OPÉRATEURS EXISTANTS
-- ============================================================
UPDATE operateur_prefixes
SET nom = 'Mobile Money (Notre Opérateur)', est_notre_operateur = 1
WHERE id = 1;

UPDATE operateur_prefixes SET nom = 'Airtel' WHERE id = 2;
UPDATE operateur_prefixes SET nom = 'Telma'  WHERE id = 3;
UPDATE operateur_prefixes SET nom = 'Bip'    WHERE id = 4;

-- ============================================================
-- 4. AJOUT DE NOUVEAUX OPÉRATEURS (avec prefixe obligatoire)
-- ============================================================
INSERT INTO operateur_prefixes (id, prefixe, nom, est_notre_operateur) VALUES
(5, '031', 'Vodacom', 0);

-- ============================================================
-- 5. TABLE historique_operateur_prefixes
-- ============================================================
CREATE TABLE IF NOT EXISTS historique_operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_prefixe_id INTEGER NOT NULL,
    prefixe TEXT NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operateur_prefixe_id) REFERENCES operateur_prefixes(id)
);

-- ============================================================
-- 6. INSERTION DES PRÉFIXES HISTORIQUES
-- ============================================================
DELETE FROM historique_operateur_prefixes;
INSERT INTO historique_operateur_prefixes (operateur_prefixe_id, prefixe, date_modif) VALUES
(1, '033', '2026-07-01 00:00:00'),
(1, '037', '2026-07-01 00:00:00'),
(2, '034', '2026-07-01 00:00:00'),
(3, '038', '2026-07-01 00:00:00'),
(4, '032', '2026-07-01 00:00:00'),
(5, '031', '2026-07-01 00:00:00');

-- ============================================================
-- 7. TABLES COMMISSIONS
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
-- 8. INSERTION DES COMMISSIONS
-- ============================================================
DELETE FROM commissions_historique;
DELETE FROM commissions;

INSERT INTO commissions (operateur_destination_id) VALUES
(2), (3), (4), (5);

INSERT INTO commissions_historique (commission_id, pourcentage) VALUES
(1, 1.5),
(2, 1.5),
(3, 1.5),
(4, 1.5);

-- ============================================================
-- 9. AJOUT DES BARÈMES POUR L'OPÉRATEUR 5
--    (Copie des tranches de l'opérateur 1)
-- ============================================================
-- Insérer les barèmes (lignes maîtresses)
INSERT INTO baremes_frais (id, type_operation_id, operateur_id) VALUES
(13, 1, 5),
(14, 2, 5),
(15, 3, 5);

-- Copier les tranches depuis l'historique de l'opérateur 1
INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe)
SELECT
    new.id,
    hist.montant_min,
    hist.montant_max,
    hist.frais_fixe
FROM baremes_frais new
JOIN baremes_frais ref
    ON ref.type_operation_id = new.type_operation_id
    AND ref.operateur_id = 1
JOIN baremes_frais_historique hist
    ON hist.bareme_id = ref.id
WHERE new.operateur_id = 5;

-- ============================================================
-- 10. AJOUT DE LA COLONNE frais_inclus DANS transactions
-- ============================================================
ALTER TABLE transactions ADD COLUMN frais_inclus INTEGER NOT NULL DEFAULT 0;

-- ============================================================
-- 11. MISE À JOUR DES OPERATEUR_ID DES CLIENTS
-- ============================================================
UPDATE clients SET operateur_id = 1 WHERE telephone LIKE '033%' OR telephone LIKE '037%';
UPDATE clients SET operateur_id = 2 WHERE telephone LIKE '034%';
UPDATE clients SET operateur_id = 3 WHERE telephone LIKE '038%';
UPDATE clients SET operateur_id = 4 WHERE telephone LIKE '032%';
UPDATE clients SET operateur_id = 5 WHERE telephone LIKE '031%';

-- ============================================================
-- 12. DÉPÔTS DE SÉCURITÉ POUR SOLDES POSITIFS
-- ============================================================
INSERT INTO transactions (type_operation_id, expediteur_id, destinataire_id, montant_brut, date_transaction, frais_inclus)
SELECT
    (SELECT id FROM types_operation WHERE code = 'DEPOT'),
    NULL,
    c.id,
    50000,
    '2026-07-10 08:00:00',
    0
FROM clients c
WHERE NOT EXISTS (
    SELECT 1 FROM transactions t
    WHERE t.destinataire_id = c.id
      AND t.type_operation_id = (SELECT id FROM types_operation WHERE code = 'DEPOT')
);

-- ============================================================
-- 13. AJUSTEMENT DES RETRAITS / TRANSFERTS TROP ÉLEVÉS
-- ============================================================
-- Retrait du client 2 (Marie) réduit à 5000
UPDATE transactions
SET montant_brut = 5000
WHERE id IN (
    SELECT id FROM transactions
    WHERE expediteur_id = 2
      AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'RETRAIT')
      AND montant_brut > 50000
);

-- Transfert du client 1 (Jean) après le 18/07 réduit à 5000
UPDATE transactions
SET montant_brut = 5000
WHERE id IN (
    SELECT id FROM transactions
    WHERE expediteur_id = 1
      AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'TRANSFERT')
      AND montant_brut > 10000
      AND date_transaction > '2026-07-18'
);

-- ============================================================
-- 14. RECRÉATION DES VUES VERSION 2
-- ============================================================

-- Vue v_transactions_operateur
CREATE VIEW v_transactions_operateur AS
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

-- Vue v_transactions_frais (avec commission inter‑opérateur)
CREATE VIEW v_transactions_frais AS
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

-- Vue v_situation_gains
CREATE VIEW v_situation_gains AS
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

-- Vue v_montants_a_envoyer
CREATE VIEW v_montants_a_envoyer AS
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
-- 15. VÉRIFICATIONS OPTIONNELLES
-- ============================================================
-- SELECT * FROM operateur_prefixes;
-- SELECT * FROM historique_operateur_prefixes;
-- SELECT * FROM commissions;
-- SELECT * FROM commissions_historique;
-- SELECT * FROM v_transactions_frais LIMIT 5;
-- SELECT * FROM v_situation_gains;
-- SELECT * FROM v_montants_a_envoyer;

-- ============================================================
-- FIN DE LA MIGRATION
-- ============================================================