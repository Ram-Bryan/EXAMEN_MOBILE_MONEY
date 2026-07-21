<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Mon Compte' ?> — Mobile Money</title>
    <meta name="description" content="Espace client Mobile Money — gérez vos opérations en toute sécurité.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-wallet"></i> MOB-MONEY</h3>
            <small style="opacity:0.8; font-size:12px;"><?= esc(session()->get('phone') ?? '') ?></small>
        </div>

        <?php
            $uri = service('uri')->getPath();
            function activeLink(string $uri, string $path): string {
                return strpos($uri, $path) !== false ? 'active' : '';
            }
        ?>

        <ul class="components">
            <li class="<?= activeLink($uri, 'client/dashboard') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/dashboard') ?>"><i class="fas fa-home"></i> Accueil</a>
            </li>
            <li class="<?= activeLink($uri, 'client/balance') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/balance') ?>"><i class="fas fa-wallet"></i> Mon Solde</a>
            </li>
            <li class="<?= activeLink($uri, 'client/deposit') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/deposit') ?>"><i class="fas fa-plus-circle"></i> Dépôt</a>
            </li>
            <li class="<?= activeLink($uri, 'client/withdraw') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/withdraw') ?>"><i class="fas fa-minus-circle"></i> Retrait</a>
            </li>
            <li class="<?= activeLink($uri, 'client/transfer') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/transfer') ?>"><i class="fas fa-exchange-alt"></i> Transfert</a>
            </li>
            <li class="<?= activeLink($uri, 'client/history') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/history') ?>"><i class="fas fa-history"></i> Historique</a>
            </li>
            
            <li class="<?= activeLink($uri, 'client/epargne') === 'active' ? 'active' : '' ?>">
                <a href="<?= base_url('client/epargne') ?>"><i class="fas fa-history"></i> Epargne</a>
            </li>

            <li>
                <a href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div id="content">
        <div class="navbar-custom">
            <div>
                <h5 class="m-0 fw-bold" style="color: var(--text-dark);"><?= $page_title ?? 'Mon Compte' ?></h5>
            </div>
            <div class="user-profile">
                <span class="badge px-3 py-2" style="background: var(--primary-badge-bg); color: var(--primary); border-radius: 20px; font-size: 13px;">
                    <i class="fas fa-user-circle me-1"></i> <?= esc(session()->get('name') ?? 'Client') ?>
                </span>
                <a href="<?= base_url('logout') ?>" class="text-danger text-decoration-none" style="font-size: 13px;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="main-container animate-fade-in">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/crud.js') ?>"></script>
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
    <script>
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
