<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-wallet text-green"></i> Mon Epargne</h2>
    <p class="text-muted">Consultez et suivez l'état de votre Epargne en temps réel.</p>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm text-center p-5" style="border-radius: 16px;">
            <i class="fas fa-wallet mb-3 text-green" style="font-size: 60px;"></i>
            <h3 class="text-muted text-uppercase mb-2" style="font-size: 14px; font-weight: 600; letter-spacing: 1px;">Epargne Actuel</h3>
            <div class="mb-4 text-green" style="font-size: 48px; font-weight: 800;">
                <?= number_format($epargne['montant'], 0, ',', ' ') ?> Ar
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
