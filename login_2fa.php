<?php
require "ligacao.php";
require_once __DIR__ . '/functions_2fa.php';

session_start();

// Redireciona se o utilizador já estiver logado ou se não houver um ID temporário para 2FA
if (isset($_SESSION['user_id']) || !isset($_SESSION['tfa_user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['tfa_user_id'];
$error = '';

// 1. Obter o segredo 2FA do utilizador
$stmt = $conn->prepare("SELECT tfa_secret, username FROM user WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$secret = $user['tfa_secret'] ?? null;
$username = $user['username'] ?? 'Utilizador';

if (!$secret) {
    // Se por algum motivo o segredo não existir, volta para o login
    unset($_SESSION['tfa_user_id']);
    unset($_SESSION['tfa_username']);
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    try {
        if (function_exists('verify_tfa_code') && verify_tfa_code($secret, $code)) {
            // Código 2FA Válido!

            // Finaliza o login
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['last_activity'] = time();

            // Limpa as variáveis temporárias
            unset($_SESSION['tfa_user_id']);
            unset($_SESSION['tfa_username']);


            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Código incorreto. Tente novamente.";

        }
    } catch (Exception $e) {
        $error = "Erro interno. Tente novamente.";
        error_log("2FA Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verificação de Segurança - CyberVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #06b6d4;
            --primary-hover: #0891b2;
            --secondary: #3b82f6;
            --bg-dark: #020617;
            --card-bg: rgba(15, 23, 42, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }



        /* Glass Card */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Icon Animation */
        .icon-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(59, 130, 246, 0.1));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }

        .icon-container svg {
            width: 40px;
            height: 40px;
            color: var(--primary);
            filter: drop-shadow(0 0 10px rgba(6, 182, 212, 0.5));
            animation: pulseIcon 3s infinite ease-in-out;
        }

        @keyframes pulseIcon {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        /* Input Fields */
        .otp-container {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin: 1.5rem 0;
        }

        .otp-input {
            width: 45px;
            height: 55px;
            background: rgba(2, 6, 23, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            caret-color: var(--primary);
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.15);
            transform: translateY(-2px);
            background: rgba(2, 6, 23, 0.8);
        }

        .otp-input:not(:placeholder-shown) {
            border-color: rgba(6, 182, 212, 0.5);
            background: rgba(6, 182, 212, 0.05);
        }

        /* Button */
        .btn-verify {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 20px -5px rgba(6, 182, 212, 0.4);
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(6, 182, 212, 0.5);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .btn-verify::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(255, 255, 255, 0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-verify:hover::after {
            opacity: 1;
        }

        /* Error Message */
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }

        /* Footer */
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .footer-link:hover {
            color: var(--primary);
        }

        .footer-link svg {
            width: 16px;
            height: 16px;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../includes/particle_background.php'; ?>

    <div class="glass-card">
        <div class="text-center">
            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Verificação em Duas Etapas</h1>
            <p class="text-slate-400 text-sm mb-6">
                Digite o código de 6 dígitos gerado pelo seu aplicativo autenticador.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-msg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" id="otpForm">
            <input type="hidden" name="code" id="fullCode">

            <div class="otp-container">
                <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric"
                    autocomplete="one-time-code" required>
                <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" required>
                <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" required>
            </div>

            <button type="submit" class="btn-verify mt-6">
                Verificar Identidade
            </button>
        </form>

        <div class="mt-8 text-center border-t border-slate-700/50 pt-6">
            <a href="logout.php" class="footer-link">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Voltar para o Login
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.otp-input');
            const form = document.getElementById('otpForm');
            const fullCodeInput = document.getElementById('fullCode');

            // Focus first input on load
            inputs[0].focus();

            inputs.forEach((input, index) => {
                // Handle input
                input.addEventListener('input', (e) => {
                    const value = e.target.value;

                    // Allow only numbers
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    if (value.length === 1) {
                        // Move to next input
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        } else {
                            // If last input, submit or focus out
                            input.blur();
                            submitIfComplete();
                        }
                    }
                });

                // Handle backspace and navigation
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowLeft' && index > 0) {
                        inputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);

                    if (pastedData) {
                        pastedData.split('').forEach((char, i) => {
                            if (inputs[i]) {
                                inputs[i].value = char;
                            }
                        });

                        // Focus the next empty input or the last one
                        const nextIndex = Math.min(pastedData.length, inputs.length - 1);
                        inputs[nextIndex].focus();

                        submitIfComplete();
                    }
                });
            });

            function submitIfComplete() {
                const code = Array.from(inputs).map(i => i.value).join('');
                if (code.length === 6) {
                    fullCodeInput.value = code;
                    // Optional: Auto submit
                    // form.submit();
                }
            }

            form.addEventListener('submit', (e) => {
                const code = Array.from(inputs).map(i => i.value).join('');
                fullCodeInput.value = code;

                if (code.length !== 6) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Código Incompleto',
                        text: 'Por favor, digite os 6 dígitos do código.',
                        background: '#0f172a',
                        color: '#cbd5e1',
                        confirmButtonColor: '#06b6d4'
                    });
                }
            });
        });
    </script>
</body>

</html>