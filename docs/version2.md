# Version 2 — Mobile Money

**Livraison à 17h10 — tag `v2`**

## Côté opérateur

- [ ] Configuration des préfixes valables pour les autres opérateurs (ex : `032`, `031`, …)
- [ ] Configuration d'un **% de commission** en plus, pour les transferts vers les autres opérateurs
- [ ] Sur la page "Situation gain via les différents frais" : séparer **notre opérateur** et **les autres opérateurs**
- [ ] Situation des montants à envoyer à chaque opérateur (settlement inter-opérateurs)

## Côté client

- [ ] Option : inclure le frais de retrait lors de l'envoi (le montant tapé peut inclure le frais, ou non)
- [ ] Envoi multiple vers plusieurs numéros (le montant est divisé entre chaque destinataire)

---

## Changements de conception à effectuer

### 1. Séparer `operateurs` et `operateur_prefixes` (structurant)

Un opérateur peut avoir plusieurs préfixes actifs en même temps (033 **et** 037 pour nous). Il faut donc une vraie table `operateurs`, et `operateur_prefixes` devient une table à part avec une **période de validité** par préfixe (pas un historique façon "dernière valeur gagne" comme pour les frais — ici plusieurs valeurs coexistent) :

```sql
CREATE TABLE operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    est_notre_operateur INTEGER NOT NULL DEFAULT 0, -- 1 = nous, 0 = externe
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    prefixe TEXT NOT NULL,
    date_debut DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_fin DATETIME, -- NULL = toujours actif
    FOREIGN KEY (operateur_id) REFERENCES operateurs(id)
);
```

⚠️ Toutes les FK qui pointaient vers `operateur_prefixes(id)` (dans `clients`, `baremes_frais`, `commissions`) doivent maintenant pointer vers `operateurs(id)`. C'est le premier changement à faire, avant tout le reste — il casse les fondations de v1.

### 2. Commission % sur transferts inter-opérateurs (versionné, comme les barèmes)

Une seule valeur vraie à la fois → même pattern que `baremes_frais`, INSERT-only :

```sql
CREATE TABLE commissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_destination_id INTEGER NOT NULL,
    FOREIGN KEY (operateur_destination_id) REFERENCES operateurs(id)
);

CREATE TABLE commissions_historique (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    commission_id INTEGER NOT NULL,
    pourcentage REAL NOT NULL, -- ex: 1.5 = 1.5%
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commission_id) REFERENCES commissions(id)
);
```

Frais total d'un transfert inter-opérateur = frais fixe de la tranche + `(montant_brut × pourcentage / 100)`.

### 3. Frais inclus ou non lors d'un envoi

Fait propre à la transaction, stocké directement (comme `frais_applique` l'aurait été) :

```sql
ALTER TABLE transactions ADD COLUMN frais_inclus INTEGER NOT NULL DEFAULT 0;
-- 0 = frais en plus du montant tapé (comportement v1)
-- 1 = frais inclus dans le montant tapé
```

### 4. Envoi multiple vers plusieurs numéros

Pas de table de groupe nécessaire pour que les soldes soient corrects — plusieurs `INSERT` indépendants dans `transactions` suffisent (montant divisé par le nombre de destinataires, un frais calculé séparément pour chacun).

### 5. Vue "situation gains" séparée nous / autres opérateurs

```sql
SELECT
    o.est_notre_operateur,
    t.code AS type_operation,
    SUM(tf.frais_applique) AS total_gains
FROM v_transactions_frais tf
JOIN types_operation t ON t.id = tf.type_operation_id
JOIN clients cd ON cd.id = tf.destinataire_id
JOIN operateurs o ON o.id = cd.operateur_id
WHERE tf.frais_applique IS NOT NULL
GROUP BY o.est_notre_operateur, t.code;
```

### 6. Vue "montants à envoyer à chaque opérateur"

```sql
CREATE VIEW v_montants_a_envoyer AS
SELECT
    o.id AS operateur_id,
    o.nom,
    SUM(tf.montant_brut) AS montant_total_a_envoyer
FROM v_transactions_frais tf
JOIN clients cd ON cd.id = tf.destinataire_id
JOIN operateurs o ON o.id = cd.operateur_id
WHERE o.est_notre_operateur = 0
GROUP BY o.id, o.nom;
```

---

## Checklist de livraison v2

- [ ] Migration : `operateurs` + `operateur_prefixes` avec périodes de validité
- [ ] Migration : toutes les FK vers `operateurs(id)` mises à jour (`clients`, `baremes_frais`, `commissions`)
- [ ] `commissions` + `commissions_historique` + logique de calcul du frais total inter-opérateurs
- [ ] `transactions.frais_inclus` + adaptation du calcul de solde
- [ ] Écran opérateur : gains séparés nous / autres opérateurs
- [ ] Écran opérateur : montants à envoyer à chaque opérateur externe
- [ ] Écran client : case à cocher "inclure le frais" au moment de l'envoi
- [ ] Écran client : formulaire d'envoi multiple (plusieurs numéros, montant divisé)
- [ ] `Taches.md` mis à jour
- [ ] `base.sql` mis à jour (schéma v2 complet)
- [ ] Tag `v2` poussé sur `main`