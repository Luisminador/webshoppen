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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    $city = trim($_POST['city'] ?? '');

    // Validering
    if (!$email || !$password || !$confirm_password || !$firstname || !$lastname) {
        $error = 'Vänligen fyll i alla obligatoriska fält';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Vänligen ange en giltig e-postadress';
    } elseif ($password !== $confirm_password) {
        $error = 'Lösenorden matchar inte';
    } elseif (strlen($password) < 6) {
        $error = 'Lösenordet måste vara minst 6 tecken långt';
    } else {
        // Kolla om e-postadressen redan finns
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'E-postadressen är redan registrerad';
        } else {
            // Skapa användaren
            $stmt = $pdo->prepare('
                INSERT INTO users (email, password, firstname, lastname, phone, address, postcode, city)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            
            try {
                $stmt->execute([
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $firstname,
                    $lastname,
                    $phone ?: null,
                    $address ?: null,
                    $postcode ?: null,
                    $city ?: null
                ]);
                
                setFlashMessage('Ditt konto har skapats! Du kan nu logga in.', 'success');
                header('Location: /webshoppen/public/login.php');
                exit();
            } catch (PDOException $e) {
                $error = 'Ett fel uppstod. Försök igen senare.';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="auth-form">
        <h1>Skapa konto</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/webshoppen/public/register.php" class="form">
            <div class="form-group">
                <label for="email">E-postadress: *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>"
                       autocomplete="email"
                       placeholder="din@email.com">
            </div>

            <div class="form-group">
                <label for="password">Lösenord: *</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       autocomplete="new-password"
                       placeholder="Minst 6 tecken">
            </div>

            <div class="form-group">
                <label for="confirm_password">Bekräfta lösenord: *</label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       required
                       autocomplete="new-password"
                       placeholder="Upprepa lösenord">
            </div>

            <div class="form-group">
                <label for="firstname">Förnamn: *</label>
                <input type="text" 
                       id="firstname" 
                       name="firstname" 
                       required
                       value="<?= isset($_POST['firstname']) ? sanitize($_POST['firstname']) : '' ?>"
                       placeholder="Förnamn">
            </div>

            <div class="form-group">
                <label for="lastname">Efternamn: *</label>
                <input type="text" 
                       id="lastname" 
                       name="lastname" 
                       required
                       value="<?= isset($_POST['lastname']) ? sanitize($_POST['lastname']) : '' ?>"
                       placeholder="Efternamn">
            </div>

            <div class="form-group">
                <label for="phone">Telefon:</label>
                <input type="tel" 
                       id="phone" 
                       name="phone"
                       value="<?= isset($_POST['phone']) ? sanitize($_POST['phone']) : '' ?>"
                       placeholder="Valfritt">
            </div>

            <div class="form-group">
                <label for="address">Gatuadress:</label>
                <input type="text" 
                       id="address" 
                       name="address"
                       value="<?= isset($_POST['address']) ? sanitize($_POST['address']) : '' ?>"
                       placeholder="Valfritt">
            </div>

            <div class="form-group">
                <label for="postcode">Postnummer:</label>
                <input type="text" 
                       id="postcode" 
                       name="postcode"
                       value="<?= isset($_POST['postcode']) ? sanitize($_POST['postcode']) : '' ?>"
                       placeholder="Valfritt">
            </div>

            <div class="form-group">
                <label for="city">Stad:</label>
                <input type="text" 
                       id="city" 
                       name="city"
                       value="<?= isset($_POST['city']) ? sanitize($_POST['city']) : '' ?>"
                       placeholder="Valfritt">
            </div>

            <button type="submit" class="btn btn-primary">Skapa konto</button>
        </form>

        <div class="auth-links">
            <p>Har du redan ett konto? <a href="/webshoppen/public/login.php">Logga in här</a></p>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.auth-form {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.auth-form h1 {
    margin-bottom: 1.5rem;
    text-align: center;
    color: #333;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #333;
}

.form-group input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: #666;
}

.btn-primary {
    background: #000;
    color: white;
    padding: 0.75rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
    margin-top: 1rem;
}

.btn-primary:hover {
    background: #333;
}

.auth-links {
    margin-top: 1.5rem;
    text-align: center;
}

.auth-links a {
    color: #000;
    text-decoration: underline;
}

.alert {
    padding: 0.75rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-error {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #dc2626;
}
</style>

<?php require_once '../includes/footer.php'; ?> 