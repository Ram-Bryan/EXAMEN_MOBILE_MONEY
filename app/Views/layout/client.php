<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Mon Compte' ?> — Mobile Money</title>
    <meta name="description" content="Espace client Mobile Money — gérez vos opérations en toute sécurité.">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        body { background: #f0f2f5; }
        .sidebar {
            background: #fff;
            min-height: 100vh;
            border-right: 1px solid #e8ecef;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.04);
        }
        .sidebar-brand {
            background: linear-gradient(135deg, #FF6B00, #FF8C38);
            padding: 22px 20px;
            color: white;
            text-decoration: none;
            display: block;
        }
        .sidebar-brand h5 { margin: 0; font-weight: 700; font-size: 17px; }
        .sidebar-brand small { opacity: 0.85; font-size: 12px; }
        .sidebar-menu { padding: 12px 10px; }
        .sidebar-menu .nav-link {
            color: #555;
            padding: 11px 16px;
            margin: 3px 0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-menu .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .sidebar-menu .nav-link:hover { background: #fff5ee; color: #FF6B00; }
        .sidebar-menu .nav-link.active { background: linear-gradient(135deg, #FF6B00, #FF8C38); color: white; box-shadow: 0 4px 12px rgba(255,107,0,0.3); }
        .sidebar-menu .nav-link.active:hover { background: linear-gradient(135deg, #FF6B00, #FF8C38); }
        .sidebar-divider { border: none; border-top: 1px solid #f0f0f0; margin: 8px 16px; }
        .sidebar-logout {
            padding: 12px 20px 20px;
        }
        .main-content {
            padding: 30px;
            min-height: 100vh;
        }
        .text-orange { color: #FF6B00 !important; }
        .bg-orange { background: linear-gradient(135deg, #FF6B00, #FF8C38) !important; }
        .badge.bg-orange { background: linear-gradient(135deg, #FF6B00, #FF8C38) !important; }
        .navbar-top {
            background: white;
            border-bottom: 1px solid #e8ecef;
            padding: 14px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.4s ease; }
        .card:hover { transform: translateY(-2px); transition: transform 0.25s ease; }
        @media (max-width: 768px) {
            .sidebar { min-height: auto; }
            .main-content { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="container-fluid g-0">
    <div class="row g-0">
        <!-- ===== Sidebar ===== -->
        <div class="col-md-3 col-lg-2 sidebar d-flex flex-column">
            <a class="sidebar-brand" href="<?= base_url('client/dashboard') ?>">
                <h5><i class="fas fa-wallet me-2"></i>Mobile Money</h5>
                <small><?= esc(session()->get('phone') ?? '') ?></small>
            </a>

            <div class="sidebar-menu flex-grow-1">
                <?php
                    $uri = service('uri')->getPath();
                    function activeLink(string $uri, string $path): string {
                        return strpos($uri, $path) !== false ? 'active' : '';
                    }
                ?>
                <a class="nav-link <?= activeLink($uri, 'client/dashboard') ?>" href="<?= base_url('client/dashboard') ?>">
                    <i class="fas fa-home"></i> Accueil
                </a>
                <a class="nav-link <?= activeLink($uri, 'client/balance') ?>" href="<?= base_url('client/balance') ?>">
                    <i class="fas fa-wallet"></i> Mon Solde
                </a>
                <hr class="sidebar-divider">
                <a class="nav-link <?= activeLink($uri, 'client/deposit') ?>" href="<?= base_url('client/deposit') ?>">
                    <i class="fas fa-plus-circle"></i> Dépôt
                </a>
                <a class="nav-link <?= activeLink($uri, 'client/withdraw') ?>" href="<?= base_url('client/withdraw') ?>">
                    <i class="fas fa-minus-circle"></i> Retrait
                </a>
                <a class="nav-link <?= activeLink($uri, 'client/transfer') ?>" href="<?= base_url('client/transfer') ?>">
                    <i class="fas fa-exchange-alt"></i> Transfert
                </a>
                <hr class="sidebar-divider">
                <a class="nav-link <?= activeLink($uri, 'client/history') ?>" href="<?= base_url('client/history') ?>">
                    <i class="fas fa-history"></i> Historique
                </a>
            </div>

            <div class="sidebar-logout">
                <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger w-100" style="border-radius: 8px;">
                    <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- ===== Main Content Area ===== -->
        <div class="col-md-9 col-lg-10 d-flex flex-column">
            <!-- Top Bar -->
            <div class="navbar-top">
                <h6 class="mb-0 text-muted"><?= $page_title ?? 'Mon Compte' ?></h6>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge px-3 py-2" style="background: rgba(255,107,0,0.1); color: #FF6B00; border-radius: 20px; font-size: 13px;">
                        <i class="fas fa-user-circle me-1"></i> <?= esc(session()->get('name') ?? 'Client') ?>
                    </span>
                    <a href="<?= base_url('logout') ?>" class="text-danger text-decoration-none" style="font-size: 13px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>

            <div class="main-content animate-fade-in">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Page Content injected here -->
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts (after body so views can define inline scripts) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script src="<?= base_url('assets/js/crud.js') ?>"></script>
<script src="<?= base_url('assets/js/notifications.js') ?>"></script>
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); }, 500);
        });
    }, 5000);
</script>
</body>
</html>