<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Situation des Gains</h2>
    <p class="text-muted mb-0" style="font-size: 14px;">Frais collectés par type d'opération, séparés par opérateur</p>
</div>

<?php
// Séparation des gains v2 (notre opérateur vs autres opérateurs)
$notreOp = [];
$autresOps = [];
foreach ($gainsSepares ?? [] as $g) {
    if ($g->est_notre_operateur == 1) {
        $notreOp[] = $g;
    } else {
        $autresOps[] = $g;
    }
}

$totalNous = array_sum(array_column($notreOp, 'total_gains'));
$totalAutres = array_sum(array_column($autresOps, 'total_gains'));
$totalGlobal = $totalNous + $totalAutres;

$typeIcons = [
    'Dépôt'    => ['icon' => 'fa-arrow-down', 'color' => '#16a34a'],
    'Retrait'  => ['icon' => 'fa-arrow-up',   'color' => '#dc2626'],
    'Transfert'=> ['icon' => 'fa-exchange-alt','color'=> '#2563eb'],
];
?>

<!-- Total Général -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="icon green"><i class="fas fa-coins"></i></div>
            <div class="details">
                <h3><?= number_format($totalGlobal, 0, ',', ' ') ?> Ar</h3>
                <p>Total gains global</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #16a34a;">
            <div class="icon" style="background:#dcfce7; color:#16a34a;"><i class="fas fa-star"></i></div>
            <div class="details">
                <h3><?= number_format($totalNous, 0, ',', ' ') ?> Ar</h3>
                <p>Notre opérateur</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="icon" style="background:#fef3c7; color:#f59e0b;"><i class="fas fa-exchange-alt"></i></div>
            <div class="details">
                <h3><?= number_format($totalAutres, 0, ',', ' ') ?> Ar</h3>
                <p>Opérateurs externes</p>
            </div>
        </div>
    </div>
</div>

<!-- Bloc Notre Opérateur -->
<div class="premium-card mb-4">
    <div class="card-header">
        <span>
            <span style="background:#dcfce7; color:#16a34a; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; margin-right:10px;">
                <i class="fas fa-star me-1"></i>Notre Opérateur
            </span>
            Gains par type d'opération
        </span>
        <span class="fw-bold text-success"><?= number_format($totalNous, 0, ',', ' ') ?> Ar</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Type d'opération</th>
                    <th style="text-align:right; padding-right:24px;">Total frais collectés</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($notreOp)): ?>
                    <tr><td colspan="2" class="text-center text-muted py-4">Aucune donnée.</td></tr>
                <?php else: ?>
                    <?php foreach ($notreOp as $g):
                        $style = $typeIcons[$g->type_operation] ?? ['icon' => 'fa-circle', 'color' => '#64748b'];
                    ?>
                    <tr>
                        <td>
                            <i class="fas <?= $style['icon'] ?> me-2" style="color:<?= $style['color'] ?>;"></i>
                            <strong><?= esc($g->type_operation) ?></strong>
                        </td>
                        <td style="text-align:right; padding-right:24px;">
                            <span class="fw-bold text-success"><?= number_format($g->total_gains, 0, ',', ' ') ?> Ar</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bloc Opérateurs Externes -->
<div class="premium-card mb-4">
    <div class="card-header">
        <span>
            <span style="background:#fef3c7; color:#d97706; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600; margin-right:10px;">
                <i class="fas fa-exchange-alt me-1"></i>Opérateurs Externes
            </span>
            Gains via commissions inter-opérateurs
        </span>
        <span class="fw-bold" style="color:#d97706;"><?= number_format($totalAutres, 0, ',', ' ') ?> Ar</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Type d'opération</th>
                    <th style="text-align:right; padding-right:24px;">Total frais collectés</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($autresOps)): ?>
                    <tr><td colspan="2" class="text-center text-muted py-4">Aucune commission inter-opérateur enregistrée.</td></tr>
                <?php else: ?>
                    <?php foreach ($autresOps as $g):
                        $style = $typeIcons[$g->type_operation] ?? ['icon' => 'fa-circle', 'color' => '#64748b'];
                    ?>
                    <tr>
                        <td>
                            <i class="fas <?= $style['icon'] ?> me-2" style="color:<?= $style['color'] ?>;"></i>
                            <strong><?= esc($g->type_operation) ?></strong>
                        </td>
                        <td style="text-align:right; padding-right:24px;">
                            <span class="fw-bold" style="color:#d97706;"><?= number_format($g->total_gains, 0, ',', ' ') ?> Ar</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bloc Montants à envoyer à chaque opérateur externe -->
<div class="premium-card mb-4">
    <div class="card-header">
        <span><i class="fas fa-paper-plane me-2 text-danger"></i> Montants à envoyer aux opérateurs externes (Settlement)</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Opérateur</th>
                    <th style="text-align:right; padding-right:24px;">Montant total des transferts</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($montantsAEnvoyer)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> Aucun transfert inter-opérateur enregistré.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $totalSettlement = array_sum(array_column($montantsAEnvoyer, 'montant_total_a_envoyer'));
                    foreach ($montantsAEnvoyer as $m):
                    ?>
                    <tr>
                        <td>
                            <strong><?= esc($m->nom) ?></strong>
                        </td>
                        <td style="text-align:right; padding-right:24px;">
                            <span class="fw-bold text-danger"><?= number_format($m->montant_total_a_envoyer, 0, ',', ' ') ?> Ar</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                        <td><strong style="color: #0f172a; font-size: 15px;">Total à envoyer</strong></td>
                        <td style="text-align:right; padding-right:24px;">
                            <strong class="text-danger" style="font-size: 18px;"><?= number_format($totalSettlement, 0, ',', ' ') ?> Ar</strong>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
