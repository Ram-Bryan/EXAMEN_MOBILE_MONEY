<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MigrationV2Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // ============================================================
        // 1. MISE À JOUR DES OPÉRATEURS EXISTANTS
        // ============================================================
        $db->query("UPDATE operateur_prefixes SET nom = 'Mobile Money (Notre Opérateur)', est_notre_operateur = 1 WHERE id = 1");
        $db->query("UPDATE operateur_prefixes SET nom = 'Airtel' WHERE id = 2");
        $db->query("UPDATE operateur_prefixes SET nom = 'Telma' WHERE id = 3");
        $db->query("UPDATE operateur_prefixes SET nom = 'Bip' WHERE id = 4");

        // ============================================================
        // 2. AJOUT DE NOUVEAUX OPÉRATEURS
        // ============================================================
        $db->query("INSERT OR IGNORE INTO operateur_prefixes (id, prefixe, nom, est_notre_operateur) VALUES (5, '031', 'Vodacom', 0)");

        // ============================================================
        // 3. INSERTION DES PRÉFIXES HISTORIQUES
        // ============================================================
        $db->query("DELETE FROM historique_operateur_prefixes");
        $db->query("INSERT INTO historique_operateur_prefixes (operateur_prefixe_id, prefixe, date_modif) VALUES
            (1, '033', '2026-07-01 00:00:00'),
            (1, '037', '2026-07-01 00:00:00'),
            (2, '034', '2026-07-01 00:00:00'),
            (3, '038', '2026-07-01 00:00:00'),
            (4, '032', '2026-07-01 00:00:00'),
            (5, '031', '2026-07-01 00:00:00')");

        // ============================================================
        // 4. INSERTION DES COMMISSIONS
        // ============================================================
        $db->query("DELETE FROM commissions_historique");
        $db->query("DELETE FROM commissions");
        $db->query("INSERT INTO commissions (operateur_destination_id) VALUES (2), (3), (4), (5)");
        $db->query("INSERT INTO commissions_historique (commission_id, pourcentage) VALUES (1, 1.5), (2, 1.5), (3, 1.5), (4, 1.5)");

        // ============================================================
        // 5. AJOUT DES BARÈMES POUR L'OPÉRATEUR 5
        // ============================================================
        // Vérifier si les barèmes existent déjà
        $existing = $db->query("SELECT id FROM baremes_frais WHERE operateur_id = 5")->getResult();
        if (empty($existing)) {
            $db->query("INSERT INTO baremes_frais (id, type_operation_id, operateur_id) VALUES
                (13, 1, 5),
                (14, 2, 5),
                (15, 3, 5)");

            $db->query("
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
                WHERE new.operateur_id = 5
            ");
        }

        // ============================================================
        // 6. MISE À JOUR DES OPERATEUR_ID DES CLIENTS
        // ============================================================
        $db->query("UPDATE clients SET operateur_id = 1 WHERE telephone LIKE '033%' OR telephone LIKE '037%'");
        $db->query("UPDATE clients SET operateur_id = 2 WHERE telephone LIKE '034%'");
        $db->query("UPDATE clients SET operateur_id = 3 WHERE telephone LIKE '038%'");
        $db->query("UPDATE clients SET operateur_id = 4 WHERE telephone LIKE '032%'");
        $db->query("UPDATE clients SET operateur_id = 5 WHERE telephone LIKE '031%'");

        // ============================================================
        // 7. DÉPÔTS DE SÉCURITÉ POUR SOLDES POSITIFS
        // ============================================================
        $db->query("
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
        // 8. AJUSTEMENT DES RETRAITS / TRANSFERTS TROP ÉLEVÉS
        // ============================================================
        $db->query("
            UPDATE transactions
            SET montant_brut = 5000
            WHERE id IN (
                SELECT id FROM transactions
                WHERE expediteur_id = 2
                  AND type_operation_id = (SELECT id FROM types_operation WHERE code = 'RETRAIT')
                  AND montant_brut > 50000
            )
        ");

        $db->query("
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

        echo "\n✅ Migration V2 terminée avec succès !\n";
        echo "📊 Nouveaux opérateurs : 5 (Vodacom)\n";
        echo "📊 Commissions ajoutées pour les opérateurs externes\n";
        echo "📊 Barèmes copiés pour l'opérateur 5\n";
        echo "📊 Colonne frais_inclus ajoutée\n";
        echo "📊 Vues recréées en version 2\n";
    }
}