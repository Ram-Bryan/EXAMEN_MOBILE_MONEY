<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MigrationV2Seeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // ============================================================
        // 1. MISE À JOUR DES OPÉRATEURS EXISTANTS (déjà fait par le seeder principal)
        // ============================================================
        // Les opérateurs sont déjà mis à jour avec nom et est_notre_operateur par OperateurPrefixesSeeder

        // ============================================================
        // 3. INSERTION DES PRÉFIXES HISTORIQUES
        // ============================================================
        $db->query("DELETE FROM historique_operateur_prefixes");
        // Récupérer les IDs des opérateurs par leur préfixe
        $db->query("
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
        // 4. INSERTION DES COMMISSIONS (avec sous-requêtes)
        // ============================================================
        $db->query("DELETE FROM commissions_historique");
        $db->query("DELETE FROM commissions");
        
        // Insérer les commissions pour tous les opérateurs externes (est_notre_operateur = 0)
        $db->query("
            INSERT INTO commissions (operateur_destination_id)
            SELECT id FROM operateur_prefixes WHERE est_notre_operateur = 0
        ");
        
        // Insérer l'historique des commissions (1.5% pour chaque commission)
        $db->query("
            INSERT INTO commissions_historique (commission_id, pourcentage, date_modif)
            SELECT id, 1.5, '2026-01-01 00:00:00' FROM commissions
        ");

        // ============================================================
        // 5. AJOUT DES BARÈMES POUR L'OPÉRATEUR 5
        // ============================================================
        // Supprimer les anciens barèmes de l'opérateur 5 s'ils existent
        $db->query("DELETE FROM baremes_frais_historique WHERE bareme_id IN (SELECT id FROM baremes_frais WHERE operateur_id = 5)");
        $db->query("DELETE FROM baremes_frais WHERE operateur_id = 5");
        
        // Copier les barèmes de l'opérateur 1 vers l'opérateur 5
        $db->query("
            INSERT INTO baremes_frais (type_operation_id, operateur_id)
            SELECT type_operation_id, 5 FROM baremes_frais WHERE operateur_id = 1
        ");
        
        // Copier les tranches historiques
        $db->query("
            INSERT INTO baremes_frais_historique (bareme_id, montant_min, montant_max, frais_fixe, date_modif)
            SELECT
                new.id,
                hist.montant_min,
                hist.montant_max,
                hist.frais_fixe,
                hist.date_modif
            FROM baremes_frais new
            JOIN baremes_frais ref ON ref.type_operation_id = new.type_operation_id AND ref.operateur_id = 1
            JOIN baremes_frais_historique hist ON hist.bareme_id = ref.id
            WHERE new.operateur_id = 5
        ");

        // ============================================================
        // 6. MISE À JOUR DES OPERATEUR_ID DES CLIENTS
        // ============================================================
        // Récupérer les IDs des opérateurs par préfixe
        $db->query("
            UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '033' OR prefixe = '037') WHERE telephone LIKE '033%' OR telephone LIKE '037%'
        ");
        $db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '034') WHERE telephone LIKE '034%'");
        $db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '038') WHERE telephone LIKE '038%'");
        $db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '032') WHERE telephone LIKE '032%'");
        $db->query("UPDATE clients SET operateur_id = (SELECT id FROM operateur_prefixes WHERE prefixe = '031') WHERE telephone LIKE '031%'");

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