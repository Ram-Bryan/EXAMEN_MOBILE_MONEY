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
    -function doLoginClient()
    -function loginAdmin()
    -function doLoginAdmin()
    -function logout()

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
    -function logout()
    -function solde()
    -function depot()
    -function retrait()
    -function transfert()
    -function historique()
    -function getBalance()
    -function dashboard()
    -function balance()
    -function deposit()
    -function doDeposit()
    -function withdraw()
    -function transfer()
    

# Intégration finale (Ensemble)

- [x] Fusionner les branches
- [x] Tester l'application
- [x] Vérifier les frais
- [x] Compléter `Taches.md`
- [x] Créer le tag `v1`
- [x] Pousser le tag
- [x] Livrer le projet




## Livraison v2 — [DATE], 17h10

**Binôme :** [Nom Dev A] / [Nom Dev B]

### Dev A — Back-office opérateur

- [ ] Migration : nouvelle table `operateurs` (nous / opérateurs externes)
- [ ] Migration : `operateur_prefixes` avec `date_debut` / `date_fin` (plusieurs préfixes actifs simultanément)
- [ ] Mise à jour des FK existantes (`clients`, `baremes_frais`) vers `operateurs(id)`
- [ ] CRUD : configuration des préfixes des autres opérateurs
- [ ] Table `commissions` / `commissions_historique` (% commission transferts inter-opérateurs)
- [ ] Formulaire admin : configurer/modifier le % de commission
- [ ] Écran "situation des gains" : séparation notre opérateur / autres opérateurs
- [ ] Écran "situation des montants à envoyer" à chaque opérateur externe

### Dev B — Espace client

- [ ] Option "inclure le frais de retrait" au moment de l'envoi (`transactions.frais_inclus`)
- [ ] Adaptation du calcul de solde selon `frais_inclus`
- [ ] Formulaire d'envoi multiple (plusieurs numéros, montant divisé)
- [ ] Calcul du frais + commission pour un transfert vers un autre opérateur

### Commun

- [ ] `base.sql` mis à jour avec le schéma v2 complet
- [ ] Vues mises à jour : gains séparés, montants à envoyer
- [ ] Tests croisés (chacun teste le module de l'autre)
- [ ] Tag `v2` pushé sur `main`

---
