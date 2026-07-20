<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - Mobile Money</title>
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
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-section">
        <i class="fas fa-shield-alt"></i>
        <h2>Administration</h2>
        <p>Back-office Mobile Money</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('login/admin') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" class="form-control" id="email" name="email" placeholder="admin@gmail.com" required value="admin@gmail.com">
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required value="admin123">
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100 btn-lg mb-3">
            <i class="fas fa-sign-in-alt me-2"></i> Se Connecter
        </button>

        <div class="text-center mt-3">
            <hr class="my-3">
            <a href="<?= base_url('login/client') ?>" class="link-switch">
                <i class="fas fa-mobile-alt me-1"></i> Aller à l'espace Client
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
