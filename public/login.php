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
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header('Location: /');
            exit();
        } else {
            $error = 'Felaktigt användarnamn eller lösenord';
        }
    } else {
        $error = 'Vänligen fyll i alla fält';
    }
}

require_once '../includes/header.php';
?>

<h1>Logga in</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
<?php endif; ?>

<form method="POST" action="/login.php">
    <div class="form-group">
        <label for="username">Användarnamn:</label>
        <input type="text" id="username" name="username" required>
    </div>

    <div class="form-group">
        <label for="password">Lösenord:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" class="btn">Logga in</button>
</form>

<p>Har du inget konto? <a href="/register.php">Registrera dig här</a></p>

<?php require_once '../includes/footer.php'; ?> 