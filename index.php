<?php
session_start();
$pageTitle = 'Logga in';
$pageHeading = 'Logga in';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/UserManager.php';
$um = new UserManager(__DIR__ . '/data/users.json');

if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['success'])) unset($_SESSION['success']);

$user = $_COOKIE['userid'] ?? 0;

if ($user) {
    header("Location: /Meetingbooking/dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = $um->findByUsername($username);
    if ($user && password_verify($password, $user['passwordHash'])) {
        setcookie('userid', (string)$user['id'], [
            'expires'  => time() + 3600 * 8,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        header('Location: /Meetingbooking/dashboard.php');
        exit;
    } else {
        $error = 'Felaktigt användarnamn eller lösenord';
    }
}
?>

<div class="login-box">
    <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form class="login-form" method="post">
        <label for="username">Användarnamn</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Lösenord</label>
        <input type="password" id="password" name="password" required>

        <button class="add-button" type="submit">Logga in</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php #echo password_hash('admin123', PASSWORD_DEFAULT);
?>
