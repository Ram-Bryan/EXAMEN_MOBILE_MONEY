# transaction.md — Logique détaillée par opération (v1 + v2)

Ce document sert à vérifier, pour chaque opération, **ce qu'il faut calculer** et **ce qu'il faut insérer** dans `transactions`.
Notation : `frais_fixe` = valeur trouvée dans `baremes_frais_historique` pour la tranche concernée. `commission_pct` = valeur trouvée dans `commissions_historique`.

> ⚠️ Points marqués **[À VALIDER EN BINÔME]** : le sujet ne précise pas explicitement la règle, j'ai choisi une interprétation raisonnable — confirmez-la ensemble avant de coder, sinon vos calculs de solde ne matcheront pas entre vous.

---

## 1. Dépôt (DEPOT)

**Entrée :** `client_id`, `montant`

1. Pas de recherche de frais (le dépôt est gratuit — cf. sujet v1)
2. Vérifications : aucune (pas de solde à vérifier, l'argent arrive)
3. `INSERT INTO transactions` :
   ```
   type_operation_id = DEPOT
   expediteur_id     = NULL
   destinataire_id   = client_id
   montant_brut      = montant
   frais_inclus      = 0
   ```
4. Effet sur le solde : `solde_client += montant`

---

## 2. Retrait (RETRAIT)

**Entrée :** `client_id`, `montant`

1. `frais = lookup(baremes_frais, type=RETRAIT, operateur=client.operateur_id, tranche contient montant, date=maintenant)`
2. Vérification : `solde_client >= montant + frais` → sinon rejeter ("solde insuffisant")
3. `INSERT INTO transactions` :
   ```
   type_operation_id = RETRAIT
   expediteur_id     = client_id
   destinataire_id   = NULL
   montant_brut      = montant
   frais_inclus      = 0
   ```
4. Effet sur le solde : `solde_client -= (montant + frais)`

*(Le sujet ne propose l'option "frais inclus" que pour l'envoi/transfert, pas pour le retrait — donc `frais_inclus` reste toujours à `0` ici.)*

---

## 3. Transfert — même opérateur, frais NON inclus (comportement par défaut)

**Entrée :** `expediteur_id`, `destinataire_id`, `montant`, `frais_inclus = 0`

1. `frais = lookup(baremes_frais, type=TRANSFERT, operateur=expediteur.operateur_id, tranche contient montant, date=maintenant)`
2. Pas de commission (même opérateur des deux côtés)
3. Vérification : `solde_expediteur >= montant + frais`
4. `INSERT INTO transactions` :
   ```
   type_operation_id = TRANSFERT
   expediteur_id     = expediteur_id
   destinataire_id   = destinataire_id
   montant_brut      = montant
   frais_inclus      = 0
   ```
5. Effets :
   - `solde_expediteur -= (montant + frais)`
   - `solde_destinataire += montant` (reçoit le montant plein)

---

## 4. Transfert — même opérateur, frais INCLUS (nouveau v2)

**Entrée :** `expediteur_id`, `destinataire_id`, `montant`, `frais_inclus = 1`

1. `frais = lookup(baremes_frais, type=TRANSFERT, operateur=expediteur.operateur_id, tranche contient montant, date=maintenant)`
   **[À VALIDER]** : la tranche est cherchée sur `montant` tapé (montant brut), pas sur le montant net après frais.
2. Vérification : `solde_expediteur >= montant` (ici pas besoin d'ajouter le frais, il est prélevé sur le montant lui-même)
3. `INSERT INTO transactions` :
   ```
   type_operation_id = TRANSFERT
   expediteur_id     = expediteur_id
   destinataire_id   = destinataire_id
   montant_brut      = montant
   frais_inclus      = 1
   ```
4. Effets :
   - `solde_expediteur -= montant`
   - `solde_destinataire += (montant - frais)`

---

## 5. Transfert vers un AUTRE opérateur, frais NON inclus

**Entrée :** `expediteur_id`, `destinataire_id`, `montant`, `frais_inclus = 0`
Condition de déclenchement : `expediteur.operateur_id != destinataire.operateur_id`

1. `frais_fixe = lookup(baremes_frais, type=TRANSFERT, operateur=expediteur.operateur_id, tranche contient montant, date=maintenant)`
2. `commission_pct = lookup(commissions_historique, operateur_destination=destinataire.operateur_id, date=maintenant)`
3. `commission_montant = montant * commission_pct / 100`
4. `frais_total = frais_fixe + commission_montant`
5. Vérification : `solde_expediteur >= montant + frais_total`
6. `INSERT INTO transactions` :
   ```
   type_operation_id = TRANSFERT
   expediteur_id     = expediteur_id
   destinataire_id   = destinataire_id
   montant_brut      = montant
   frais_inclus      = 0
   ```
7. Effets :
   - `solde_expediteur -= (montant + frais_total)`
   - `solde_destinataire += montant`
   - **[À VALIDER]** montant dû à l'opérateur externe (settlement) = `montant` (le montant plein reçu par leur client — nous gardons `frais_total` comme gain/commission de notre côté)

---

## 6. Transfert vers un AUTRE opérateur, frais INCLUS

**Entrée :** `expediteur_id`, `destinataire_id`, `montant`, `frais_inclus = 1`

1. `frais_fixe = lookup(baremes_frais, type=TRANSFERT, operateur=expediteur.operateur_id, tranche contient montant, date=maintenant)`
2. `commission_pct = lookup(commissions_historique, operateur_destination=destinataire.operateur_id, date=maintenant)`
3. `commission_montant = montant * commission_pct / 100`
4. `frais_total = frais_fixe + commission_montant`
5. Vérification : `solde_expediteur >= montant`
6. `INSERT INTO transactions` :
   ```
   type_operation_id = TRANSFERT
   expediteur_id     = expediteur_id
   destinataire_id   = destinataire_id
   montant_brut      = montant
   frais_inclus      = 1
   ```
7. Effets :
   - `solde_expediteur -= montant`
   - `solde_destinataire += (montant - frais_total)`
   - **[À VALIDER]** montant dû à l'opérateur externe = `montant - frais_total` (montant net réellement crédité chez eux)

---

## 7. Envoi multiple vers plusieurs numéros

**Entrée :** `expediteur_id`, `montant_total`, `[destinataire_1, destinataire_2, ..., destinataire_n]`, `frais_inclus`

1. `montant_par_destinataire = montant_total / n`
2. Pour **chaque** destinataire, répéter l'un des scénarios 3 à 6 ci-dessus (selon que ce destinataire précis est sur le même opérateur ou non), avec `montant = montant_par_destinataire`
3. Résultat : **n lignes indépendantes** dans `transactions`, chacune avec son propre `frais_applique` recalculé via `v_transactions_frais`
4. Pas de table de groupe — la somme des `n` lignes suffit à retrouver le montant total envoyé si besoin (`WHERE expediteur_id = X AND date_transaction = ... GROUP BY`)

**Vérification globale avant de lancer les n insert :** `solde_expediteur >= montant_total + somme_de_tous_les_frais_prevus` (calculer tous les frais **avant** de commencer les inserts, pour rejeter l'opération entière si le solde est insuffisant — éviter d'insérer 2 transactions sur 3 puis échouer sur la 3e).

---

## Récapitulatif — tableau de vérification rapide

| Opération | expediteur_id | destinataire_id | frais_inclus | Débit expéditeur | Crédit destinataire |
|---|---|---|---|---|---|
| Dépôt | NULL | client | 0 | — | + montant |
| Retrait | client | NULL | 0 | − (montant + frais) | — |
| Transfert même opérateur, frais en plus | client A | client B | 0 | − (montant + frais) | + montant |
| Transfert même opérateur, frais inclus | client A | client B | 1 | − montant | + (montant − frais) |
| Transfert autre opérateur, frais en plus | client A | client B (externe) | 0 | − (montant + frais + commission) | + montant |
| Transfert autre opérateur, frais inclus | client A | client B (externe) | 1 | − montant | + (montant − frais − commission) |

## Rappel des règles de calcul de frais (ne pas oublier en testant)

- Le frais fixe est toujours cherché par **tranche de `montant`** (le montant tapé/brut), jamais sur le montant net.
- Le frais appliqué est toujours celui **valide à la date de la transaction** (`date_modif <= date_transaction`, la plus récente).
- La commission ne s'applique **que** si `expediteur.operateur_id != destinataire.operateur_id`.
- Aucun `UPDATE` n'est jamais fait sur `baremes_frais_historique` ni `commissions_historique` — seulement des `INSERT`.