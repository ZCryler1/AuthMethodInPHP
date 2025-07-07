<?php
require_once __DIR__ . '/../includes/functions.php';

// Verifica se está logado
$user = requireAuth();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['disable_2fa'])) {
        $password = $_POST['password'] ?? '';
        $code = sanitizeInput($_POST['verification_code'] ?? '');

        // Verifica senha atual
        $userData = getUserByEmail($user['email']);
        if (!password_verify($password, $userData['password'])) {
            $message = 'Senha incorreta.';
        } elseif (!verify2FACode($user['id'], $code)) {
            $message = 'Código 2FA inválido.';
        } else {
            if (disable2FA($user['id'])) {
                header('Location: dashboard.php?message=2FA desativado com sucesso.');
                exit;
            } else {
                $message = 'Erro ao desativar 2FA.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar 2FA</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="icon" type="image/svg+xml" href='data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"><circle cx="20" cy="20" r="18" fill="%23222" stroke="%23444" stroke-width="2"/><circle cx="20" cy="20" r="8" fill="none" stroke="%230070f3" stroke-width="3"/></svg>'>
    <style>
        .warning {
            background: #ffecaf;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 22px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .danger-zone {
            background: #faa8a8;
            border: 1px solid #f8d7da;
            border-radius: 8px;
            padding: 22px 20px;
            margin-top: 28px;
        }

        .danger-zone h3 {
            color: #c82333;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .danger-zone p {
            color: #721c24;
            margin-bottom: 18px;
            font-size: 15px;
        }

        .danger-zone form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="background-circuit">
        <div class="left-top"></div>
        <div class="left-bottom"></div>
        <div class="right-top"></div>
        <div class="right-bottom"></div>
    </div>

    <div class="login-container ">
        <div class="login-card">
            <h2>Gerenciar Autenticação de Dois Fatores</h2>

            <?php if ($message): ?>
                <div class="message error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (has2FA($user['id'])): ?>
                <div class="warning">
                    <strong>⚠️</strong> Você tem a autenticação de dois fatores ativada.
                    Isso adiciona uma camada extra de segurança à sua conta.
                </div>

                <div class="danger-zone">
                    <h3>🔴 Desativar 2FA</h3>
                    <p><strong>Cuidado:</strong> Desativar o 2FA tornará sua conta menos segura.</p>

                    <form method="POST">
                        <div class="input-group" style="
                        display: flex;
                        flex-direction: column;">
                            <p style="align-self: flex-start;">Senha atual:</p>
                            <input type="password" id="password" name="password" placeholder="Sua senha" required>
                        </div>

                        <div class="input-group" style=" 
                        display: flex;
                        flex-direction: column;">
                            <p style="align-self: flex-start;">Código de verificação do Google Authenticator:</p>
                            <input type="text"
                                id="verification_code"
                                name="verification_code"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                placeholder="000000"
                                required>
                        </div>

                        <div class="input-group" style="width: 210px;
                        display: flex;
                        flex-direction: column;">
                            <button type="submit"
                                name="disable_2fa"
                                class="btn btn-danger"
                                onclick="return confirm('Tem certeza que deseja desativar o 2FA? Sua conta ficará menos segura.')">
                                Desativar 2FA
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <div class="warning">
                    <strong>ℹ️ Info:</strong> Você não tem a autenticação de dois fatores ativada.
                </div>

                <p>Para maior segurança da sua conta, recomendamos ativar o 2FA.</p>

                <div class="form-actions">
                    <a href="setup-2fa.php" class="btn btn-primary">Ativar 2FA</a>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>