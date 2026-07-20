<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Préfixes Opérateurs</h2>
        <p class="text-muted mb-0" style="font-size: 14px;">Configuration des préfixes de numéros autorisés</p>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter un préfixe
    </button>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-network-wired me-2"></i> Liste des préfixes</span>
        <span class="badge bg-green" style="font-size:13px;"><?= count($operators ?? []) ?> préfixe(s)</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Préfixe</th>
                    <th>Date d'ajout</th>
                    <th style="text-align: right; padding-right: 24px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($operators)): ?>
                    <?php foreach ($operators as $op): ?>
                    <tr>
                        <td><span class="text-muted" style="font-size:13px;">#<?= esc($op->id) ?></span></td>
                        <td>
                            <span class="badge bg-green" style="padding:6px 14px; font-size:15px; border-radius:20px;">
                                <?= esc($op->prefixe) ?>
                            </span>
                        </td>
                        <td style="color: var(--text-muted); font-size:14px;"><?= esc($op->created_at ?? '—') ?></td>
                        <td style="text-align: right; padding-right: 24px;">
                            <a href="<?= base_url('admin/operators/detail/' . $op->id) ?>" class="btn btn-sm btn-primary-custom">
                                <i class="fas fa-eye"></i> Détail
                            </a>
                            <button class="btn btn-sm btn-outline-secondary ms-1"
                                onclick="openEdit(<?= $op->id ?>, '<?= esc($op->prefixe) ?>')">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <button class="btn btn-sm btn-outline-danger ms-1"
                                onclick="confirmDelete(<?= $op->id ?>, '<?= esc($op->prefixe) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-3 d-block" style="color: var(--primary-border);"></i>Aucun préfixe configuré.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--text-dark);"><i class="fas fa-plus me-2 text-success"></i>Nouveau préfixe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/operators/create') ?>" method="POST">
                <div class="modal-body">
                    <label class="form-label">Préfixe (ex: 034)</label>
                    <input type="text" name="prefixe" class="form-control" placeholder="033" maxlength="10" required
                        pattern="[0-9]+" title="Chiffres uniquement">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modification -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--text-dark);"><i class="fas fa-edit me-2 text-warning"></i>Modifier le préfixe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                <div class="modal-body">
                    <label class="form-label">Préfixe</label>
                    <input type="text" name="prefixe" id="editPrefixe" class="form-control" maxlength="10" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger"><i class="fas fa-trash me-2"></i>Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Supprimer le préfixe <strong id="deletePrefixeLabel"></strong> ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
function openEdit(id, prefixe) {
    document.getElementById('editPrefixe').value = prefixe;
    document.getElementById('editForm').action = '<?= base_url('admin/operators/update/') ?>' + id;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function confirmDelete(id, prefixe) {
    document.getElementById('deletePrefixeLabel').textContent = prefixe;
    document.getElementById('deleteForm').action = '<?= base_url('admin/operators/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
