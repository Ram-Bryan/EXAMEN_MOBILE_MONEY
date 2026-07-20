<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Mon Compte' ?> - Client</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?= view('header') ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Client Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= base_url('client/dashboard') ?>">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <a class="nav-link" href="<?= base_url('client/balance') ?>">
                        <i class="fas fa-wallet"></i> Solde
                    </a>
                    <a class="nav-link" href="<?= base_url('client/deposit') ?>">
                        <i class="fas fa-plus-circle"></i> Dépôt
                    </a>
                    <a class="nav-link" href="<?= base_url('client/withdraw') ?>">
                        <i class="fas fa-minus-circle"></i> Retrait
                    </a>
                    <a class="nav-link" href="<?= base_url('client/transfer') ?>">
                        <i class="fas fa-exchange-alt"></i> Transfert
                    </a>
                    <a class="nav-link" href="<?= base_url('client/history') ?>">
                        <i class="fas fa-history"></i> Historique
                    </a>
                </nav>
            </div>
            
            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
    
    <?= view('footer') ?>
    
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
</body>
</html>