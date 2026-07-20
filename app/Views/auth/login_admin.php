<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - Mobile Money</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --blue-primary: #1e3a8a;
            --blue-secondary: #3b82f6;
            --blue-dark: #1e40af;
            --bg-glass: rgba(255, 255, 255, 0.90);
        }
        
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', 'Inter', sans-serif;
            padding: 20px;
        }

        .login-container {
            background: var(--bg-glass);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideUp 0.6s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section i {
            font-size: 50px;
            color: var(--blue-secondary);
            margin-bottom: 10px;
        }

        .logo-section h2 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .logo-section p {
            color: #64748b;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--blue-secondary);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--blue-secondary), var(--blue-dark));
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--blue-primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .alert {
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
        }

        .link-switch {
            text-decoration: none;
            color: #475569;
            font-weight: 600;
            font-size: 14px;
            transition: color 0.3s;
            display: inline-block;
            margin-top: 15px;
        }

        .link-switch:hover {
            color: var(--blue-secondary);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
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
            <label for="email" class="form-label font-weight-bold" style="color: #334155;">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-2 border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                    <i class="fas fa-envelope text-muted"></i>
                </span>
                <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="admin@gmail.com" required style="border-radius: 0 12px 12px 0;" value="admin@gmail.com">
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label font-weight-bold" style="color: #334155;">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-2 border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e2e8f0;">
                    <i class="fas fa-lock text-muted"></i>
                </span>
                <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="••••••••" required style="border-radius: 0 12px 12px 0;" value="admin123">
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100 btn-lg mb-3">
            <i class="fas fa-sign-in-alt me-2"></i> Se Connecter
        </button>

        <div class="text-center mt-3">
            <hr class="my-3" style="border-color: #cbd5e1;">
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
