<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="dashboard-header mb-4">
    <h1 class="h2"><i class="fas fa-wallet text-orange"></i> Mon Solde</h1>
    <p class="text-muted">Consultez et suivez l'état de votre solde en temps réel.</p>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm text-center p-5" style="border-radius: 16px; background: white;">
            <i class="fas fa-wallet mb-3 text-orange" style="font-size: 60px;"></i>
            <h3 class="text-muted text-uppercase tracking-wider mb-2" style="font-size: 14px; font-weight: 600;">Solde Actuel</h3>
            <div class="number mb-4 text-orange" style="font-size: 48px; font-weight: 800;">
                <?= number_format($balance, 0, ',', ' ') ?> Ar
            </div>
            
            <div class="d-grid gap-2">
                <button class="btn btn-orange btn-lg" onclick="refreshBalance()">
                    <i class="fas fa-sync me-2"></i> Actualiser
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="font-weight-bold mb-0">Informations du compte</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between border-bottom py-3">
                    <span class="text-muted">Numéro de téléphone :</span>
                    <span class="font-weight-bold"><?= esc($phone) ?></span>
                </div>
                <div class="d-flex justify-content-between border-bottom py-3">
                    <span class="text-muted">Nom du Client :</span>
                    <span class="font-weight-bold"><?= esc(session()->get('name')) ?></span>
                </div>
                <div class="d-flex justify-content-between py-3">
                    <span class="text-muted">Opérateur de réseau :</span>
                    <span class="badge bg-orange px-3 py-2"><?= esc($operator_prefix) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshBalance() {
    $.ajax({
        url: '<?= base_url('api/client/balance') ?>',
        method: 'GET',
        success: function(data) {
            $('.number').text(new Intl.NumberFormat('fr-MG').format(data.balance) + ' Ar');
            notify.show('Solde actualisé avec succès', 'success');
        },
        error: function() {
            notify.show('Erreur lors de l\'actualisation du solde', 'error');
        }
    });
}
</script>
<?= $this->endSection() ?>
