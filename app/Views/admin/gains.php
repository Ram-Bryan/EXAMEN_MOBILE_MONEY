<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Situation des Gains</h2>
    <p class="text-muted mb-0" style="font-size: 14px;">Frais collectés par type d'opération</p>
</div>

<?php
$types = ['DEPOT', 'RETRAIT', 'TRANSFERT'];
$labels = ['DEPOT' => 'Dépôt', 'RETRAIT' => 'Retrait', 'TRANSFERT' => 'Transfert'];
$icons  = ['DEPOT' => 'fa-arrow-down', 'RETRAIT' => 'fa-arrow-up', 'TRANSFERT' => 'fa-exchange-alt'];
$colors = ['DEPOT' => '#16a34a', 'RETRAIT' => '#dc2626', 'TRANSFERT' => '#2563eb'];

$nouveauTotal = array_sum(array_column($gainsNotreOp, 'total_gains'));
$nouveauNb    = array_sum(array_column($gainsNotreOp, 'nb_operations'));
?>

<!-- ============================================================ -->
<!-- SECTION 1 : Notre Opérateur                                   -->
<!-- ============================================================ -->
<div class="premium-card mb-4">
    <div class="card-header">
        <span>
            <span style="background:#dcfce7; color:#16a34a; padding:4px 14px; border-radius:20px; font-size:12px; font-weight:600; margin-right:10px;">
                <i class="fas fa-star me-1"></i>Notre Opérateur
            </span>
            Mobile Money (033)
        </span>
        <span class="fw-bold text-success" style="font-size:18px;"><?= number_format($nouveauTotal, 0, ',', ' ') ?> Ar</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th style="width:50%;">Type d'opération</th>
                    <th style="text-align:right; width:25%;">Frais collectés</th>
                    <th style="text-align:right; padding-right:24px; width:25%;">Nb opérations</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $nouveauData = [];
                foreach ($gainsNotreOp as $g) {
                    $nouveauData[$g->type_operation] = $g;
                }
                foreach ($types as $code):
                    $g = $nouveauData[$code] ?? null;
                    $frais = $g ? (float)$g->total_gains : 0;
                    $nb    = $g ? (int)$g->nb_operations : 0;
                ?>
                <tr>
                    <td>
                        <i class="fas <?= $icons[$code] ?> me-2" style="color:<?= $colors[$code] ?>;"></i>
                        <strong><?= $labels[$code] ?></strong>
                    </td>
                    <td style="text-align:right;">
                        <?php if ($frais > 0): ?>
                            <span class="fw-bold text-success"><?= number_format($frais, 0, ',', ' ') ?> Ar</span>
                        <?php else: ?>
                            <span class="text-muted">0 Ar</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:right; padding-right:24px;">
                        <span class="fw-bold" style="color: var(--text-dark);"><?= $nb ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f0fdf4; border-top: 2px solid #bbf7d0;">
                    <td><strong style="color: #16a34a;">Total Notre Opérateur</strong></td>
                    <td style="text-align:right;">
                        <strong class="text-success" style="font-size:16px;"><?= number_format($nouveauTotal, 0, ',', ' ') ?> Ar</strong>
                    </td>
                    <td style="text-align:right; padding-right:24px;">
                        <strong style="font-size:16px;"><?= $nouveauNb ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================================ -->
<!-- SECTION 2 : Autre Opérateur                                   -->
<!-- ============================================================ -->
<div class="premium-card mb-4">
    <div class="card-header">
        <span>
            <span style="background:#fef3c7; color:#d97706; padding:4px 14px; border-radius:20px; font-size:12px; font-weight:600; margin-right:10px;">
                <i class="fas fa-exchange-alt me-1"></i>Autre Opérateur
            </span>
            Transferts sortants vers un opérateur externe
        </span>
        <div>
            <select id="externalOpSelect" class="form-select form-select-sm" style="min-width:200px; font-weight:600;">
                <?php if (empty($externalOps)): ?>
                    <option disabled selected>Aucun opérateur externe</option>
                <?php else: ?>
                    <?php foreach ($externalOps as $op): ?>
                        <option value="<?= $op->id ?>"><?= esc($op->nom) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="card-body p-0">

        <?php if (empty($externalOps)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-info-circle fa-2x mb-3 d-block" style="color:#d97706;"></i>
                Aucun opérateur externe configuré.
            </div>
        <?php else: ?>
            <?php foreach ($externalOps as $op):
                $opId = $op->id;
                $opGains = $gainsExternesParOp[$opId] ?? [];
                $opData = [];
                foreach ($opGains as $g) {
                    $opData[$g->type_operation] = $g;
                }
                $totalOp = array_sum(array_column($opGains, 'total_gains'));
                $nbOp    = array_sum(array_column($opGains, 'nb_operations'));
            ?>
            <div class="external-op-table" data-op-id="<?= $opId ?>" style="<?= $opId !== ($externalOps[0]->id ?? '') ? 'display:none;' : '' ?>">
                <table class="table-custom w-100">
                    <thead>
                        <tr>
                            <th style="width:50%;">Type d'opération</th>
                            <th style="text-align:right; width:25%;">Frais collectés</th>
                            <th style="text-align:right; padding-right:24px; width:25%;">Nb opérations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($types as $code):
                            $g = $opData[$code] ?? null;
                            $frais = $g ? (float)$g->total_gains : 0;
                            $nb    = $g ? (int)$g->nb_operations : 0;
                        ?>
                        <tr>
                            <td>
                                <i class="fas <?= $icons[$code] ?> me-2" style="color:<?= $colors[$code] ?>;"></i>
                                <strong><?= $labels[$code] ?></strong>
                                <?php if ($code !== 'TRANSFERT' && $frais == 0): ?>
                                    <small class="text-muted ms-2">(non applicable)</small>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <?php if ($frais > 0): ?>
                                    <span class="fw-bold" style="color:#d97706;"><?= number_format($frais, 0, ',', ' ') ?> Ar</span>
                                <?php else: ?>
                                    <span class="text-muted">0 Ar</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right; padding-right:24px;">
                                <span class="fw-bold" style="color: var(--text-dark);"><?= $nb ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: #fffbeb; border-top: 2px solid #fde68a;">
                            <td><strong style="color: #d97706;">Total <?= esc($op->nom) ?></strong></td>
                            <td style="text-align:right;">
                                <strong style="color:#d97706; font-size:16px;"><?= number_format($totalOp, 0, ',', ' ') ?> Ar</strong>
                            </td>
                            <td style="text-align:right; padding-right:24px;">
                                <strong style="font-size:16px;"><?= $nbOp ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('externalOpSelect')?.addEventListener('change', function() {
    const selected = this.value;
    document.querySelectorAll('.external-op-table').forEach(function(el) {
        el.style.display = el.dataset.opId == selected ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
