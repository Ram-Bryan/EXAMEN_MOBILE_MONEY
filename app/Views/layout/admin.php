<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> - Admin</title>
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
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/operators') ?>">
                        <i class="fas fa-user-cog"></i> Opérateurs
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/operations-types') ?>">
                        <i class="fas fa-tasks"></i> Types d'opérations
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/fees-config') ?>">
                        <i class="fas fa-coins"></i> Configuration frais
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/clients') ?>">
                        <i class="fas fa-users"></i> Clients
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/transactions') ?>">
                        <i class="fas fa-exchange-alt"></i> Transactions
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/gains') ?>">
                        <i class="fas fa-money-bill-wave"></i> Gains
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
    <script src="<?= base_url('assets/js/crud.js') ?>"></script>
</body>
</html>