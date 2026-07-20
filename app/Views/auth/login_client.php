<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client - Mobile Money</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 50%, #22c55e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }

        .prefix-badge {
            background-color: var(--primary-badge-bg, #dcfce7);
            color: var(--primary, #16a34a);
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 13px;
            margin: 4px;
            display: inline-block;
        }

        .logo-section i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-section">
        <i class="fas fa-wallet"></i>
        <h2>Mobile Money</h2>
        <p>Espace Client</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('login/client') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="phone" class="form-label">Numéro de téléphone</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-phone-alt"></i>
                </span>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Ex: 0341234567" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="code" class="form-label">Code PIN</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" class="form-control" id="code" name="code" placeholder="Entrez votre code PIN">
            </div>
        </div>

        <div class="mb-4 text-center">
            <small class="text-muted d-block mb-1">Préfixes valides de l'opérateur :</small>
            <div class="d-flex justify-content-center flex-wrap">
                <span class="prefix-badge">033</span>
                <span class="prefix-badge">034</span>
                <span class="prefix-badge">037</span>
                <span class="prefix-badge">038</span>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100 btn-lg mb-3">
            <i class="fas fa-sign-in-alt me-2"></i> Se Connecter
        </button>

        <div class="text-center mt-3">
            <small class="text-muted d-block mb-2">
                Première connexion ? Entrez votre numéro pour créer un compte automatiquement.
            </small>
            <hr class="my-3">
            <a href="<?= base_url('login/admin') ?>" class="link-switch">
                <i class="fas fa-user-shield me-1"></i> Connexion Administrateur
            </a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
</body>
</html>
