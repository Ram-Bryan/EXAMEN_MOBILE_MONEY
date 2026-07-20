<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color: var(--text-dark);">Comptes Clients</h2>
    <p class="text-muted mb-0" style="font-size: 14px;">Situation des soldes de l'ensemble des comptes clients</p>
</div>

<div class="card">
    <div class="card-header">
        <span><i class="fas fa-users me-2"></i> Liste des clients et leurs soldes</span>
        <span class="badge bg-green" style="font-size:13px;"><?= count($clients ?? []) ?> client(s)</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Opérateur</th>
                    <th style="text-align:right; padding-right:24px;">Solde</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $client): ?>
                    <?php $solde = $client->solde ?? 0; ?>
                    <tr>
                        <td><span class="text-muted" style="font-size:13px;">#<?= esc($client->client_id) ?></span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;">
                                    <?= strtoupper(substr($client->nom, 0, 1)) ?>
                                </div>
                                <strong><?= esc($client->nom) ?></strong>
                            </div>
                        </td>
                        <td style="font-family:monospace; font-size:14px;"><?= esc($client->telephone) ?></td>
                        <td>
                            <span class="badge bg-green" style="padding:4px 12px; font-size:13px; border-radius:20px;">
                                <?= esc($client->operateur) ?>
                            </span>
                        </td>
                        <td style="text-align:right; padding-right:24px;">
                            <span style="font-size:16px; font-weight:700; color:<?= $solde >= 0 ? 'var(--primary)' : '#dc2626' ?>;">
                                <?= number_format($solde, 0, ',', ' ') ?> Ar
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-2x mb-3 d-block" style="color: var(--primary-border);"></i>Aucun client trouvé.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
