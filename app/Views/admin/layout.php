<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin') ?> | Mobile Money</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2563eb; /* Blue */
            --secondary-color: #f1f5f9;
            --dark-color: #1e293b;
            --text-muted: #64748b;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            padding-top: 20px;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        #sidebar .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        #sidebar .sidebar-header h3 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 1px;
            color: #38bdf8;
        }

        #sidebar ul.components {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        #sidebar ul li a {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        #sidebar ul li a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 1.1em;
        }

        #sidebar ul li a:hover, #sidebar ul li.active a {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 4px solid #38bdf8;
        }

        /* Content Styling */
        #content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Top Navbar */
        .navbar-custom {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-custom .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-custom .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Main Container */
        .main-container {
            padding: 30px;
        }

        /* Premium Cards */
        .premium-card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .premium-card .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
            font-weight: 600;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .premium-card .card-body {
            padding: 24px;
        }

        /* Stat Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 20px;
        }

        .stat-card .icon.blue { background: #eff6ff; color: #2563eb; }
        .stat-card .icon.green { background: #f0fdf4; color: #16a34a; }
        .stat-card .icon.purple { background: #faf5ff; color: #9333ea; }
        .stat-card .icon.orange { background: #fff7ed; color: #ea580c; }

        .stat-card .details h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-card .details p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        /* Buttons */
        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s;
        }
        .btn-primary-custom:hover {
            background: #1d4ed8;
            color: white;
        }

        /* Tables */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-custom th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-custom td {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .table-custom tr:last-child td {
            border-bottom: none;
        }
        .table-custom tr:hover td {
            background: #f8fafc;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-wallet"></i> MOB-MONEY</h3>
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
                <h4 class="m-0 font-weight-bold" style="color: #0f172a;"><?= esc($title ?? 'Back-office') ?></h4>
            </div>
            <div class="user-profile">
                <span class="fw-semibold">Admin (<?= session()->get('email') ?>)</span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Admin Avatar">
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

    <!-- Bootstrap JS & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
