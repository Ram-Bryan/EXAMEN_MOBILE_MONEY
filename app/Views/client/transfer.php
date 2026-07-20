<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="dashboard-header mb-4">
    <h1 class="h2"><i class="fas fa-exchange-alt text-orange"></i> Effectuer un Transfert</h1>
    <p class="text-muted">Envoyez des fonds instantanément à un autre numéro.</p>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-orange text-white py-3 border-0" style="border-radius: 16px 16px 0 0 !important;">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-exchange-alt me-2"></i> Formulaire de Transfert</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <span class="text-muted">Solde disponible :</span>
                    <strong class="text-orange" id="clientBalance" data-balance="<?= $balance ?>"><?= number_format($balance, 0, ',', ' ') ?> Ar</strong>
                </div>

                <form id="transferForm" action="<?= base_url('client/transfer') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="recipient_phone" class="form-label font-weight-bold">Numéro de téléphone du destinataire</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-2 border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e0e0e0; font-weight: bold; color: #666;"><i class="fas fa-user-friends"></i></span>
                            <input type="tel" class="form-control required border-2 border-start-0" id="recipient_phone" name="recipient_phone" placeholder="Ex: 0349876543" required style="border-radius: 0 12px 12px 0;">
                        </div>
                        <small class="text-muted mt-2 d-block">
                            Le destinataire sera automatiquement inscrit s'il n'existe pas encore (avec un préfixe valide).
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label font-weight-bold">Montant à transférer (Ar)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-2 border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e0e0e0; font-weight: bold; color: #666;">Ar</span>
                            <input type="number" class="form-control required border-2 border-start-0" id="amount" name="amount" placeholder="Entrez le montant" required min="1" style="border-radius: 0 12px 12px 0;" oninput="previewTransferFee(this.value)">
                        </div>
                    </div>

                    <!-- Transfer Fee Preview -->
                    <div id="feePreviewBox" class="p-3 mb-4 border border-warning bg-light rounded-3 d-none animate-fade-in">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Montant à transférer :</span>
                            <span id="amountShow" class="font-weight-bold">0 Ar</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Frais de transfert :</span>
                            <span id="feeShow" class="font-weight-bold text-danger">0 Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>Total débité de votre compte :</span>
                            <span id="totalShow" class="text-danger">0 Ar</span>
                        </div>
                        <div id="balanceAlert" class="mt-3 alert alert-danger p-2 mb-0 d-none" style="font-size: 13px;">
                            <i class="fas fa-exclamation-triangle me-1"></i> Solde insuffisant (le total dépasse votre solde).
                        </div>
                    </div>

                    <button type="submit" class="btn btn-orange btn-lg w-100" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i> Confirmer le Transfert
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="font-weight-bold mb-0">Tarifs & Frais de Transfert</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th>Tranche de Montant</th>
                                <th class="text-end">Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($fees)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Aucun barème de transfert défini.</td>
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
function previewTransferFee(amount) {
    amount = parseFloat(amount);
    const balance = parseFloat($('#clientBalance').data('balance'));
    
    if (isNaN(amount) || amount <= 0) {
        $('#feePreviewBox').addClass('d-none');
        $('#submitBtn').prop('disabled', false);
        return;
    }

    $.ajax({
        url: '<?= base_url('api/fees/calculate') ?>',
        method: 'GET',
        data: {
            type_code: 'TRANSFERT',
            amount: amount
        },
        success: function(response) {
            if (response.success) {
                $('#feePreviewBox').removeClass('d-none');
                $('#amountShow').text(new Intl.NumberFormat('fr-MG').format(amount) + ' Ar');
                
                if (response.fee === null) {
                    $('#feeShow').text('Non supporté / Pas de barème');
                    $('#totalShow').text('N/A');
                    $('#balanceAlert').addClass('d-none');
                    $('#submitBtn').prop('disabled', true);
                } else {
                    const fee = parseFloat(response.fee);
                    const total = amount + fee;
                    
                    $('#feeShow').text(new Intl.NumberFormat('fr-MG').format(fee) + ' Ar');
                    $('#totalShow').text(new Intl.NumberFormat('fr-MG').format(total) + ' Ar');
                    
                    if (total > balance) {
                        $('#balanceAlert').removeClass('d-none');
                        $('#submitBtn').prop('disabled', true);
                    } else {
                        $('#balanceAlert').addClass('d-none');
                        $('#submitBtn').prop('disabled', false);
                    }
                }
            } else {
                $('#feePreviewBox').addClass('d-none');
                $('#submitBtn').prop('disabled', false);
            }
        },
        error: function() {
            $('#feePreviewBox').addClass('d-none');
            $('#submitBtn').prop('disabled', false);
        }
    });
}

$('#transferForm').on('submit', function(e) {
    const recipient = $('#recipient_phone').val().trim();
    if (recipient === '<?= esc($phone) ?>') {
        e.preventDefault();
        alert('Vous ne pouvez pas effectuer un transfert vers votre propre numéro');
        return false;
    }
});
</script>
<?= $this->endSection() ?>
