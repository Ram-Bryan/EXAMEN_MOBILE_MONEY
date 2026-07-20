<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-exchange-alt text-green"></i> Effectuer un Transfert</h2>
    <p class="text-muted">Envoyez des fonds instantanément à un autre numéro.</p>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header text-white py-3 border-0" style="background: var(--primary-gradient); border-radius: 16px 16px 0 0 !important;">
                <h5 class="mb-0 fw-bold"><i class="fas fa-exchange-alt me-2"></i> Formulaire de Transfert</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <span class="text-muted">Solde disponible :</span>
                    <strong class="text-green" id="clientBalance" data-balance="<?= $balance ?>"><?= number_format($balance, 0, ',', ' ') ?> Ar</strong>
                </div>

                <form id="transferForm" action="<?= base_url('client/transfer') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="recipient_phone" class="form-label">Numéro de téléphone du destinataire</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                            <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone" placeholder="Ex: 0349876543" required>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            Le numéro doit être un client Mobile Money existant (033, 034, 037, 038).
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label">Montant à transférer (Ar)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-weight: bold;">Ar</span>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Entrez le montant" required min="1" oninput="previewTransferFee(this.value)">
                        </div>
                    </div>

                    <!-- Transfer Fee Preview -->
                    <div id="feePreviewBox" class="p-3 mb-4 border bg-light rounded-3 d-none animate-fade-in" style="border-color: var(--primary-border) !important;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Montant à transférer :</span>
                            <span id="amountShow" class="fw-bold">0 Ar</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Frais de transfert :</span>
                            <span id="feeShow" class="fw-bold text-danger">0 Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total débité de votre compte :</span>
                            <span id="totalShow" class="text-danger">0 Ar</span>
                        </div>
                        <div id="balanceAlert" class="mt-3 alert alert-danger p-2 mb-0 d-none" style="font-size: 13px;">
                            <i class="fas fa-exclamation-triangle me-1"></i> Solde insuffisant (le total dépasse votre solde).
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green btn-lg w-100" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i> Confirmer le Transfert
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">Tarifs & Frais de Transfert</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr style="background: var(--primary-bg);">
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
const validPrefixes = ['033', '034', '037', '038'];

function validatePhone(phone) {
    phone = phone.trim();
    if (phone.length !== 10) return false;
    if (!/^\d{10}$/.test(phone)) return false;
    return validPrefixes.includes(phone.substring(0, 3));
}

function previewTransferFee(amount) {
    amount = parseFloat(amount);
    const balance = parseFloat($('#clientBalance').data('balance'));

    if (isNaN(amount) || amount <= 0) {
        $('#feePreviewBox').addClass('d-none');
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
                    $('#feeShow').text('Aucun barème');
                    $('#totalShow').text('N/A');
                    $('#balanceAlert').addClass('d-none');
                } else {
                    const fee = parseFloat(response.fee);
                    const total = amount + fee;

                    $('#feeShow').text(new Intl.NumberFormat('fr-MG').format(fee) + ' Ar');
                    $('#totalShow').text(new Intl.NumberFormat('fr-MG').format(total) + ' Ar');

                    if (total > balance) {
                        $('#balanceAlert').removeClass('d-none');
                    } else {
                        $('#balanceAlert').addClass('d-none');
                    }
                }
            } else {
                $('#feePreviewBox').addClass('d-none');
            }
        },
        error: function() {
            $('#feePreviewBox').addClass('d-none');
        }
    });
}

$('#transferForm').on('submit', function(e) {
    const recipient = $('#recipient_phone').val().trim();
    const senderPhone = '<?= esc($phone) ?>';

    if (recipient === senderPhone) {
        e.preventDefault();
        alert('Vous ne pouvez pas effectuer un transfert vers votre propre numéro');
        return false;
    }

    if (!validatePhone(recipient)) {
        e.preventDefault();
        alert('Numéro de téléphone invalide. Utilisez un numéro à 10 chiffres commençant par 033, 034, 037 ou 038.');
        return false;
    }
});
</script>
<?= $this->endSection() ?>
