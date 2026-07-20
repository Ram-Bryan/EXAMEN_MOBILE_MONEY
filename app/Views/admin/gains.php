<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color: #0f172a;">Situation des Gains</h2>
    <p class="text-muted mb-0" style="font-size: 14px;">Agrégat des frais collectés par type d'opération (retrait & transfert)</p>
</div>

<?php 
$totalGains = 0;
foreach ($gains ?? [] as $g) { $totalGains += $g->total_gains ?? 0; }
$typeIcons = [
    'Dépôt' => ['icon' => 'fa-arrow-down', 'bg' => '#f0fdf4', 'color' => '#16a34a'],
    'Retrait' => ['icon' => 'fa-arrow-up', 'bg' => '#fef2f2', 'color' => '#dc2626'],
    'Transfert' => ['icon' => 'fa-exchange-alt', 'bg' => '#eff6ff', 'color' => '#2563eb'],
];
?>

<!-- Total Card -->
<div class="stat-card mb-4" style="border-left: 4px solid #2563eb;">
    <div class="icon blue"><i class="fas fa-coins"></i></div>
    <div class="details">
        <h3><?= number_format($totalGains, 0, ',', ' ') ?> Ar</h3>
        <p>Total des gains (tous types confondus)</p>
    </div>
</div>

<div class="row g-4">
    <?php foreach ($gains ?? [] as $gain): 
        $style = $typeIcons[$gain->type_operation] ?? ['icon' => 'fa-circle', 'bg' => '#f8fafc', 'color' => '#64748b'];
    ?>
    <div class="col-md-4">
        <div class="premium-card h-100">
            <div class="card-body text-center py-4">
                <div style="width:72px;height:72px;border-radius:50%;background:<?= $style['bg'] ?>;color:<?= $style['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 20px;">
                    <i class="fas <?= $style['icon'] ?>"></i>
                </div>
                <p class="text-muted mb-1" style="font-size:13px; text-transform:uppercase; letter-spacing:1px; font-weight:600;"><?= esc($gain->type_operation) ?></p>
                <h3 style="font-size:28px; font-weight:800; color:<?= $style['color'] ?>; margin:0;">
                    <?= number_format($gain->total_gains ?? 0, 0, ',', ' ') ?> Ar
                </h3>
                <p class="text-muted mt-2" style="font-size:12px;">frais collectés</p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($gains)): ?>
    <div class="col-12">
        <div class="premium-card">
            <div class="card-body text-center py-5 text-muted">
                <i class="fas fa-chart-pie fa-3x mb-3 d-block" style="color:#e2e8f0;"></i>
                Aucune donnée de gains disponible.
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
