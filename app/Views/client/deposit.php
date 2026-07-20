<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="dashboard-header mb-4">
    <h1 class="h2"><i class="fas fa-plus-circle text-orange"></i> Effectuer un Dépôt</h1>
    <p class="text-muted">Déposez de l'argent instantanément sur votre compte.</p>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-orange text-white py-3 border-0" style="border-radius: 16px 16px 0 0 !important;">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i> Formulaire de Dépôt</h5>
            </div>
            <div class="card-body p-4">
                <form id="depositForm" action="<?= base_url('client/deposit') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label for="amount" class="form-label font-weight-bold">Montant à déposer (Ar)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-2 border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e0e0e0; font-weight: bold; color: #666;">Ar</span>
                            <input type="number" class="form-control required border-2 border-start-0" id="amount" name="amount" placeholder="Entrez le montant" required min="1" style="border-radius: 0 12px 12px 0;" oninput="previewFee(this.value)">
                        </div>
                        <small class="text-muted mt-2 d-block">
                            Saisissez le montant brut du dépôt. Le calcul des frais s'effectue dynamiquement.
                        </small>
                    </div>

                    <!-- Fee Preview (Dynamic via AJAX) -->
                    <div id="feePreviewBox" class="p-3 mb-4 border border-warning bg-light rounded-3 d-none animate-fade-in">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Montant saisi :</span>
                            <span id="amountShow" class="font-weight-bold">0 Ar</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Frais applicables :</span>
                            <span id="feeShow" class="font-weight-bold text-danger">0 Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>Total à imputer :</span>
                            <span id="totalShow" class="text-orange">0 Ar</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-orange btn-lg w-100">
                        <i class="fas fa-check-circle me-1"></i> Confirmer le Dépôt
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="font-weight-bold mb-0">Informations & Tarifs</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-0" style="background-color: rgba(255, 107, 0, 0.05); color: var(--orange-dark); border-radius: 12px;">
                    <i class="fas fa-info-circle me-2"></i>
                    Les dépôts sont immédiatement crédités sur votre compte et sont soumis au barème des frais de votre opérateur.
                </div>
                
                <h6 class="font-weight-bold mt-4 mb-3"><i class="fas fa-percent text-orange me-1"></i> Frais de Dépôt Actuels</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th>Tranche</th>
                                <th class="text-end">Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($fees)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Aucun barème défini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($fees as $fee): ?>
                                    <tr>
                                        <td>
                                            <?= number_format($fee->montant_min, 0, ',', ' ') ?> Ar - 
                                            <?= $fee->montant_max ? number_format($fee->montant_max, 0, ',', ' ') . ' Ar' : 'Illimité' ?>
                                        </td>
                                        <td class="text-end font-weight-bold text-danger">
                                            <?= number_format($fee->frais_fixe, 0, ',', ' ') ?> Ar
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewFee(amount) {
    amount = parseFloat(amount);
    if (isNaN(amount) || amount <= 0) {
        $('#feePreviewBox').addClass('d-none');
        return;
    }

    $.ajax({
        url: '<?= base_url('api/fees/calculate') ?>',
        method: 'POST',
        data: {
            type_code: 'DEPOT',
            amount: amount
        },
        success: function(response) {
            if (response.success) {
                $('#feePreviewBox').removeClass('d-none');
                $('#amountShow').text(new Intl.NumberFormat('fr-MG').format(amount) + ' Ar');
                $('#feeShow').text(new Intl.NumberFormat('fr-MG').format(response.fee) + ' Ar');
                
                // For deposit, check if fee is applied
                // In Mobile Money, does deposit subtract fee or add fee?
                // Let's assume deposit adds the amount to the user's account, and the fee is applied.
                // Wait! "Dépôt : destinataire_id = client_id, expediteur_id = NULL".
                // Since expediteur_id is NULL, the client is NOT the sender. The client is the recipient.
                // The recipient does NOT pay the fee! In Mobile Money, the sender pays the fee. 
                // Since expediteur_id is NULL (external deposit / cash-in), there is no expediteur client to pay the fee!
                // So the client receives the exact amount (montant_brut), and the operator records the fee as gains (paid by operator or from cash-in).
                // Or if there is a fee, the receiver gets montant_brut - fee?
                // Let's see: in v_transactions_frais, the fee is applied.
                // In balance calculation:
                // `WHEN tf.destinataire_id = c.id THEN tf.montant_brut`
                // So when destinataire_id = c.id, the client's balance increases by `montant_brut` (frais is NOT subtracted!).
                // `WHEN tf.expediteur_id = c.id THEN -(tf.montant_brut + COALESCE(tf.frais_applique, 0))`
                // So when expediteur_id = c.id, the client's balance decreases by `montant_brut + frais_applique`.
                // This means the recipient receives the FULL `montant_brut`, and the sender pays the fee!
                // Since expediteur_id is NULL, the sender is external, so the client receives exactly `montant_brut`.
                // Let's reflect this in the deposit form: fee = 0 Ar for the recipient client!
                // But wait! If the operator applies a fee, who pays it?
                // Since expediteur_id is NULL, it doesn't affect any client's balance! It only generates gains for the operator.
                // Therefore, for the client, the deposit is indeed free (net credit = amount).
                // Let's display this clearly to the user.
                
                $('#feeShow').text('0 Ar (Pris en charge)');
                $('#totalShow').text(new Intl.NumberFormat('fr-MG').format(amount) + ' Ar');
            } else {
                $('#feePreviewBox').addClass('d-none');
            }
        },
        error: function() {
            $('#feePreviewBox').addClass('d-none');
        }
    });
}

$('#depositForm').on('submit', function(e) {
    e.preventDefault();
    submitForm('depositForm', function(response) {
        if (response.success) {
            setTimeout(function() {
                window.location.href = '<?= base_url('client/dashboard') ?>';
            }, 1000);
        }
    });
});
</script>
<?= $this->endSection() ?>
