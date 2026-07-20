<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="dashboard-header">
    <h1><i class="fas fa-chart-line"></i> Dashboard Administrateur</h1>
    <p>Bienvenue sur l'interface d'administration</p>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-user-cog"></i>
            <div class="number"><?= $totalOperators ?? 0 ?></div>
            <div>Opérateurs actifs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-users"></i>
            <div class="number"><?= $totalClients ?? 0 ?></div>
            <div>Clients</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-exchange-alt"></i>
            <div class="number"><?= number_format($totalTransactions ?? 0) ?></div>
            <div>Transactions</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-money-bill-wave"></i>
            <div class="number"><?= number_format($totalVolume ?? 0, 0, ',', ' ') ?> Ar</div>
            <div>Volume total</div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list"></i> Dernières transactions</h5>
        <a href="<?= base_url('admin/transactions') ?>" class="btn btn-orange btn-sm float-end">Voir tout</a>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Frais</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($recentTransactions) && !empty($recentTransactions)): ?>
                    <?php foreach($recentTransactions as $transaction): ?>
                    <tr>
                        <td>#<?= $transaction['id'] ?></td>
                        <td><?= $transaction['client_phone'] ?></td>
                        <td>
                            <span class="badge <?= $transaction['type'] == 'deposit' ? 'bg-success' : ($transaction['type'] == 'withdraw' ? 'bg-danger' : 'bg-info') ?>">
                                <?= ucfirst($transaction['type']) ?>
                            </span>
                        </td>
                        <td><?= number_format($transaction['amount'], 0, ',', ' ') ?> Ar</td>
                        <td><?= number_format($transaction['fee'], 0, ',', ' ') ?> Ar</td>
                        <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
                        <td><span class="badge bg-success">Complété</span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Aucune transaction récente</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>