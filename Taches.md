# TODO Version 1 — Mobile Money (5h, binôme)

Répartition pensée pour que **Dev A** et **Dev B** avancent en parallèle sans se bloquer.
Le seul point de synchronisation obligatoire est l'étape **0 (setup commun)**, à faire ensemble en tout début.
Après ça, chacun travaille sur ses propres fichiers/routes/contrôleurs jusqu'à l'intégration finale.

---

## Étape 0 — Setup commun (30 min, ensemble)

- [ ] Créer le repo Git (public), premier commit
- [ ] Remplir le Google Form avec l'URL du repo
- [ ] Installer CodeIgniter 4 + config SQLite (`.env`, connexion DB)
- [ ] Créer `base.sql` à la racine avec **tout le schéma déjà conçu** (tables + vues)
- [ ] Exécuter `base.sql` sur la DB locale de chacun, vérifier que ça tourne des deux côtés
- [ ] Créer `Taches.md` à la racine (squelette vide, sections "Dev A" / "Dev B")
- [ ] Se répartir : Dev A = **back-office opérateur**, Dev B = **espace client**
- [ ] Convenir d'un jeu de données de seed minimal (2-3 préfixes, 3 types d'opération, quelques tranches de frais) — **Dev A l'écrit dans `base.sql`**, Dev B en dépend pour tester

⚠️ Tant que le seed des barèmes n'est pas dans `base.sql`, Dev B ne peut pas tester ses calculs de frais. Priorité absolue pour Dev A dans la première demi-heure.

---

## Dev A — Back-office opérateur (config + stats)

### Config (1h30)
- [ ] Modèle `OperateurPrefixesModel` + CRUD (créer/lister/modifier un préfixe)
- [ ] Vue/formulaire admin : liste des préfixes + ajout
- [ ] Modèle `TypesOperationModel` (lecture seule suffit si déjà seedé — DEPOT/RETRAIT/TRANSFERT)
- [ ] Modèle `BaremesFraisModel` + `BaremesFraisHistoriqueModel`
- [ ] Formulaire admin "créer une tranche de frais" → insert dans `baremes_frais` (id) + première ligne dans `baremes_frais_historique`
- [ ] Formulaire admin "modifier une tranche" → **INSERT uniquement** dans `baremes_frais_historique` avec le même `bareme_id` (jamais d'UPDATE — respecter la règle d'historique)
- [ ] Écran listant les tranches **actuelles** par type d'opération (utiliser la sous-requête `MAX(date_modif)` déjà écrite)

### Authentification admin (30 min)
- [ ] Table `admin` déjà en place → formulaire login email/password
- [ ] Filtre CodeIgniter (`before` filter) qui protège les routes `/admin/*`

### Stats / dashboards (1h)
- [ ] Écran "situation des gains" : agrégat de `v_transactions_frais.frais_applique` groupé par type d'opération
- [ ] Écran "situation des comptes clients" : liste des clients + solde (requête solde déjà écrite, sans filtre de dates pour cet écran)

### Buffer / polish (30 min)
- [ ] Vérifier les CHECK constraints (montant_max >= montant_min, montant_brut > 0)
- [ ] Mettre à jour `Taches.md` avec le détail de son travail

---

## Dev B — Espace client (auto-login + opérations)

### Login automatique (1h)
- [x] Formulaire "entrer son numéro de téléphone" (`app/Views/auth/login.php`)
- [x] Contrôleur : valider le préfixe contre `operateur_prefixes` (rejeter si préfixe inconnu) — `Auth::doLogin()`
- [x] Si le numéro existe déjà dans `clients` → connexion directe (session)
- [x] Si le numéro n'existe pas → création automatique du client (`ClientModel::createClient()`), puis connexion
- [x] Session client (CodeIgniter session) : `client_id`, `phone`, `name`, `client_code`, `role='client'`

### Voir le solde (30 min)
- [x] Contrôleur qui appelle la requête solde paramétrée via `ClientModel::getBalance()` (sans dates = solde total actuel)
- [x] Vue dédiée `app/Views/client/balance.php` affichant solde + infos compte
- [x] Endpoint AJAX `GET /api/client/balance` via `Api::getBalance()` pour actualiser le solde sans rechargement

