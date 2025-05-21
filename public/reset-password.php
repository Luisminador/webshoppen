<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if ($email) {
        // Kontrollera om e-postadressen finns i databasen
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generera en unik återställningskod
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Spara token i databasen
            $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
            $stmt->execute([$reset_token, $reset_expires, $user['id']]);

            // Här skulle vi normalt skicka ett e-postmeddelande med återställningslänken
            // För demo-syfte visar vi bara ett framgångsmeddelande
            $success = 'Om e-postadressen finns i vårt system kommer du att få instruktioner för att återställa ditt lösenord.';
        } else {
            // För säkerhetens skull visar vi samma meddelande även om e-postadressen inte finns
            $success = 'Om e-postadressen finns i vårt system kommer du att få instruktioner för att återställa ditt lösenord.';
        }
    } else {
        $error = 'Vänligen ange din e-postadress';
    }
}

require_once '../includes/header.php';
?>

<link rel="stylesheet" href="/webshoppen/public/css/login.css">

<div class="login-container">
    <h1>Återställ lösenord</h1>
    
    <p class="intro">
        Ange din e-postadress nedan så skickar vi instruktioner för att återställa ditt lösenord.
    </p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= sanitize($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/webshoppen/public/reset-password.php">
        <div class="form-group">
            <label for="email">E-postadress</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   required
                   autocomplete="email"
                   placeholder="Ange din e-postadress">
        </div>

        <button type="submit" class="login-btn">Skicka instruktioner</button>
    </form>

    <p class="register-link">
        <a href="/webshoppen/public/login.php">Tillbaka till inloggning</a>
    </p>
</div>

<?php require_once '../includes/footer.php'; ?> 