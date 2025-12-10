<?php
session_start();
$pageTitle   = 'Användare';
$pageHeading = 'Användare';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/classes/UserManager.php';
require_once __DIR__ . '/classes/BookingManager.php';
require_once __DIR__ . '/classes/User.php';

$um = new UserManager(__DIR__ . '/data/users.json');
$bm = new BookingManager(__DIR__ . '/data/booked.json');

$user = $_COOKIE['userid'] ?? 0;

if (!$user) {
    header("Location: /Meetingbooking/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password && !$um->findByUsername($username)) {
        $user = new User($um->nextId(), $username, password_hash($password, PASSWORD_DEFAULT));
        $um->add($user);
    }

    $_SESSION['success'] = "Användaren har lagts till!";
    header('Location: /Meetingbooking/users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'pwd') {
    $id = (int)($_POST['id'] ?? 0);
    $password = $_POST['password'] ?? '';
    if ($id > 0 && $password) {
        $um->updatePassword($id, $password);
    }

    $_SESSION['success'] = "Lösenordet har ändrats!";
    header('Location: /Meetingbooking/users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'del') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $um->delete($id);
        $bm->deleteUser($id);
    }

    $_SESSION['success'] = "Användaren har raderats";
    header('Location: /Meetingbooking/users.php');
    exit;
}

$users = $um->all();

$editId = $_GET['edit'] ?? null;
?>

<p>Här kan du lägga till, ta bort eller redigera användare</p>

<div style="display: grid; gap: 100px; grid-template-columns: 1fr 1fr; padding: 50px;">
    <div>
        <h2>Lägg till användare</h2>
        <form method="post" style="display: flex; flex-direction: column;">
            <input type="hidden" name="action" value="add">
            <h3>Användaramn</h3>
            <input name="username" required>
            <h3>Lösenord</h3>
            <input name="password" required>
            <button class="add-button" style="margin-top: 2em;" type="submit">Skapa användare</button>
        </form>
    </div>

    <div>
        <h2>Användare</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="padding: 10px; background: #efe; border: 1px solid #9f9; border-radius: 6px;margin-bottom: 20px;">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php foreach ($users as $u): ?>
            <?php if ($editId && $editId == $u['id']): ?>
                <div id="<?= $u['id'] ?>" class="content-card">
                    <form method="post" style="display: flex; flex-direction: column;">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input type="hidden" name="action" value="pwd">
                        <h3>Välj nytt lösenord för <?= $u['username'] ?></h3>
                        <input name="password">
                        <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                        <button type="submit">Spara lösenord</button>
                        <a href="<?= '/Meetingbooking/users.php#' . $u['id'] ?>">Avbryt</a>
                    </form>
                </div>
            <?php else : ?>
                <div id="<?= $u['id'] ?>" class="content-card">
                    <h3><?= $u['username'] ?></h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <form action="edit_scroll_to_id.php" method="post">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="location" value="users.php">
                            <input type="hidden" name="edit_id" value="<?= $u['id'] ?>">
                            <button type="submit">Ändra lösenord</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Ta bort användare?');">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="action" value="del">
                            <button type="submit">Ta bort</button>
                        </form>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
