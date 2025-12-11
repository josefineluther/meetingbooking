<?php
session_start();

$pageTitle = 'Mötesrum';
$pageHeading = 'Mötesrum';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/MeetingRoomManager.php';

$mm = new MeetingroomManager(__DIR__ . '/data/meetingrooms.json');

$meetingrooms = $mm->all();

$allTimes = ['08:00-10:00', '10:00-12:00', '12:00-14:00', '14:00-16:00'];

$user = $_COOKIE['userid'] ?? 0;

if (!$user) {
    header("Location: /Meetingbooking/index.php");
    exit;
}

$editId = $_GET['edit'] ?? null;
$bookingId = $_GET['booking'] ?? null;

if (isset($_SESSION['error'])) unset($_SESSION['error']);
if (isset($_SESSION['success'])) unset($_SESSION['success']);
?>

<p>Här kan du boka, lägga till, ta bort eller ändra mötesrum</p>

<div style="display: grid; gap: 100px; grid-template-columns: 1fr 1fr; padding: 30px;">
    <div>
        <h2>Lägg till rum</h2>
        <form action="meetingroom_add.php" method="post" style="display: flex; flex-direction: column;">
            <h3>Namn</h3>
            <input name="name">
            <h3>Antal platser</h3>
            <input type="number" name="capacity">
            <h3 class="facilities">Faciliteter</h3>
            <input id="tv2" type="checkbox" name="tv" value="1">
            <label for="tv2">TV finns</label>

            <input id="audio2" type="checkbox" name="audio" value="1">
            <label for="audio2">Ljud finns</label>
            <button class="add-button">Lägg till</button>
        </form>
    </div>

    <div>
        <h2>Mötesrum</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?= $_SESSION['error'] ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success">
                <?= $_SESSION['success'] ?>
            </div>
        <?php endif; ?>

        <?php foreach ($meetingrooms as $m): ?>
            <?php if ($editId && $editId == $m['id']): ?>
                <div id="<?= $m['id'] ?>" class="content-card">
                    <form action="meetingroom_update.php" method="post" style="display: flex; flex-direction: column;">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">

                        <h3>Namn</h3>
                        <input name="name" value="<?= $m['name'] ?>">

                        <h3>Antal platser</h3>
                        <input type="number" name="capacity" value="<?= $m['capacity'] ?>">

                        <h3 class="facilities">Faciliteter</h3>

                        <input type="checkbox" id="tv3" name="tv" value="1" <?= $m['tv'] ? 'checked' : '' ?>>
                        <label for="tv3">TV finns</label>

                        <input type="checkbox" id="audio3" name="audio" value="1" <?= $m['audio'] ? 'checked' : '' ?>>
                        <label for="audio3">Ljud finns</label>

                        <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                        <button type="submit">Spara ändringar</button>
                        <a href="<?= '/Meetingbooking/meetingrooms.php#' . $m['id'] ?>">Avbryt</a>
                    </form>
                </div>

            <?php elseif ($bookingId && $bookingId == $m['id']): ?>
                <div id="<?= $m['id'] ?>" class="content-card">
                    <h3><?= $m['name'] ?></h3>
                    <p>Antal platser: <?= $m['capacity'] ?></p>
                    <p>TV: <?= $m['tv'] ? 'Ja' : 'Nej' ?> | Ljud: <?= $m['audio'] ? 'Ja' : 'Nej' ?></p>
                    <form action="booking_add.php" method="post">
                        <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                        <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                            <label for="date">Välj dag:</label>
                            <input type="date" id="date" name="date" required min="<?= date('Y-m-d'); ?>">
                            <label for="time">Välj tid:</label>
                            <select name="time" id="time">
                                <?php foreach ($allTimes as $t): ?>
                                    <option value="<?= $t ?>"><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="room" value="<?= $m['id'] ?>">
                        <button type="submit">Boka rum</button>
                    </form>
                    <a href="<?= '/Meetingbooking/meetingrooms.php#' . $m['id'] ?>">Avbryt</a>
                </div>

            <?php else: ?>
                <div id="<?= $m['id'] ?>" class="content-card">
                    <h3><?= $m['name'] ?></h3>
                    <p>Antal platser: <?= $m['capacity'] ?></p>
                    <p>TV: <?= $m['tv'] ? 'Ja' : 'Nej' ?> | Ljud: <?= $m['audio'] ? 'Ja' : 'Nej' ?></p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">

                        <form action="booking_scroll_to_id.php" method="post">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <input type="hidden" name="location" value="meetingrooms.php">
                            <input type="hidden" name="booking_id" value="<?= $m['id'] ?>">
                            <button type="submit">Boka</button>
                        </form>

                        <form action="edit_scroll_to_id.php" method="post">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <input type="hidden" name="location" value="meetingrooms.php">
                            <input type="hidden" name="edit_id" value="<?= $m['id'] ?>">
                            <button type="submit">Redigera</button>
                        </form>

                        <form action="meetingroom_delete.php" method="post" onsubmit="return confirm('Vill du ta bort detta mötesrum?')">
                            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <button type="submit">Ta bort</button>
                        </form>
                    </div>
                </div>

            <?php endif; ?>

        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
