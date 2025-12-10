<?php
session_start();

$username = null;

$user = $_COOKIE['userid'] ?? 0;

$usersJson = file_get_contents(__DIR__ . '/data/users.json');
$users = json_decode($usersJson, true) ?: [];

foreach ($users as $u) {
  if ($u['id'] == $user) {
    $username = $u['username'];
    break;
  }
}

$pageTitle = 'Mötesbokning';
$pageHeading = 'Välkommen ' . $username . '!';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/classes/MeetingroomManager.php';
require_once __DIR__ . '/classes/BookingManager.php';

$mm = new MeetingroomManager(__DIR__ . '/data/meetingrooms.json');
$bm = new BookingManager(__DIR__ . '/data/booked.json');

$meetingrooms = $mm->all();
$bookedRooms = $bm->all();

$user = $_COOKIE['userid'] ?? 0;

if (!$user) {
  header("Location: /Meetingbooking/index.php");
  exit;
}

$myBookings = array_filter($bookedRooms, fn($b) => $b['user'] == $user);

$roomsById = [];
foreach ($meetingrooms as $r) {
  $roomsById[$r['id']] = $r;
}

$allTimes = ['08:00-10:00', '10:00-12:00', '12:00-14:00', '14:00-16:00'];

$editId = $_GET['edit'] ?? null;
$bookingId = $_GET['booking'] ?? null;

?>

<div style="display: grid; gap: 100px; grid-template-columns: 1fr 1fr; padding: 50px;">
  <div>
    <h2>Mina bokningar</h2>
    <?php if (empty($myBookings)): ?>
      <p>Du har inga bokade rum</p>
    <?php endif; ?>
    <?php foreach ($myBookings as $b): ?>
      <?php
      $room = $roomsById[$b['room']] ?? null;
      if (!$room) continue;
      ?>
      <div class="content-card">
        <h3><?= htmlspecialchars($room['name']) ?></h3>
        <p>Antal platser: <?= htmlspecialchars($room['capacity'] ?? '') ?></p>
        <p>TV: <?= $room['tv'] ? 'Ja' : 'Nej' ?> | Ljud: <?= $room['audio'] ? 'Ja' : 'Nej' ?></p>
        <p><?= $b['date'] . ' | ' . $b['time'] ?></p>
        <form action="booking_delete.php" method="post" onsubmit="return confirm('Vill du avboka detta mötesrum?')">
          <input type="hidden" name="id" value="<?= $b['id'] ?>">
          <button type="submit">Avboka</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>

  <div>
    <h2>Mötesrum</h2>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="error">
        <?= $_SESSION['error'] ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="success">
        <?= $_SESSION['success'] ?>
      </div>
      <?php unset($_SESSION['success']); ?>
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

            <input type="checkbox" id="tv" name="tv" value="1" <?= $m['tv'] ? 'checked' : '' ?>>
            <label for="tv">TV finns</label>

            <input type="checkbox" id="audio" name="audio" value="1" <?= $m['audio'] ? 'checked' : '' ?>>
            <label for="audio">Ljud finns</label>

            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <button type="submit">Spara ändringar</button>
            <a href="<?= '/Meetingbooking/dashboard.php#' . $m['id'] ?>">Avbryt</a>
          </form>
        </div>

      <?php elseif ($bookingId && $bookingId == $m['id']): ?>
        <div id="<?= $m['id'] ?>" class="content-card">
          <h3><?= $m['name'] ?></h3>
          <p>Antal platser: <?= $m['capacity'] ?></p>
          <p>TV: <?= $m['tv'] ? 'Ja' : 'Nej' ?> | Ljud: <?= $m['audio'] ? 'Ja' : 'Nej' ?></p>
          <form action="booking_add.php" method="post">
            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
            <?php if (isset($_SESSION['error'])) unset($_SESSION['error']); ?>
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
          <a href="<?= '/Meetingbooking/dashboard.php#' . $m['id'] ?>">Avbryt</a>
        </div>

      <?php else: ?>
        <div id="<?= $m['id'] ?>" class="content-card">
          <h3><?= $m['name'] ?></h3>
          <p>Antal platser: <?= $m['capacity'] ?></p>
          <p>TV: <?= $m['tv'] ? 'Ja' : 'Nej' ?> | Ljud: <?= $m['audio'] ? 'Ja' : 'Nej' ?></p>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">

            <form action="booking_scroll_to_id.php" method="post">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <input type="hidden" name="location" value="dashboard.php">
              <input type="hidden" name="booking_id" value="<?= $m['id'] ?>">
              <button type="submit">Boka</button>
            </form>

            <form action="edit_scroll_to_id.php" method="post">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <input type="hidden" name="location" value="dashboard.php">
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
