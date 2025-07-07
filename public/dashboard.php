<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Requer autenticação para acessar esta página
$user = requireAuth();
$userEmail = $user['email'];

$message = getErrorFromUrl();
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Login</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><circle cx="20" cy="20" r="18" fill="%23222" stroke="%23444" stroke-width="2"/><circle cx="20" cy="20" r="8" fill="none" stroke="%230070f3" stroke-width="3"/></svg>'>
    <link rel="stylesheet" href="../assets/css/2fa.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <h2>Dashboard</h2>

            <?php if ($message): ?>
                <div class="message <?= strpos($message, 'sucesso') !== false || strpos($message, 'ativado') !== false ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <p>Bem-vindo, <?php echo htmlspecialchars($userEmail); ?>!</p>
            <p>Você está autenticado com sucesso.</p>

            <div class="security-section">
                <h3>🔐 Segurança da Conta</h3>
                <p>
                    <strong>Autenticação de Dois Fatores:</strong>
                    <?php if (has2FA($user['id'])): ?>
                        <span class="status-badge status-enabled">✅ Ativada</span>
                    <?php else: ?>
                        <span class="status-badge status-disabled">❌ Desativada</span>
                    <?php endif; ?>
                </p>

                <div style="margin-top: 15px;">
                    <?php if (has2FA($user['id'])): ?>
                        <a href="manage-2fa.php" class="btn btn-secondary">Gerenciar 2FA</a>
                    <?php else: ?>
                        <a href="setup-2fa.php" class="btn btn-primary">Ativar 2FA</a>
                    <?php endif; ?>
                </div>
            </div>

            <p>Esta é a área protegida do sistema, onde você pode acessar informações exclusivas.</p>

            <div style="margin-top: 20px;">
                <a href="logout.php" class="logout-btn" style="text-decoration: none; text-align: center; display: block;">
                    Logout
                </a>
            </div>
        </div>
    </div>
</body>

</html>