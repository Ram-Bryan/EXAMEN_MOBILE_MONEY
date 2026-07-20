<?php
// Simulate auto-login with phone number
$phone_number = $_GET['phone'] ?? $_SESSION['phone'] ?? '0341234567';
$page_title = 'Mon Compte';
ob_start();
?>

<div class="dashboard-header">
    <h1><i class="fas fa-mobile-alt"></i> Mon Compte Mobile Money</h1>
    <p>Bienvenue, <?php echo $phone_number; ?></p>
</div>

<!-- Balance Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stats-card text-center">
            <i class="fas fa-wallet" style="font-size: 48px;"></i>
            <div class="number" style="font-size: 36px;">1,234,567 Ar</div>
            <div>Solde disponible</div>
            <button class="btn btn-light mt-3" onclick="refreshBalance()">
                <i class="fas fa-sync"></i> Actualiser
            </button>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-plus-circle" style="font-size: 48px; color: var(--orange-primary);"></i>
                <h5 class="mt-3">Dépôt</h5>
                <p>Effectuer un dépôt</p>
                <a href="/client/deposit.php" class="btn btn-orange">Dépôt</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-minus-circle" style="font-size: 48px; color: var(--orange-primary);"></i>
                <h5 class="mt-3">Retrait</h5>
                <p>Effectuer un retrait</p>
                <a href="/client/withdraw.php" class="btn btn-orange">Retrait</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-exchange-alt" style="font-size: 48px; color: var(--orange-primary);"></i>
                <h5 class="mt-3">Transfert</h5>
                <p>Transférer de l'argent</p>
                <a href="/client/transfer.php" class="btn btn-orange">Transfert</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-history" style="font-size: 48px; color: var(--orange-primary);"></i>
                <h5 class="mt-3">Historique</h5>
                <p>Voir vos transactions</p>
                <a href="/client/history.php" class="btn btn-orange">Historique</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list"></i> Dernières transactions</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Frais</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#TRX001</td>
                    <td><span class="badge bg-success">Dépôt</span></td>
                    <td>+10,000 Ar</td>
                    <td>0 Ar</td>
                    <td>2024-01-15 14:30</td>
                    <td><span class="badge bg-success">Complété</span></td>
                </tr>
                <tr>
                    <td>#TRX002</td>
                    <td><span class="badge bg-danger">Retrait</span></td>
                    <td>-25,000 Ar</td>
                    <td>400 Ar</td>
                    <td>2024-01-15 14:15</td>
                    <td><span class="badge bg-success">Complété</span></td>
                </tr>
                <tr>
                    <td>#TRX003</td>
                    <td><span class="badge bg-info">Transfert</span></td>
                    <td>-5,000 Ar</td>
                    <td>50 Ar</td>
                    <td>2024-01-15 13:45</td>
                    <td><span class="badge bg-success">Complété</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function refreshBalance() {
    $.ajax({
        url: '/api/client/balance',
        method: 'GET',
        success: function(data) {
            $('.stats-card .number').text(data.balance + ' Ar');
            notify.show('Solde actualisé avec succès', 'success');
        },
        error: function() {
            notify.show('Erreur lors de l\'actualisation du solde', 'error');
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include '../includes/layouts/client.php';
?>