<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-plus-circle text-green"></i> Effectuer un Dépôt</h2>
    <p class="text-muted">Déposez de l'argent instantanément sur votre compte.</p>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--primary-gradient); border-radius: 16px 16px 0 0 !important;">
                <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2"></i> Formulaire de Dépôt</h5>
            </div>
            <div class="card-body p-4">
                <form id="depositForm" action="<?= base_url('client/deposit') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="amount" class="form-label">Montant à déposer (Ar)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-weight: bold;">Ar</span>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Entrez le montant" required min="1" oninput="previewFee(this.value)">
                        </div>
                        <small class="text-muted mt-2 d-block">
                            Saisissez le montant brut du dépôt. Le calcul des frais s'effectue dynamiquement.
                        </small>
                    </div>

                    <!-- Fee Preview -->
                    <div id="feePreviewBox" class="p-3 mb-4 border bg-light rounded-3 d-none animate-fade-in" style="border-color: var(--primary-border) !important;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Montant saisi :</span>
                            <span id="amountShow" class="fw-bold">0 Ar</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Frais applicables :</span>
                            <span id="feeShow" class="fw-bold text-danger">0 Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total à imputer :</span>
                            <span id="totalShow" class="text-green">0 Ar</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green btn-lg w-100">
                        <i class="fas fa-check-circle me-1"></i> Confirmer le Dépôt
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">Informations & Tarifs</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert" style="background-color: var(--primary-bg); color: var(--primary-dark); border-radius: 12px;">
                    <i class="fas fa-info-circle me-2"></i>
                    Les dépôts sont immédiatement crédités sur votre compte et sont soumis au barème des frais de votre opérateur.
                </div>

                <h6 class="fw-bold mt-4 mb-3"><i class="fas fa-percent text-green me-1"></i> Frais de Dépôt Actuels</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr style="background: var(--primary-bg);">
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
                                        <td class="text-end fw-bold text-danger">
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
        method: 'GET',
        data: {
            type_code: 'DEPOT',
            amount: amount
        },
        success: function(response) {
            if (response.success) {
                $('#feePreviewBox').removeClass('d-none');
                $('#amountShow').text(new Intl.NumberFormat('fr-MG').format(amount) + ' Ar');
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
</script>
<?= $this->endSection() ?>
