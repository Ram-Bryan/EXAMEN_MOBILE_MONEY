# Examen Projet Final — S4 Info et Design
**Juillet 2026 — Binôme**

## Contraintes techniques

- Langage : **PHP avec CodeIgniter 4**
- Base de données : **SQLite embarqué**
- Front : **HTML, CSS, JS**, Bootstrap ou équivalent
- Livraison : sous forme de **tag Git** dans un dépôt public (GitHub ou GitLab)

## Déroulement

- Le sujet est dévoilé en plusieurs parties.
- Une livraison (tag public) est attendue à chaque partie.
- **Fichier `Taches.md`** à la racine du projet : lister les travaux effectués par chaque étudiant, à chaque livraison (on ajoute, on ne remplace pas).
- **Fichier `base.sql`** unique à la racine : contient tous les scripts de création des tables, vues, et données de seed.
- Informations du binôme à soumettre via ce formulaire (une seule fois, en tout début de projet) : https://forms.gle/nCv6xJYHVvVJj2FKA — les tags seront récupérés à partir de l'URL fournie.
- La version finale doit être sur la branche **main**.

### Noms de tags à utiliser

| Version | Tag |
|---|---|
| Version 1 | `v1` |
| Version 2 | `v2` |
| Version 3 | `v3` |

## Thème

Un système qui simule un **opérateur de mobile money**.

---

## Version 1

**Livraison : 13h — tag `v1`**

### Côté opérateur (back-office)

- [ ] Configuration des préfixes valables de l'opérateur (ex : `033`, `037`)
- [ ] Création de types d'opérations (dépôt, retrait, transfert) avec des barèmes de frais par tranche de montant, **modifiables**. Exemple de barème :

| Tranche de montant (Ar) | Frais (Ar) |
|---|---|
| 100 – 1 000 | 50 |
| 1 001 – 5 000 | 50 |
| 5 001 – 10 000 | 100 |
| 10 001 – 25 000 | 200 |
| 25 001 – 50 000 | 400 |
| 50 001 – 100 000 | 800 |
| 100 001 – 250 000 | 1 500 |
| 250 001 – 500 000 | 1 500 |
| 500 001 – 1 000 000 | 2 500 |
| 1 000 001 – 2 000 000 | 3 000 |

- [ ] Situation des gains via les différents frais (retrait et transfert)
- [ ] Situation des comptes clients

### Côté client

- [ ] Login automatique avec le numéro de téléphone — **pas d'inscription au préalable**
- [ ] Opérations disponibles :
  - [ ] Voir le solde
  - [ ] Faire un dépôt (supposé automatique, pas de validation d'agent)
  - [ ] Faire un retrait (supposé automatique)
  - [ ] Faire un transfert
  - [ ] Voir les historiques (des transactions)

---

## Résumé express — ce qu'il faut livrer pour `v1`

1. Back-office : CRUD préfixes + CRUD types d'opération/barèmes de frais (par tranche, modifiable sans perdre l'historique)
2. Back-office : écran gains (frais collectés) + écran comptes clients (soldes)
3. Client : login auto par numéro (création de compte si numéro inconnu)
4. Client : solde, dépôt, retrait, transfert, historique
5. `Taches.md` à jour
6. `base.sql` à jour (tables + vues + seed)
7. Tag `v1` poussé sur le dépôt public, branche `main`