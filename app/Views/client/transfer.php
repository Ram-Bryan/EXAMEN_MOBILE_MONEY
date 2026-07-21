<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-exchange-alt text-green"></i> Effectuer un Transfert</h2>
    <p class="text-muted">Envoyez des fonds instantanément à un ou plusieurs numéros.</p>
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
                    <input type="hidden" name="transfer_mode" id="transferMode" value="single">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mode de transfert</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transfer_mode_radio" id="modeSingle" value="single" checked onchange="switchMode('single')">
                                <label class="form-check-label" for="modeSingle">
                                    <i class="fas fa-user me-1"></i> Transfert simple
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transfer_mode_radio" id="modeMultiple" value="multiple" onchange="switchMode('multiple')">
                                <label class="form-check-label" for="modeMultiple">
                                    <i class="fas fa-users me-1"></i> Envoi multiple
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="singleRecipientBlock">
                        <div class="mb-3">
                            <label for="recipient_phone" class="form-label">Numéro de téléphone du destinataire</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-friends"></i></span>
                                <input type="tel" class="form-control" id="recipient_phone" name="c" placeholder="Ex: 0349876543" oninput="previewTransferFee()">
                            </div>
                            <small class="text-muted mt-2 d-block">
                                Numéros acceptés : 031, 032, 033, 034, 037, 038.
                            </small>
                        </div>
                    </div>

                    <div id="multiRecipientBlock" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Numéros des destinataires</label>
                            <div id="recipientList">
                                <div class="input-group mb-2 recipient-row">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="tel" class="form-control recipient-input" placeholder="Ex: 0349876543" oninput="previewTransferFee()">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRecipient(this)" title="Supprimer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="addRecipient()">
                                <i class="fas fa-plus me-1"></i> Ajouter un numéro
                            </button>
                            <small class="text-muted mt-2 d-block">
                                Le montant total sera divisé automatiquement entre les destinataires.
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Montant <?= '<span id="amountLabel">à transférer</span>' ?> (Ar)</label>
                        <div class="input-group">
                            <span class="input-group-text" style="font-weight: bold;">Ar</span>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Entrez le montant" required min="1" oninput="previewTransferFee()">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="include_fees" class="form-label">Options de transfert</label>
                        <select class="form-select" id="include_fees" name="include_fees" onchange="previewTransferFee()">
                            <option value="1">1. Transférer le montant + frais + commission (frais en plus)</option>
                            <option value="2">2. Transférer le montant (frais inclus dans le montant)</option>
                        </select>
                        <small class="text-muted mt-1 d-block">
                            Option 1 : le total débité = montant + frais + commission.<br>
                            Option 2 : le total débité = montant (les frais et commission sont déduits du montant reçu).
                        </small>
                    </div>

                    <div id="feePreviewBox" class="p-3 mb-4 border bg-light rounded-3 d-none animate-fade-in" style="border-color: var(--primary-border) !important;">
                        <h6 class="fw-bold mb-2" style="color: var(--text-dark);"><i class="fas fa-receipt me-1"></i> Aperçu du transfert</h6>

                        <div id="singlePreview">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Montant à transférer :</span>
                                <span id="amountShow" class="fw-bold">0 Ar</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Frais de transfert :</span>
                                <span id="feeShow" class="fw-bold text-danger">0 Ar</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 d-none" id="commissionRow">
                                <span class="text-muted">Commission inter-opérateur :</span>
                                <span id="commissionShow" class="fw-bold text-warning">0 Ar</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total débité de votre compte :</span>
                                <span id="totalShow" class="text-danger">0 Ar</span>
                            </div>
                        </div>

                        <div id="multiPreview" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm mb-2">
                                    <thead>
                                        <tr><th>Destinataire</th><th class="text-end">Montant</th><th class="text-end">Frais</th><th class="text-end">Commission</th><th class="text-end">Débit</th></tr>
                                    </thead>
                                    <tbody id="multiPreviewBody"></tbody>
                                </table>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total débité :</span>
                                <span id="multiTotalShow" class="text-danger">0 Ar</span>
                            </div>
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
                <div class="mt-3 p-2 rounded" style="background: #fff7ed; border: 1px solid #fed7aa;">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1 text-warning"></i>
                        <strong>Commission inter-opérateur :</strong> 1.5% du montant pour les transferts vers un opérateur externe.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const validPrefixes = ['031', '032', '033', '034', '037', '038'];
