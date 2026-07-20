<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Mobile Money</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --orange-primary: #FF6B00;
            --orange-secondary: #FF8C38;
            --orange-dark: #CC5500;
            --bg-glass: rgba(255, 255, 255, 0.85);
        }
        
        body {
            background: linear-gradient(135deg, var(--orange-primary), var(--orange-secondary));
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideUp 0.6s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section i {
            font-size: 60px;
            color: var(--orange-primary);
            margin-bottom: 10px;
            animation: pulse 2s infinite;
        }

        .logo-section h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .logo-section p {
            color: #666;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--orange-primary);
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 0, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--orange-primary), var(--orange-secondary));
            border: none;
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.4);
            color: white;
        }

        .prefix-badge {
            background-color: rgba(255, 107, 0, 0.1);
            color: var(--orange-primary);
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 13px;
            margin: 4px;
            display: inline-block;
        }

        .alert {
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-section">
        <i class="fas fa-wallet"></i>
        <h2>Mobile Money</h2>
        <p>Connexion rapide & sécurisée à votre espace client</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('login') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div class="mb-4">
            <label for="phone" class="form-label font-weight-bold" style="color: #333;">Numéro de téléphone</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-2 border-end-0" style="border-radius: 12px 0 0 12px; border-color: #e0e0e0;">
                    <i class="fas fa-phone-alt text-muted"></i>
                </span>
                <input type="tel" class="form-control border-start-0" id="phone" name="phone" placeholder="Ex: 0331234567" required style="border-radius: 0 12px 12px 0;">
            </div>
            <div class="mt-3 text-center">
                <small class="text-muted d-block mb-1">Préfixes valides de l'opérateur :</small>
                <div class="d-flex justify-content-center flex-wrap">
                    <span class="prefix-badge">033 (Orange)</span>
                    <span class="prefix-badge">034 (Telma)</span>
                    <span class="prefix-badge">037 (Airtel)</span>
                    <span class="prefix-badge">038 (Airtel)</span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100 btn-lg mb-3">
            <i class="fas fa-sign-in-alt me-2"></i> Se Connecter
        </button>

        <div class="text-center mt-3">
            <small class="text-muted">
                Pas encore de compte ? Saisissez simplement un numéro valide pour créer automatiquement votre compte.
            </small>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide alert
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
</body>
</html>
