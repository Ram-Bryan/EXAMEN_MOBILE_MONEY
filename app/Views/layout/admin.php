<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?= view('header') ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" style="background: var(--primary-bg); min-height: calc(100vh - 56px); border-right: 1px solid var(--primary-border); padding-top: 20px;">
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= base_url('admin/dashboard') ?>" style="color: #333; padding: 12px 16px; border-radius: 8px; margin: 4px 8px; transition: all 0.2s;">
                        <i class="fas fa-chart-line me-2" style="color: var(--primary);"></i> Dashboard
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/operators') ?>" style="color: #333; padding: 12px 16px; border-radius: 8px; margin: 4px 8px; transition: all 0.2s;">
                        <i class="fas fa-user-cog me-2" style="color: var(--primary);"></i> Opérateurs
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/clients') ?>" style="color: #333; padding: 12px 16px; border-radius: 8px; margin: 4px 8px; transition: all 0.2s;">
                        <i class="fas fa-users me-2" style="color: var(--primary);"></i> Clients
                    </a>
                    <a class="nav-link" href="<?= base_url('admin/gains') ?>" style="color: #333; padding: 12px 16px; border-radius: 8px; margin: 4px 8px; transition: all 0.2s;">
                        <i class="fas fa-money-bill-wave me-2" style="color: var(--primary);"></i> Gains
                    </a>
                </nav>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-container">
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
