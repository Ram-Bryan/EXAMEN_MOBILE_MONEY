<?php
$page_title = 'Configuration des Frais';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-coins"></i> Configuration des Frais</h1>
    <button class="btn btn-orange" onclick="openModal('feeModal', 'Ajouter une tranche', getFeeForm())">
        <i class="fas fa-plus"></i> Ajouter une tranche
    </button>
</div>

<!-- Fee Configuration Table -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover" id="feesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Opération</th>
                    <th>Montant min</th>
                    <th>Montant max</th>
                    <th>Frais</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Sample fee data
                $fees = [
                    ['id' => 1, 'operation' => 'Dépôt', 'min' => 100, 'max' => 1000, 'fee' => 50],
                    ['id' => 2, 'operation' => 'Dépôt', 'min' => 1000, 'max' => 5000, 'fee' => 50],
                    ['id' => 3, 'operation' => 'Dépôt', 'min' => 5000, 'max' => 10000, 'fee' => 100],
                    ['id' => 4, 'operation' => 'Retrait', 'min' => 100, 'max' => 1000, 'fee' => 50],
                    ['id' => 5, 'operation' => 'Retrait', 'min' => 1000, 'max' => 5000, 'fee' => 50],
                    ['id' => 6, 'operation' => 'Retrait', 'min' => 5000, 'max' => 10000, 'fee' => 100],
                    ['id' => 7, 'operation' => 'Transfert', 'min' => 100, 'max' => 1000, 'fee' => 50],
                    ['id' => 8, 'operation' => 'Transfert', 'min' => 1000, 'max' => 5000, 'fee' => 50],
                ];
                foreach($fees as $fee): ?>
                <tr>
                    <td>#<?php echo $fee['id']; ?></td>
                    <td><span class="badge bg-info"><?php echo $fee['operation']; ?></span></td>
                    <td><?php echo number_format($fee['min'], 0, ',', ' '); ?> Ar</td>
                    <td><?php echo number_format($fee['max'], 0, ',', ' '); ?> Ar</td>
                    <td><strong><?php echo number_format($fee['fee'], 0, ',', ' '); ?> Ar</strong></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editFee(<?php echo $fee['id']; ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteFee(<?php echo $fee['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Fee Modal -->
<div class="modal fade" id="feeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange text-white">
                <h5 class="modal-title">Configurer les frais</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="feeForm">
                    <div class="mb-3">
                        <label class="form-label">Type d'opération</label>
                        <select class="form-control required" name="operation_type">
                            <option value="deposit">Dépôt</option>
                            <option value="withdraw">Retrait</option>
                            <option value="transfer">Transfert</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Montant minimum</label>
                                <input type="number" class="form-control required" name="min_amount" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Montant maximum</label>
                                <input type="number" class="form-control required" name="max_amount" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frais</label>
                        <input type="number" class="form-control required" name="fee" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-orange" onclick="saveFee()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
const feeCRUD = new CRUDManager('/api/fees', 'feesTable');

function getFeeForm(data = null) {
    return `
        <form id="feeForm">
            <input type="hidden" name="id" value="${data?.id || ''}">
            <div class="mb-3">
                <label class="form-label">Type d'opération</label>
                <select class="form-control required" name="operation_type">
                    <option value="deposit" ${data?.operation_type === 'deposit' ? 'selected' : ''}>Dépôt</option>
                    <option value="withdraw" ${data?.operation_type === 'withdraw' ? 'selected' : ''}>Retrait</option>
                    <option value="transfer" ${data?.operation_type === 'transfer' ? 'selected' : ''}>Transfert</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Montant minimum</label>
                        <input type="number" class="form-control required" name="min_amount" value="${data?.min_amount || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Montant maximum</label>
                        <input type="number" class="form-control required" name="max_amount" value="${data?.max_amount || ''}" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Frais</label>
                <input type="number" class="form-control required" name="fee" value="${data?.fee || ''}" required>
            </div>
        </form>
    `;
}

function saveFee() {
    const formData = $('#feeForm').serializeArray();
    const data = {};
    formData.forEach(item => data[item.name] = item.value);
    
    if (data.id) {
        feeCRUD.update(data.id, data);
    } else {
        feeCRUD.create(data);
    }
    closeModal('feeModal');
}

function editFee(id) {
    feeCRUD.get(id, function(data) {
        openModal('feeModal', 'Modifier les frais', getFeeForm(data));
    });
}

function deleteFee(id) {
    feeCRUD.delete(id);
}
</script>

<?php
$content = ob_get_clean();
include '../includes/layouts/admin.php';
?>