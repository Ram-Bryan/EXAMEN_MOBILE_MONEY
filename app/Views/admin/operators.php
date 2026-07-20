<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-cog"></i> Gestion des Opérateurs</h1>
    <button class="btn btn-orange" onclick="openModal('operatorModal', 'Ajouter un opérateur', getOperatorForm())">
        <i class="fas fa-plus"></i> Ajouter un opérateur
    </button>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Rechercher..." id="searchOperator">
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filterStatus">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Operators Table -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover" id="operatorsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Préfixes</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($operators) && !empty($operators)): ?>
                    <?php foreach($operators as $operator): ?>
                    <tr>
                        <td>#OP<?= str_pad($operator['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><strong><?= $operator['name']; ?></strong></td>
                        <td><?= $operator['email']; ?></td>
                        <td><?= $operator['phone']; ?></td>
                        <td><span class="badge bg-orange"><?= $operator['prefixes']; ?></span></td>
                        <td><span class="badge <?= $operator['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                            <?= ucfirst($operator['status']); ?>
                        </span></td>
                        <td><?= date('d/m/Y', strtotime($operator['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editOperator(<?= $operator['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteOperator(<?= $operator['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucun opérateur trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Operator Modal -->
<div class="modal fade" id="operatorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-orange text-white">
                <h5 class="modal-title">Ajouter un opérateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="operatorModalBody">
                <!-- Form loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= base_url(); ?>';

function getOperatorForm(data = null) {
    return `
        <form id="operatorForm">
            <input type="hidden" name="id" value="${data?.id || ''}">
            <div class="mb-3">
                <label class="form-label">Nom de l'opérateur</label>
                <input type="text" class="form-control required" name="name" value="${data?.name || ''}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control required" name="email" value="${data?.email || ''}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" class="form-control required" name="phone" value="${data?.phone || ''}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Préfixes (séparés par des virgules)</label>
                <input type="text" class="form-control required" name="prefixes" value="${data?.prefixes || ''}" placeholder="034,038" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Statut</label>
                <select class="form-control required" name="status">
                    <option value="active" ${data?.status === 'active' ? 'selected' : ''}>Actif</option>
                    <option value="inactive" ${data?.status === 'inactive' ? 'selected' : ''}>Inactif</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-orange" onclick="saveOperator()">Enregistrer</button>
            </div>
        </form>
    `;
}

function saveOperator() {
    const formData = $('#operatorForm').serializeArray();
    const data = {};
    formData.forEach(item => data[item.name] = item.value);
    
    if (data.id) {
        // Update
        $.ajax({
            url: baseUrl + 'admin/operators/update/' + data.id,
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    notify.show('Opérateur mis à jour avec succès', 'success');
                    location.reload();
                } else {
                    notify.show(response.message, 'error');
                }
            }
        });
    } else {
        // Create
        $.ajax({
            url: baseUrl + 'admin/operators/create',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    notify.show('Opérateur créé avec succès', 'success');
                    location.reload();
                } else {
                    notify.show(response.message, 'error');
                }
           