const senderPhone = '<?= esc($phone) ?>';
let currentMode = 'single';

function switchMode(mode) {
    currentMode = mode;
    document.getElementById('transferMode').value = mode;
    if (mode === 'single') {
        document.getElementById('singleRecipientBlock').classList.remove('d-none');
        document.getElementById('multiRecipientBlock').classList.add('d-none');
        document.getElementById('amountLabel').textContent = 'à transférer';
        document.getElementById('recipient_phone').setAttribute('required', 'required');
    } else {
        document.getElementById('singleRecipientBlock').classList.add('d-none');
        document.getElementById('multiRecipientBlock').classList.remove('d-none');
        document.getElementById('amountLabel').textContent = 'total à répartir';
        document.getElementById('recipient_phone').removeAttribute('required');
    }
    previewTransferFee();
}

function addRecipient() {
    const list = document.getElementById('recipientList');
    const row = document.createElement('div');
    row.className = 'input-group mb-2 recipient-row';
    row.innerHTML = `
        <span class="input-group-text"><i class="fas fa-user"></i></span>
        <input type="tel" class="form-control recipient-input" placeholder="Ex: 0349876543" oninput="previewTransferFee()">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRecipient(this)" title="Supprimer">
            <i class="fas fa-times"></i>
        </button>
    `;
    list.appendChild(row);
    previewTransferFee();
}

function removeRecipient(btn) {
    const rows = document.querySelectorAll('.recipient-row');
    if (rows.length > 1) {
        btn.closest('.recipient-row').remove();
        previewTransferFee();
    }
}

function getRecipientPhones() {
    if (currentMode === 'single') {
        const phone = ($('#recipient_phone').val() || '').trim();
        return phone ? [phone] : [];
    }
    const phones = [];
    document.querySelectorAll('.recipient-input').forEach(el => {
        const v = el.value.trim();
        if (v) phones.push(v);
    });
    return phones;
}

function validatePhone(phone) {
    phone = phone.trim();
    if (phone.length !== 10) return false;
    if (!/^\d{10}$/.test(phone)) return false;
    return validPrefixes.includes(phone.substring(0, 3));
}

function formatAr(n) {
    return new Intl.NumberFormat('fr-MG').format(n) + ' Ar';
}

let pendingRequests = 0;
let accumulatedResults = [];

