<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon blue">
                <i class="fas fa-network-wired"></i>
            </div>
            <div class="details">
                <h3><?= esc($totalOperators ?? 0) ?></h3>
                <p>Préfixes opérateurs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon green">
                <i class="fas fa-users"></i>
            </div>
            <div class="details">
                <h3><?= esc($totalClients ?? 0) ?></h3>
                <p>Clients actifs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="icon purple">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="details">
                <h3><?= number_format($totalTransactions ?? 0) ?></h3>
                <p>Transactions totales</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="premium-card">
            <div class="card-header">
                <span><i class="fas fa-chart-pie me-2"></i> Situation des gains</span>
                <a href="<?= base_url('admin/gains') ?>" class="btn btn-primary-custom btn-sm">Voir tout</a>
            </div>
            <div class="card-body text-center py-4">
                <p class="text-muted mb-3" style="font-size: 14px;">Visualisez les frais collectés par type d'opération.</p>
                <a href="<?= base_url('admin/gains') ?>" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-right me-2"></i>Accéder aux gains
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="premium-card">
            <div class="card-header">
                <span><i class="fas fa-wallet me-2"></i> Comptes clients</span>
                <a href="<?= base_url('admin/clients') ?>" class="btn btn-primary-custom btn-sm">Voir tout</a>
            </div>
            <div class="card-body text-center py-4">
                <p class="text-muted mb-3" style="font-size: 14px;">Consultez le solde actuel de chaque client.</p>
                <a href="<?= base_url('admin/clients') ?>" class="btn btn-primary-custom">
                    <i class="fas fa-arrow-right me-2"></i>Voir les soldes
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>