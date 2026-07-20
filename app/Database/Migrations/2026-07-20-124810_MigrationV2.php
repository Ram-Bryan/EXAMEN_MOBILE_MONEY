<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrationV2 extends Migration
{
    public function up()
    {
        // ============================================================
        // 1. AJOUT DES COLONNES DANS operateur_prefixes
        // ============================================================
        $columns = $this->db->getFieldData('operateur_prefixes');
        $existingColumns = array_column($columns, 'name');
        
        if (!in_array('nom', $existingColumns)) {
            $this->forge->addColumn('operateur_prefixes', [
                'nom' => ['type' => 'TEXT', 'null' => true]
            ]);
        }
        
        if (!in_array('est_notre_operateur', $existingColumns)) {
            $this->forge->addColumn('operateur_prefixes', [
                'est_notre_operateur' => ['type' => 'INTEGER', 'constraint' => 1, 'null' => false, 'default' => 0]
            ]);
        }

        // ============================================================
        // 2. MISE À JOUR DES OPÉRATEURS EXISTANTS
        // ============================================================
        $this->db->query("UPDATE operateur_prefixes SET nom = 'Mobile Money (Notre Opérateur)', est_notre_operateur = 1 WHERE id = 1");
        $this->db->query("UPDATE operateur_prefixes SET nom = 'Airtel' WHERE id = 2");
        $this->db->query("UPDATE operateur_prefixes SET nom = 'Telma' WHERE id = 3");
        $this->db->query("UPDATE operateur_prefixes SET nom = 'Bip' WHERE id = 4");

        // ============================================================
        // 3. AJOUT DE NOUVEAUX OPÉRATEURS (avec OR IGNORE)
        // ============================================================
        $this->db->query("INSERT OR IGNORE INTO operateur_prefixes (id, prefixe, nom, est_notre_operateur) VALUES (5, '031', 'Vodacom', 0)");

        // ============================================================
        // 4. TABLE historique_operateur_prefixes
        // ============================================================
        if (!$this->db->tableExists('historique_operateur_prefixes')) {
            $this->forge->addField([
                'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'operateur_prefixe_id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'prefixe' => ['type' => 'TEXT', 'null' => false],
                'date_modif' => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('operateur_prefixe_id', 'operateur_prefixes', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('historique_operateur_prefixes');
        }

        // ============================================================
        // 5. INSERTION DES PRÉFIXES HISTORIQUES (avec sous-requêtes)
        // ============================================================
        $this->db->query("DELETE FROM historique_operateur_prefixes");
        $this->db->query("
            INSERT INTO historique_operateur_prefixes (operateur_prefixe_id, prefixe, date_modif)
            SELECT id, '033', '2026-07-01 00:00:00' FROM operateur_prefixes WHERE prefixe = '033' OR id = 1
            UNION ALL
            SELECT id, '037', '2026-07-01 00:00:00' FROM operateur_prefixes WHERE prefixe = '037' OR id = 1
            UNION ALL
            SELECT id, '034', '2026-07-01 00:00:00' FROM operateur_prefixes WHERE prefixe = '034' OR id = 2
            UNION ALL
            SELECT id, '038', '2026-07-01 00:00:00' FROM operateur_prefixes WHERE prefixe = '038' OR id = 3
            UNION ALL
            SELECT id, '032', '2026-07-01 00:00:00' FROM operateur_prefixes WHERE prefixe = '032' OR id = 4
            UNION ALL
            SELECT id, '031', '2026-07-01 00:00:00' FROM operateur_prefixes WHERE prefixe = '031' OR id = 5
        ");

        // ============================================================
        // 6. TABLES COMMISSIONS
        // ============================================================
        if (!$this->db->tableExists('commissions')) {
            $this->forge->addField([
                'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'operateur_destination_id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('operateur_destination_id', 'operateur_prefixes', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('commissions');
        }

        if (!$this->db->tableExists('commissions_historique')) {
            $this->forge->addField([
                'id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'commission_id' => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'pourcentage' => ['type' => 'REAL', 'null' => false],
                'date_modif' => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('commission_id', 'commissions', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('commissions_historique');
        }

        // ============================================================
        // 7. INSERTION DES COMMISSIONS (avec sous-requêtes)
        // ============================================================
        $this->db->query("DELETE FROM commissions_historique");
        $this->db->query("DELETE FROM commissions");
        $this->db->query("
            INSERT INTO commissions (operateur_destination_id)
            SELECT id FROM operateur_prefixes WHERE est_notre_operateur = 0
        ");
        $this->db->query("
            INSERT INTO commissions_historique (commission_id, pourcentage)
            SELECT id, 1.5 FROM commissions
        ");

        // ============================================================
        // 8. AJOUT DES BARÈMES POUR L'OPÉRATEUR 5
        // ============================================================
        $this->db->query("DELETE FROM baremes_frais_historique WHERE bareme_id IN (SELECT id FROM baremes_frais WHERE operateur_id = 5)");
        $this->db->query("DELETE FROM baremes_frais WHERE operateur_id = 5");
        
        $this->db->query("
            INSERT INTO baremes_frais (type_operation_id, operateur_id)
            SELECT type_operation_id, 5 FROM baremes_frais WHERE operateur_id = 1
        ");
        
        $this->db->query("
            INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe)
            SELECT
                new.id,
                hist.montant_min,
                hist.montant_max,
                hist.frais_fixe
            FROM baremes_frais new
            JOIN baremes_frais ref ON ref.type_operation_id = new.type_operation_id AND ref.operateur_id = 1
            JOIN baremes_frais_historique hist ON hist.bareme_id = ref.id
            WHERE new.operateur_id = 5
        ");

        // ============================================================
        // 9. AJOUT DE LA COLONNE frais_inclus DANS transactions
        // ============================================================
        $columns = $this->db->getFieldData('transactions');
        $existingColumns = array_column($columns, 'name');
        
        if (!in_array('frais_inclus', $existingColumns)) {
            $this->forge->addColumn('transactions', [
                'frais_inclus' => ['type' => 'INTEGER', 'constraint' => 1, 'null' => false, 'default' => 0]
            ]);
        }

        // ============================================================
        // 10. MISE À JOUR DES OPERATEUR_ID DES CLIENTS
        // ============================================================
        $this->db->query("
            UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '033' OR prefixe = '037') WHERE telephone LIKE '033%' OR telephone LIKE '037%'
        ");
        $this->db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '034') WHERE telephone LIKE '034%'");
        $this->db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '038') WHERE telephone LIKE '038%'");
        $this->db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '032') WHERE telephone LIKE '032%'");
        $this->db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '031') WHERE telephone LIKE '031%'");

        // ============================================================
        // 11. DÉPÔTS DE SÉCURITÉ POUR SOLDES POSITIFS
        // ============================================================
        $this->db->query("
            INSERT OR IGNORE INTO transactions (type_operation_id, expediteur_id, destinataire_id, montant_brut, date_transaction, frais_inclus)
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
            )
        ");

        // ============================================================
        // 12. AJUSTEMENT DES RETRAITS / TRANSFERTS TROP ÉLEVÉS
        // ============================================================
        $this->db->query("
            UPDATE transactions
            SET montant_brut = 5000
            WHERE id IN (
                SELECT id FROM transactions
                WHERE expediteur_id = 2
                  AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'RETRAIT')
                  AND montant_brut > 50000
            )
        ");

        $this->db->query("
            UPDATE transactions
            SET montant_brut = 5000
            WHERE id IN (
                SELECT id FROM transactions
                WHERE expediteur_id = 1
                  AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'TRANSFERT')
                  AND montant_brut > 10000
                  AND date_transaction > '2026-07-18'
            )
        ");

        // ============================================================
        // 13. RECRÉATION DES VUES VERSION 2 (DROP + CREATE)
        // ============================================================

        // Supprimer les anciennes vues si elles existent
        $this->db->query('DROP VIEW IF EXISTS v_transactions_frais');
        $this->db->query('DROP VIEW IF EXISTS v_transactions_operateur');
        $this->db->query('DROP VIEW IF EXISTS v_situation_gains');
        $this->db->query('DROP VIEW IF EXISTS v_montants_a_envoyer');

        // Vue v_transactions_operateur
        $this->db->query("
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
            LEFT JOIN clients cd ON cd.id = tr.destinataire_id
        ");

        // Vue v_transactions_frais (fixed: no alias reuse for SQLite compatibility)
        $this->db->query("
            CREATE VIEW v_transactions_frais AS
            SELECT
                fb.transaction_id,
                fb.type_operation_id,
                fb.expediteur_id,
                fb.destinataire_id,
                fb.montant_brut,
                fb.date_transaction,
                fb.operateur_id,
                fb.frais_inclus,
                fb.frais_fixe,
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
                        JOIN commissions cm ON cm.id = ch.commission_id
                        WHERE cm.operateur_destination_id = (
                            SELECT o2.id FROM clients c2
                            JOIN operateur_prefixes o2 ON o2.id = c2.operateur_id
                            WHERE c2.id = fb.destinataire_id
                        )
                        AND ch.date_modif <= fb.date_transaction
                        ORDER BY ch.date_modif DESC LIMIT 1
                    )
                    ELSE 0
                END AS commission,
                COALESCE(fb.frais_fixe, 0) + COALESCE(
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
                            JOIN commissions cm ON cm.id = ch.commission_id
                            WHERE cm.operateur_destination_id = (
                                SELECT o2.id FROM clients c2
                                JOIN operateur_prefixes o2 ON o2.id = c2.operateur_id
                                WHERE c2.id = fb.destinataire_id
                            )
                            AND ch.date_modif <= fb.date_transaction
                            ORDER BY ch.date_modif DESC LIMIT 1
                        )
                        ELSE 0
                    END,
                0) AS frais_applique
            FROM (
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
            ) fb
        ");

        // Vue v_situation_gains
        $this->db->query("
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
            GROUP BY o.est_notre_operateur, t.code
        ");

        // Vue v_montants_a_envoyer
        $this->db->query("
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
            GROUP BY o.id, o.nom
        ");
    }

    public function down()
    {
        // Supprimer les vues
        $this->db->query('DROP VIEW IF EXISTS v_transactions_frais');
        $this->db->query('DROP VIEW IF EXISTS v_transactions_operateur');
        $this->db->query('DROP VIEW IF EXISTS v_situation_gains');
        $this->db->query('DROP VIEW IF EXISTS v_montants_a_envoyer');

        // Supprimer les nouvelles tables
        $this->forge->dropTable('commissions_historique', true);
        $this->forge->dropTable('commissions', true);
        $this->forge->dropTable('historique_operateur_prefixes', true);

        // Supprimer la colonne frais_inclus
        $this->forge->dropColumn('transactions', 'frais_inclus');

        // Supprimer les colonnes ajoutées dans operateur_prefixes
        $this->forge->dropColumn('operateur_prefixes', 'nom');
        $this->forge->dropColumn('operateur_prefixes', 'est_notre_operateur');

        // Supprimer l'opérateur 5
        $this->db->query("DELETE FROM operateur_prefixes WHERE id = 5");

        // Supprimer les barèmes de l'opérateur 5
        $this->db->query("DELETE FROM baremes_frais WHERE operateur_id = 5");

        // Restaurer les vues v1 (DROP + CREATE)
        $this->db->query('DROP VIEW IF EXISTS v_transactions_operateur');
        $this->db->query('DROP VIEW IF EXISTS v_transactions_frais');

        $this->db->query("
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
            LEFT JOIN clients cd ON cd.id = tr.destinataire_id
        ");

        $this->db->query("
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
            FROM v_transactions_operateur tc
        ");
    }
}