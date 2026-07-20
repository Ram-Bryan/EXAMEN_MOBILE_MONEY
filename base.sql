-- Active le support des clés étrangères dans SQLite (à exécuter à chaque connexion)
PRAGMA foreign_keys = ON;

---
-- Table : Configurations des préfixes de l'opérateur
---
CREATE TABLE operateur_prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE, -- Ex: '033', '037'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

---
-- Table : Types d'opérations (Dépôt, Retrait, Transfert)
---
CREATE TABLE types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE, -- 'DEPOT', 'RETRAIT', 'TRANSFERT'
    nom TEXT NOT NULL
);

---
-- Table : Barèmes des frais par tranche
-- Gère les frais modifiables sans casser l'historique des anciennes opérations
---
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min REAL NOT NULL, -- Borne inférieure de la tranche
    montant_max REAL NOT NULL, -- Borne supérieure de la tranche
    frais_fixe REAL DEFAULT 0.0,
    frais_pourcentage REAL DEFAULT 0.0, -- Ex: 0.01 pour 1%
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id) ON DELETE CASCADE,
    -- Contrainte pour éviter les valeurs absurdes
    CHECK (montant_max >= montant_min AND montant_min >= 0)
);

---
-- Table : Comptes Clients
-- Pas d'inscription préalable : un compte est créé dès sa première interaction
---
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone TEXT NOT NULL UNIQUE, -- Servira d'identifiant unique pour le login auto
    solde REAL DEFAULT 0.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CHECK (solde >= 0) -- Un compte client ne peut pas être à découvert
);

---
-- Table : Historique des Opérations
-- Stocke toutes les transactions et fige les frais au moment de l'impact
---
CREATE TABLE operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    expediteur_id INTEGER, -- Client qui initie (NULL si dépôt externe automatique)
    destinataire_id INTEGER, -- Client qui reçoit (NULL si retrait automatique)
    montant_brut REAL NOT NULL, -- Le montant saisi par le client
    frais_operateur REAL DEFAULT 0.0, -- Les frais appliqués selon le barème de l'époque
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id),
    FOREIGN KEY (expediteur_id) REFERENCES clients(id),
    FOREIGN KEY (destinataire_id) REFERENCES clients(id),
    CHECK (montant_brut > 0)
);