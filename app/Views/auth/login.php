<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur | Mobile Money</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            text-align: center;
        }
        .login-card .icon-container {
            width: 80px;
            height: 80px;
            background: #eff6ff;
            color: #38bdf8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
            box-shadow: 0 4px 10px rgba(56, 189, 248, 0.2);
        }
        .login-card h3 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .login-card p {
            color: #64748b;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-control {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            background: #fff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }
        .btn-login {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            font-size: 16px;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="icon-container">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h3>Administration</h3>
        <p>Connectez-vous pour accéder au back-office</p>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="font-size: 14px; text-align: left;">
                <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST">
            <div class="mb-3 text-start">
                <label class="form-label fw-semibold" style="font-size: 14px; color: #475569;">Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required value="admin@gmail.com">
            </div>
            <div class="mb-4 text-start">
                <label class="form-label fw-semibold" style="font-size: 14px; color: #475569;">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required value="admin123">
            </div>
            <button type="submit" class="btn-login">Se Connecter <i class="fas fa-arrow-right ms-2"></i></button>
        </form>
    </div>

</body>
</html>
