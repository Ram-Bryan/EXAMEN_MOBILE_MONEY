<?php
$page_title = 'Dashboard Opérateur';
ob_start();
?>

<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-user-cog text-green"></i> Dashboard Opérateur</h2>
    <p class="text-muted">Gestion des opérations mobile money</p>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon green"><i class="fas fa-users"></i></div>
            <div class="details">
                <h3>567</h3>
                <p>Clients actifs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon blue"><i class="fas fa-exchange-alt"></i></div>
            <div class="details">
                <h3>2,345</h3>
                <p>Transactions aujourd'hui</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon purple"><i class="fas fa-money-bill-wave"></i></div>
            <div class="details">
                <h3>5,678,901 Ar</h3>
                <p>Volume total</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="icon orange"><i class="fas fa-coins"></i></div>
            <div class="details">
                <h3>234,567 Ar</h3>
                <p>Gains totaux</p>
            </div>
        </div>
    </div>
</div>

<!-- Prefix Configuration -->
<div class="card mb-4">
    <div class="card-header">
        <span><i class="fas fa-code me-2"></i> Configuration des préfixes</span>
        <button class="btn btn-primary-custom btn-sm" onclick="editPrefixes()">
            <i class="fas fa-edit"></i> Modifier
        </button>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Préfixes actifs</label>
                <div>
                    <span class="badge bg-green" style="padding:6px 14px; font-size:14px;">033</span>
                    <span class="badge bg-green" style="padding:6px 14px; font-size:14px;">034</span>
                    <span class="badge bg-green" style="padding:6px 14px; font-size:14px;">037</span>
                    <span class="badge bg-green" style="padding:6px 14px; font-size:14px;">038</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Operation Types -->
<div class="card mb-4">
    <div class="card-header">
        <span><i class="fas fa-tasks me-2"></i> Types d'opérations</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card" style="border-left: 4px solid var(--primary);">
                    <div class="icon green"><i class="fas fa-plus-circle"></i></div>
                    <div class="details">
                        <h3>Dépôt</h3>
                        <p>Frais: Variable</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left: 4px solid #dc2626;">
                    <div class="icon red"><i class="fas fa-minus-circle"></i></div>
                    <div class="details">
                        <h3>Retrait</h3>
                        <p>Frais: Variable</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left: 4px solid #2563eb;">
                    <div class="icon blue"><i class="fas fa-exchange-alt"></i></div>
                    <div class="details">
                        <h3>Transfert</h3>
                        <p>Frais: Variable</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fee Schedule -->
<div class="card">
    <div class="card-header">
        <span><i class="fas fa-table me-2"></i> Barème des frais</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table-custom w-100">
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
                <input type="text" class="form-control" name="prefixes" value="033,034,037,038" required>
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
