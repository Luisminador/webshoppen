<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';

initSession();

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ogiltig e-postadress";
    }
    if (empty($password)) {
        $errors[] = "Lösenord krävs";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_firstname'] = $user['firstname'];
                    
                    setFlashMessage("Välkommen tillbaka!", 'success');
                    
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    
                    header('Location: ' . BASE_URL . '/profile.php');
                    exit();
                } else {
                    $errors[] = "Felaktig e-post eller lösenord";
                }
            } else {
                $errors[] = "Felaktig e-post eller lösenord";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = "Ett fel uppstod vid inloggning. Vänligen försök igen.";
        }
    }
}

ob_start();

require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h1 class="text-center">Logga in</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">E-post</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Lösenord</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary">Logga in</button>
        </form>

        <div class="auth-links">
            <p>Har du inget konto? <a href="register.php">Registrera dig här</a></p>
        </div>
    </div>
</div>

<style>
.auth-container {
    max-width: 400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.auth-box {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.text-center {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
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

.btn-primary {
    background: #000;
    color: #fff;
    padding: 0.875rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 1rem;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background: #333;
}

.auth-links {
    margin-top: 2rem;
    text-align: center;
    color: #666;
}

.auth-links a {
    color: #000;
    text-decoration: none;
    font-weight: 500;
}

.auth-links a:hover {
    text-decoration: underline;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
}

.alert-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
</style>

<?php 
require_once '../includes/footer.php';
if (ob_get_level()) {
    ob_end_flush();
}
?> 