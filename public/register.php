<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    header('Location: /webshoppen/public/');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username && $email && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = 'Lösenorden matchar inte';
        } elseif (strlen($password) < 6) {
            $error = 'Lösenordet måste vara minst 6 tecken långt';
        } else {
            // Kontrollera om användarnamnet redan finns
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Användarnamnet är redan taget';
            } else {
                // Kontrollera om e-postadressen redan finns
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'E-postadressen är redan registrerad';
                } else {
                    // Skapa användaren
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                    
                    try {
                        $stmt->execute([$username, $email, $hashed_password]);
                        $success = 'Ditt konto har skapats! Du kan nu logga in.';
                    } catch (PDOException $e) {
                        $error = 'Ett fel uppstod vid registreringen. Försök igen.';
                    }
                }
            }
        }
    } else {
        $error = 'Vänligen fyll i alla fält';
    }
}

require_once '../includes/header.php';
?>

<link rel="stylesheet" href="/webshoppen/public/css/register.css">

<div class="register-container">
    <h1>Skapa konto</h1>
    <p class="intro">Vänligen fyll i dina uppgifter för att skapa ett konto.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= sanitize($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/webshoppen/public/register.php" id="registerForm">
        <div class="form-group">
            <label for="username">Användarnamn</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   required 
                   minlength="3"
                   autocomplete="username"
                   placeholder="Välj ett användarnamn">
            <div class="requirements">Minst 3 tecken</div>
        </div>

        <div class="form-group">
            <label for="email">E-postadress</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   required
                   autocomplete="email"
                   placeholder="Din e-postadress">
        </div>

        <div class="form-group">
            <label for="password">Lösenord</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required
                   minlength="6"
                   autocomplete="new-password"
                   placeholder="Välj ett säkert lösenord">
            <div class="password-strength">
                <div class="password-strength-bar"></div>
            </div>
            <div class="requirements">Minst 6 tecken</div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Bekräfta lösenord</label>
            <input type="password" 
                   id="confirm_password" 
                   name="confirm_password" 
                   required
                   autocomplete="new-password"
                   placeholder="Upprepa ditt lösenord">
        </div>

        <button type="submit" class="register-btn">Skapa konto</button>
    </form>

    <p class="login-link">
        Har du redan ett konto? <a href="/webshoppen/public/login.php">Logga in här</a>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.querySelector('.password-strength-bar');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        
        strengthBar.style.width = (strength / 3 * 100) + '%';
        
        if (strength === 1) {
            strengthBar.style.backgroundColor = '#e33';
        } else if (strength === 2) {
            strengthBar.style.backgroundColor = '#fc3';
        } else if (strength === 3) {
            strengthBar.style.backgroundColor = '#3c3';
        } else {
            strengthBar.style.backgroundColor = '#eee';
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?> 