### Dépôt / Retrait (1h)
- [x] Formulaire dépôt (`app/Views/client/deposit.php`) : montant → `INSERT` dans `transactions` avec `destinataire_id = client_id`, `expediteur_id = NULL`
- [x] Formulaire retrait (`app/Views/client/withdraw.php`) : montant → `INSERT` dans `transactions` avec `expediteur_id = client_id`, `destinataire_id = NULL`
- [x] **Validation avant retrait** : calcul du frais via `BaremeFraisModel::getFrais()` + vérification solde suffisant (montant + frais)
- [x] Gérer le cas où aucune tranche de frais ne matche → message d'erreur JSON clair, pas de crash
- [x] Prévisualisation dynamique AJAX des frais via `POST /api/fees/calculate`

### Transfert (1h)
- [x] Formulaire transfert (`app/Views/client/transfer.php`) : numéro destinataire + montant
- [x] Vérification et création automatique du destinataire si préfixe valide et numéro inconnu
- [x] `INSERT` dans `transactions` avec `expediteur_id` et `destinataire_id` remplis
- [x] Même validation de solde suffisant (montant + frais) que pour le retrait

### Historique (30 min)
- [x] Vue `app/Views/client/history.php` listant les transactions du client connecté depuis `v_transactions_frais`, avec montant, frais, date, type, direction (envoi/réception)

---

## Récapitulatif Dev B — Travail effectué (v1)

**Fichiers créés / modifiés :**

| Fichier | Type | Description |
|---|---|---|
| `app/Models/ClientModel.php` | Modifié | Ajout : `getByTelephone()`, `getBalance()`, `createClient()` |
| `app/Models/BaremeFraisModel.php` | Modifié | Ajout : `getFrais()`, `getFeesSchedules()` |
| `app/Models/TransactionModel.php` | Modifié | Ajout : `getClientTransactions()`, `createTransaction()` |
| `app/Controllers/Auth.php` | Modifié | Login client auto par téléphone + login admin par email/MDP |
| `app/Controllers/Client.php` | Modifié | Actions : `dashboard`, `balance`, `deposit/doDeposit`, `withdraw/doWithdraw`, `transfer/doTransfer`, `history` |
| `app/Controllers/Api.php` | Créé | API AJAX : `getBalance`, `calculateFees` |
| `app/Controllers/Home.php` | Modifié | Redirection racine `/` vers `client/dashboard` |
| `app/Views/auth/login.php` | Créé | Page de connexion par numéro de téléphone |
| `app/Views/client/dashboard.php` | Modifié | Tableau de bord : solde + 5 dernières transactions |
| `app/Views/client/balance.php` | Créé | Page solde détaillé + infos compte |
| `app/Views/client/deposit.php` | Créé | Formulaire dépôt + barème des frais |
| `app/Views/client/withdraw.php` | Créé | Formulaire retrait + calcul frais dynamique + blocage si solde insuffisant |
| `app/Views/client/transfer.php` | Créé | Formulaire transfert + calcul frais dynamique + auto-création destinataire |
| `app/Views/client/history.php` | Créé | Historique complet avec badges colorés par type d'opération |
| `app/Views/layout/client.php` | Modifié | Layout modernisé avec sidebar active link + scripts en fin de body |

---

## Étape finale — Intégration (30 min, ensemble)

- [ ] Merge des deux branches sur `main`
- [ ] Test manuel croisé : Dev A teste le parcours client de Dev B, et inversement
- [ ] Vérifier que les frais affichés côté client correspondent aux barèmes configurés côté admin
- [ ] Compléter `Taches.md` avec le récap final des deux
- [ ] `git tag v1` + push du tag
- [ ] Livraison à 13h

---

## Répartition indicative du temps (5h)

| Bloc | Dev A | Dev B |
|---|---|---|
| Setup commun | 30 min | 30 min |
| Cœur du travail | 3h | 3h |
| Buffer / bugs | 30 min | 30 min |
| Intégration finale | 30 min | 30 min |

## Points de vigilance partagés

- **Ne jamais faire d'`UPDATE` sur `baremes_frais_historique`** — seulement des `INSERT`. Toute l'équipe doit respecter cette règle, sinon le calcul de frais historique se casse silencieusement.
- Le calcul du frais dépend de `type_operation_id` + `operateur_id` + tranche de montant + date — si un test donne un frais `NULL`, c'est probablement qu'aucune tranche ne couvre ce montant pour cet opérateur à cette date (vérifier le seed).
- Dev B dépend du seed de Dev A pour tester quoi que ce soit — communiquer dès que le seed est prêt (idéalement dans les 30 premières minutes).