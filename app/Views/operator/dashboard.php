<?php
$page_title = 'Dashboard Opérateur';
ob_start();
?>

<div class="dashboard-header">
    <h1><i class="fas fa-user-cog"></i> Dashboard Opérateur</h1>
    <p>Gestion des opérations mobile money</p>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-users"></i>
            <div class="number">567</div>
            <div>Clients actifs</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-exchange-alt"></i>
            <div class="number">2,345</div>
            <div>Transactions aujourd'hui</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-money-bill-wave"></i>
            <div class="number">5,678,901 Ar</div>
            <div>Volume total</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <i class="fas fa-coins"></i>
            <div class="number">234,567 Ar</div>
            <div>Gains totaux</div>
        </div>
    </div>
</div>

<!-- Prefix Configuration -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-code"></i> Configuration des préfixes</h5>
        <button class="btn btn-orange btn-sm float-end" onclick="editPrefixes()">
            <i class="fas fa-edit"></i> Modifier
        </button>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label>Préfixes actifs</label>
                <div class="prefix-badges">
                    <span class="badge bg-orange">033</span>
                    <span class="badge bg-orange">034</span>
                    <span class="badge bg-orange">037</span>
                    <span class="badge bg-orange">038</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Operation Types -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-tasks"></i> Types d'opérations</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card bg-success">
                    <i class="fas fa-plus-circle"></i>
                    <div class="number">Dépôt</div>
                    <div>Frais: Variable</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card bg-danger">
                    <i class="fas fa-minus-circle"></i>
                    <div class="number">Retrait</div>
                    <div>Frais: Variable</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card bg-info">
                    <i class="fas fa-exchange-alt"></i>
                    <div class="number">Transfert</div>
                    <div>Frais: Variable</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fee Schedule -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-table"></i> Barème des frais</h5>
        <a href="/operator/fees-config.php" class="btn btn-orange btn-sm float-end">
            <i class="fas fa-cog"></i> Configurer
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Montant</th>
                        <th>Dépôt</th>
                        <th>Retrait</th>
                        <th>Transfert</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>100 - 1,000 Ar</td>
                        <td>50 Ar</td>
                        <td>50 Ar</td>
                        <td>50 Ar</td>
                    </tr>
                    <tr>
                        <td>1,000 - 5,000 Ar</td>
                        <td>50 Ar</td>
                        <td>50 Ar</td>
                        <td>50 Ar</td>
                    </tr>
                    <tr>
                        <td>5,000 - 10,000 Ar</td>
                        <td>100 Ar</td>
                        <td>100 Ar</td>
                        <td>100 Ar</td>
                    </tr>
                    <tr>
                        <td>10,000 - 25,000 Ar</td>
                        <td>200 Ar</td>
                        <td>200 Ar</td>
                        <td>200 Ar</td>
                    </tr>
                    <tr>
                        <td>25,000 - 50,000 Ar</td>
                        <td>400 Ar</td>
                        <td>400 Ar</td>
                        <td>400 Ar</td>
                    </tr>
                    <tr>
                        <td>50,000 - 100,000 Ar</td>
                        <td>800 Ar</td>
                        <td>800 Ar</td>
                        <td>800 Ar</td>
                    </tr>
                    <tr>
                        <td>100,000 - 250,000 Ar</td>
                        <td>1,500 Ar</td>
                        <td>1,500 Ar</td>
                        <td>1,500 Ar</td>
                    </tr>
                    <tr>
                        <td>250,000 - 500,000 Ar</td>
                        <td>2,500 Ar</td>
                        <td>2,500 Ar</td>
                        <td>2,500 Ar</td>
                    </tr>
                    <tr>
                        <td>500,000 - 1,000,000 Ar</td>
                        <td>3,000 Ar</td>
                        <td>3,000 Ar</td>
                        <td>3,000 Ar</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editPrefixes() {
    openModal('prefixModal', 'Configuration des préfixes', `
        <form id="prefixForm">
            <div class="mb-3">
                <label class="form-label">Préfixes (séparés par des virgules)</label>
                <input type="text" class="form-control required" name="prefixes" value="033,034,037,038" required>
                <small class="text-muted">Exemple: 033,034,037,038</small>
            </div>
        </form>
    `);
}
</script>

<?php
$content = ob_get_clean();
include '../includes/layouts/operator.php';
?>