function previewTransferFee() {
    const totalAmount = parseFloat($('#amount').val());
    const includeFees = $('#include_fees').val();
    const balance = parseFloat($('#clientBalance').data('balance'));
    const phones = getRecipientPhones();

    if (isNaN(totalAmount) || totalAmount <= 0 || phones.length === 0) {
        $('#feePreviewBox').addClass('d-none');
        return;
    }

    $('#feePreviewBox').removeClass('d-none');
    const count = phones.length;
    const amountPer = (currentMode === 'multiple' && count > 1) ? Math.floor(totalAmount / count) : totalAmount;

    // include_fees == '1': frais en plus (frais_inclus=0) → total = amount + fee + commission
    // include_fees == '2': frais inclus (frais_inclus=1) → total = amount
    if (includeFees === '2') {
        if (currentMode === 'single' || count <= 1) {
            $('#singlePreview').removeClass('d-none');
            $('#multiPreview').addClass('d-none');
            $('#amountShow').text(formatAr(amountPer));
            $('#feeShow').text('0 Ar (inclus)');
            $('#commissionRow').addClass('d-none');
            $('#totalShow').text(formatAr(amountPer));
        } else {
            $('#singlePreview').addClass('d-none');
            $('#multiPreview').removeClass('d-none');
            let tbody = '';
            let totalDebit = 0;
            for (let i = 0; i < count; i++) {
                tbody += `<tr>
                    <td><strong>${phones[i]}</strong></td>
                    <td class="text-end">${formatAr(amountPer)}</td>
                    <td class="text-end text-muted">0 Ar</td>
                    <td class="text-end text-muted">0 Ar</td>
                    <td class="text-end fw-bold">${formatAr(amountPer)}</td>
                </tr>`;
                totalDebit += amountPer;
            }
            $('#multiPreviewBody').html(tbody);
            $('#multiTotalShow').text(formatAr(totalDebit));
        }
        if (totalAmount > balance) {
            $('#balanceAlert').removeClass('d-none');
        } else {
            $('#balanceAlert').addClass('d-none');
        }
        return;
    }

    pendingRequests = 0;
    accumulatedResults = [];
    const targetPreview = (currentMode === 'multiple' && count > 1) ? 'multi' : 'single';

    phones.forEach(function(phone, index) {
        pendingRequests++;
        $.ajax({
            url: '<?= base_url('api/fees/calculate') ?>',
            method: 'GET',
            data: {
                type_code: 'TRANSFERT',
                amount: amountPer,
                recipient_phone: phone
            },
            success: function(response) {
                if (response.success) {
                    accumulatedResults[index] = {
                        phone: phone,
                        fee: response.fee ? parseFloat(response.fee) : 0,
                        commission: response.commission ? parseFloat(response.commission) : 0,
                        is_inter: response.is_inter_operator || false
                    };
                } else {
                    accumulatedResults[index] = { phone: phone, fee: 0, commission: 0, is_inter: false };
                }
                pendingRequests--;
                if (pendingRequests === 0) renderPreview(targetPreview, amountPer, balance, includeFees);
            },
            error: function() {
                accumulatedResults[index] = { phone: phone, fee: 0, commission: 0, is_inter: false };
                pendingRequests--;
                if (pendingRequests === 0) renderPreview(targetPreview, amountPer, balance, includeFees);
            }
        });
    });
}

