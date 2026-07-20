<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> | Mobile Money</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-wallet"></i> MOB-MONEY</h3>
            <small style="opacity:0.8; font-size:12px;">Administration</small>
        </div>

        <ul class="components">
            <li class="<?= (url_is('admin/dashboard') || url_is('admin')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="<?= url_is('admin/operators*') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/operators') ?>"><i class="fas fa-network-wired"></i> Opérateurs & Barèmes</a>
            </li>
            <li class="<?= url_is('admin/clients') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/clients') ?>"><i class="fas fa-users"></i> Comptes Clients</a>
            </li>
            <li class="<?= url_is('admin/commissions') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/commissions') ?>"><i class="fas fa-percent"></i> Commissions</a>
            </li>
            <li class="<?= url_is('admin/transactions') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/transactions') ?>"><i class="fas fa-exchange-alt"></i> Historique</a>
            </li>
            <li class="<?= url_is('admin/gains') ? 'active' : '' ?>">
                <a href="<?= base_url('admin/gains') ?>"><i class="fas fa-chart-pie"></i> Situation des Gains</a>
            </li>
            <li>
                <a href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <div class="navbar-custom">
            <div>
                <h5 class="m-0 fw-bold" style="color: var(--text-dark);"><?= esc($title ?? 'Back-office') ?></h5>
            </div>
            <div class="user-profile">
                <span class="fw-semibold" style="font-size:14px; color: var(--text-muted);">Admin (<?= session()->get('email') ?>)</span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=16a34a&color=fff" alt="Admin Avatar">
            </div>
        </div>

        <div class="main-container">
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
