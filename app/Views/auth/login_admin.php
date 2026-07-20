<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-12 col-sm-10 col-md-8 col-lg-5">

            <div class="card shadow-lg border-0 rounded-4">

                <div class="card-header bg-success text-white text-center py-4 rounded-top-4">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>

                    <h3 class="fw-bold mb-1">
                        Administration
                    </h3>

                    <p class="mb-0">
                        Back-office Mobile Money
                    </p>
                </div>

                <div class="card-body p-4">

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-circle-exclamation me-2"></i>
                            <?= session()->getFlashdata('error') ?>

                            <button class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('login/admin') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Email
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="admin@gmail.com"
                                    value="admin@gmail.com"
                                    required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Mot de passe
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="********"
                                    value="admin123"
                                    required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter
                        </button>

                    </form>

                </div>

                <div class="card-footer bg-white border-0 text-center pb-4">

                    <hr>

                    <a href="<?= base_url('login/client') ?>"
                       class="btn btn-outline-success rounded-pill px-4 mb-2">
                        <i class="fas fa-mobile-alt me-2"></i>
                        Aller à l'espace Client
                    </a>

                </div>

            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>