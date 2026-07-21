# TODO Version 1 — Mobile Money (Version simple)

## Étape 0 — Mise en place (Ensemble)

- [x] Créer le dépôt Git et faire le premier commit
- [x] Configurer CodeIgniter 4 avec SQLite
- [x] Créer et exécuter `base.sql`
- [x] Créer `Taches.md`
- [x] Répartir les tâches (Dev A / Dev B)
- [x] Ajouter les données de test (seed)


# Dev A — Back-office

## Configuration
- [x] Gérer les préfixes opérateurs (CRUD)
- [x] Gérer les types d'opérations
- [x] Gérer les barèmes de frais par opérateur
- [x] Ajouter une tranche de frais
- [x] Modifier une tranche de frais (historique uniquement)
- [x] Afficher les barèmes actuels

## Authentification
- [x] Login administrateur
- [x] Protéger les routes `/admin`

## Tableau de bord
- [x] Afficher les gains par type d'opération
- [x] Afficher les soldes des clients

## Finalisation
- [x] Vérifier les contraintes
- [x] Mettre à jour `Taches.md`

## function 
    - function dashboard() 
    - function crud operateor 
    - function createFee($operateurId) 
    - function function gains()
    - function client 
    - function doLoginClient()
    - function loginAdmin()
    - function doLoginAdmin()
    - function logout()

# Dev B — Espace client

## Connexion
- [x] Login par numéro de téléphone
- [x] Vérification du préfixe
- [x] Création automatique du client
- [x] Création de la session

## Solde
- [x] Afficher le solde
- [x] Créer la page Solde
- [x] API AJAX du solde

## Dépôt
- [x] Formulaire de dépôt
- [x] Enregistrer le dépôt

## Retrait
- [x] Formulaire de retrait
- [x] Calcul des frais
- [x] Vérification du solde
- [x] Gestion des erreurs de barème

## Transfert
- [x] Formulaire de transfert
- [x] Création automatique du destinataire
- [x] Vérification du solde
- [x] Enregistrement du transfert

## Historique
- [x] Afficher les transactions

## function 
    - function home()
    - function doLoginClient()
    - function logout()
    - function solde()
    - function depot()
    - function retrait()
    - function transfert()
    - function historique()
    - function getBalance()
    - function dashboard()
    - function balance()
    - function deposit()
    - function doDeposit()
    - function withdraw()
    - function transfer()
    

# Intégration finale (Ensemble)

- [x] Fusionner les branches
- [x] Tester l'application
- [x] Vérifier les frais
- [x] Compléter `Taches.md`
- [x] Créer le tag `v1`
- [x] Pousser le tag
- [x] Livrer le projet


---

# Version 2 — Mobile Money

## Migration base de données — `base-version2.sql`

- [x] Migration : ajout colonnes `nom`, `est_notre_operateur` dans `operateur_prefixes`
- [x] Migration : ajout opérateur 5 (Vodacom, préfixe 031)
- [x] Migration : table `historique_operateur_prefixes` + données
- [x] Migration : tables `commissions` et `commissions_historique` (1.5%)
- [x] Migration : colonne `frais_inclus` dans `transactions`
- [x] Migration : mise à jour des `operateur_id` clients selon préfixe
- [x] Migration : barèmes pour opérateur 5 (copie opérateur 1)
- [x] Migration : vues recréées (`v_transactions_operateur`, `v_transactions_frais` avec commission, `v_situation_gains`, `v_montants_a_envoyer`)

---

# Dev A — Back-office

## Barèmes et gain
- [x] Bug fix : ajout d'une tranche affiche toutes les tranches (`addTranche` copie les tranches courantes dans la nouvelle version)
- [x] Nouvelle méthode `getAllBaremesByOperateur()` — SELECT * de tous les baremes/historique pour un opérateur
- [x] Passage de `$allBaremes` dans `AdminController::operatorDetail()`
- [x] Nouveau tableau "Situation des gains — Tous les barèmes" dans `operator_detail.php` (affiche bareme_id, historique_id, type_nom, montant_min, montant_max, frais_fixe, date_modif)