function renderPreview(mode, amountPer, balance, includeFees) {
    // includeFees == '1': frais en plus (frais_inclus=0) → total = amount + fee + commission
    // includeFees == '2': frais inclus (frais_inclus=1) → total = amount
    if (includeFees === '2') {
        if (mode === 'single') {
            $('#singlePreview').removeClass('d-none');
            $('#multiPreview').addClass('d-none');
            $('#amountShow').text(formatAr(amountPer));
            $('#feeShow').text('0 Ar (inclus)');
            $('#commissionRow').addClass('d-none');
            $('#totalShow').text(formatAr(amountPer));
        } else {
            $('#singlePreview').addClass('d-none');
            $('#multiPreview').removeClass('d-none');
            let tbody = '';
            let totalDebit = 0;
            for (let i = 0; i < accumulatedResults.length; i++) {
                const r = accumulatedResults[i];
                tbody += `<tr>
                    <td><strong>${r.phone}</strong></td>
                    <td class="text-end">${formatAr(amountPer)}</td>
                    <td class="text-end text-muted">0 Ar</td>
                    <td class="text-end text-muted">0 Ar</td>
                    <td class="text-end fw-bold">${formatAr(amountPer)}</td>
                </tr>`;
                totalDebit += amountPer;
            }
            $('#multiPreviewBody').html(tbody);
            $('#multiTotalShow').text(formatAr(totalDebit));
        }
        if (amountPer > balance) { // For single transfer, check amountPer; for multi, totalAmount was checked in previewFee
            $('#balanceAlert').removeClass('d-none');
        } else {
            $('#balanceAlert').addClass('d-none');
        }
        return;
    }

    if (mode === 'single') {
        const r = accumulatedResults[0] || { fee: 0, commission: 0 };
        const total = amountPer + r.fee + r.commission;
        $('#singlePreview').removeClass('d-none');
        $('#multiPreview').addClass('d-none');
        $('#amountShow').text(formatAr(amountPer));
        $('#feeShow').text(r.fee > 0 ? formatAr(r.fee) : '0 Ar');
        if (r.commission > 0) {
            $('#commissionRow').removeClass('d-none');
            $('#commissionShow').text(formatAr(r.commission));
        } else {
            $('#commissionRow').addClass('d-none');
        }
        $('#totalShow').text(formatAr(total));
        if (total > balance) {
            $('#balanceAlert').removeClass('d-none');
        } else {
            $('#balanceAlert').addClass('d-none');
        }
    } else {
        $('#singlePreview').addClass('d-none');
        $('#multiPreview').removeClass('d-none');
        let tbody = '';
        let totalDebit = 0;
        let totalFees = 0;
        let totalComm = 0;
        for (let i = 0; i < accumulatedResults.length; i++) {
            const r = accumulatedResults[i];
            const debit = amountPer + r.fee + r.commission;
            totalDebit += debit;
            totalFees += r.fee;
            totalComm += r.commission;
            tbody += `<tr>
                <td><strong>${r.phone}</strong></td>
                <td class="text-end">${formatAr(amountPer)}</td>
                <td class="text-end ${r.fee > 0 ? 'text-danger fw-bold' : 'text-muted'}">${r.fee > 0 ? formatAr(r.fee) : '0 Ar'}</td>
                <td class="text-end ${r.commission > 0 ? 'text-warning fw-bold' : 'text-muted'}">${r.commission > 0 ? formatAr(r.commission) : '0 Ar'}</td>
                <td class="text-end fw-bold">${formatAr(debit)}</td>
            </tr>`;
        }
        if (totalFees > 0 || totalComm > 0) {
            tbody += `<tr style="background: var(--primary-bg);">
                <td colspan="2"><strong>Total frais + commission</strong></td>
                <td class="text-end fw-bold text-danger">${totalFees > 0 ? formatAr(totalFees) : '-'}</td>
                <td class="text-end fw-bold text-warning">${totalComm > 0 ? formatAr(totalComm) : '-'}</td>
                <td></td>
            </tr>`;
        }
        $('#multiPreviewBody').html(tbody);
        $('#multiTotalShow').text(formatAr(totalDebit));
        if (totalDebit > balance) {
            $('#balanceAlert').removeClass('d-none');
        } else {
            $('#balanceAlert').addClass('d-none');
        }
    }
}

$('#transferForm').on('submit', function(e) {
    e.preventDefault();
    const phones = getRecipientPhones();
    const mode = currentMode;

    if (phones.length === 0) {
        alert('Veuillez saisir au moins un numéro de destinataire.');
        return false;
    }

    for (let i = 0; i < phones.length; i++) {
        const p = phones[i];
        if (p === senderPhone) {
            alert('Vous ne pouvez pas envoyer à votre propre numéro (' + p + ').');
            return false;
        }
        if (!validatePhone(p)) {
            alert('Numéro invalide : ' + p + '. Utilisez un numéro à 10 chiffres (031, 032, 033, 034, 037, 038).');
            return false;
        }
        for (let j = i + 1; j < phones.length; j++) {
            if (phones[j] === p) {
                alert('Le numéro ' + p + ' est en double.');
                return false;
            }
        }
    }

    if (mode === 'single') {
        document.getElementById('recipient_phone').value = phones[0];
    } else {
        document.querySelectorAll('input[name="recipients[]"]').forEach(el => el.remove());
        phones.forEach(function(p) {
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'recipients[]';
            h.value = p;
            document.getElementById('transferForm').appendChild(h);
        });
    }

    this.submit();
});
</script>
<?= $this->endSection() ?>
