<?php
$page_title = 'Dépôt';
ob_start();
?>

<div class="card">
    <div class="card-header bg-orange text-white">
        <h5><i class="fas fa-plus-circle"></i> Effectuer un dépôt</h5>
    </div>
    <div class="card-body">
        <form id="depositForm" action="/api/client/deposit" method="POST">
            <div class="mb-4">
                <label class="form-label">Montant à déposer</label>
                <div class="input-group">
                    <span class="input-group-text">Ar</span>
                    <input type="number" class="form-control required" name="amount" placeholder="Entrez le montant" required>
                </div>
                <small class="text-muted">Le dépôt est automatique. Aucun frais n'est appliqué.</small>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Méthode de dépôt</label>
                <select class="form-control required" name="method">
                    <option value="mobile">Mobile Money</option>
                    <option value="bank">Virement bancaire</option>
                    <option value="cash">Espèces</option>
                </select>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Le dépôt sera crédité immédiatement sur votre compte.
            </div>
            
            <button type="submit" class="btn btn-orange btn-lg w-100">
                <i class="fas fa-check-circle"></i> Confirmer le dépôt
            </button>
        </form>
    </div>
</div>

<!-- Fee Schedule -->
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-table"></i> Barème des frais</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Montant</th>
                        <th>Frais</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>100 - 1,000 Ar</td><td>50 Ar</td></tr>
                    <tr><td>1,000 - 5,000 Ar</td><td>50 Ar</td></tr>
                    <tr><td>5,000 - 10,000 Ar</td><td>100 Ar</td></tr>
                    <tr><td>10,000 - 25,000 Ar</td><td>200 Ar</td></tr>
                    <tr><td>25,000 - 50,000 Ar</td><td>400 Ar</td></tr>
                    <tr><td>50,000 - 100,000 Ar</td><td>800 Ar</td></tr>
                    <tr><td>100,000 - 250,000 Ar</td><td>1,500 Ar</td></tr>
                    <tr><td>250,000 - 500,000 Ar</td><td>2,500 Ar</td></tr>
                    <tr><td>500,000 - 1,000,000 Ar</td><td>3,000 Ar</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#depositForm').on('submit', function(e) {
    e.preventDefault();
    submitForm('depositForm', function(response) {
        if (response.success) {
            window.location.href = '/client/dashboard.php';
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include '../includes/layouts/client.php';
?>