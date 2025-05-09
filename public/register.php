<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    header('Location: /');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username && $email && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = 'Lösenorden matchar inte';
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
                    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    if ($stmt->execute([$username, $email, $hashed_password])) {
                        $_SESSION['user_id'] = $pdo->lastInsertId();
                        $_SESSION['username'] = $username;
                        
                        header('Location: /');
                        exit();
                    } else {
                        $error = 'Ett fel uppstod vid registreringen';
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

<h1>Registrera dig</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>

<form method="POST" action="/register.php">
    <div class="form-group">
        <label for="username">Användarnamn:</label>
        <input type="text" id="username" name="username" required>
    </div>

    <div class="form-group">
        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="password">Lösenord:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="confirm_password">Bekräfta lösenord:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>

    <button type="submit" class="btn">Registrera</button>
</form>

<p>Har du redan ett konto? <a href="/login.php">Logga in här</a></p>

<?php require_once '../includes/footer.php'; ?> 