## Commission
- [x] Tables `commissions` et `commissions_historique` créées et peuplées
- [x] Affichage du taux de commission dans la sidebar des transferts côté client

## Functions
    - function operatorDetail($id) — passe `allBaremes` à la vue
    - function createFee($operateurId) — appelle `addTranche` corrigé
    - function updateFee($baremeId, $operateurId)
    - function gains() — affiche gains par type
    - function clients() — soldes clients
    - function getAllBaremesByOperateur() dans BaremeFraisModel
    - function addTranche() dans BaremeFraisModel — copie la version courante

---

# Dev B — Espace client

## Transfert simple — Option frais
- [x] Ajout `<select>` "Options de transfert" (option 1 : sans frais, option 2 : avec frais)
- [x] Champ caché `include_fees` envoyé au controller
- [x] JS `previewTransferFee()` adapté : option 1 = pas d'AJAX frais, option 2 = appel API
- [x] Controller `doTransfer()` : `$fee = 0` si option 1, calcul frais si option 2
- [x] Colonne `frais_inclus` dans `transactions` (0 ou 1)

## Envoi multiple d'argent
- [x] Radio boutons "Transfert simple" / "Envoi multiple"
- [x] Bloc multi-destinataires dynamique (ajout/suppression de champs)
- [x] Le montant total est divisé par le nombre de destinataires (`floor(total / count)`)
- [x] Aperçu multi-lignes : tableau avec montant, frais, commission, débit par destinataire
- [x] Injection de champs cachés `recipients[]` avant soumission du formulaire
- [x] Validation JS : doublons, préfixe, numéro propre, pas d'auto-envoi
- [x] Controller `doTransfer()` : boucle sur les destinataires, 1 transaction par destinataire

## Commission inter-opérateur
- [x] Nouvelle méthode `getCommission()` dans `BaremeFraisModel` — calcule pourcentage * montant / 100
- [x] Nouvelle méthode `isInterOperator()` dans `BaremeFraisModel` — vérifie `est_notre_operateur`
- [x] API `calculateFees()` modifiée : accepte `recipient_phone`, retourne `fee`, `commission`, `is_inter_operator`
- [x] Aperçu JS affiche la commission inter-opérateur (colonne + ligne séparée)
- [x] Si option 2 + inter-op : total débité = montant + frais_fixe + commission

## Controller
- [x] `transfer()` : passe `sender_operateur_id` à la vue
- [x] `doTransfer()` réécrit : gère `transfer_mode` (single/multiple), `recipients[]`, boucle destinataires, frais + commission, `frais_inclus`

## Vue
- [x] `transfer.php` réécrite : mode simple/multiple, aperçu simple + multi, commission, validation complète

## Functions
    - function transfer() — affiche formulaire, passe sender_operateur_id
    - function doTransfer() — simple + multiple, frais + commission + frais_inclus
    - function previewTransferFee() — AJAX pour frais + commission par destinataire
    - function switchMode() — bascule simple/multiple
    - function addRecipient() / removeRecipient() — gestion dynamique des champs
    - function getRecipientPhones() — collecte les numéros
    - function renderPreview() — affiche aperçu simple ou tableau multi
    - function getCommission() dans BaremeFraisModel
    - function isInterOperator() dans BaremeFraisModel
    - calculateFees() dans Api — retourne fee + commission + is_inter_operator

---

# Intégration finale v2

- [x] Exécution de la migration `base-version2.sql` sur `database.db`
- [x] Vérification des vues recréées (v_transactions_frais avec colonne commission)
- [x] Tests des transferts simples (option 1 et option 2)
- [x] Tests des transferts multiples
- [x] Tests des transferts inter-opérateur (commission)
- [x] Compléter `Taches.md`

promotion fris de transfert , meme operateur 
creer le promotion , refa lasa  