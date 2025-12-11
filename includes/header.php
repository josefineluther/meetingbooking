<?php

$username = null;

if (isset($_COOKIE['userid'])) {
  $userid = $_COOKIE['userid'];

  $usersJson = file_get_contents(__DIR__ . '/../data/users.json');
  $users = json_decode($usersJson, true) ?: [];

  foreach ($users as $user) {
    if ($user['id'] == $userid) {
      $username = $user['username'];
      break;
    }
  }
}

?>

<!doctype html>
<html lang="sv">
<meta charset="utf-8">

<head>
  <title><?= $pageTitle ?? 'Saknar rubrik' ?></title>
  <link rel="stylesheet" href="/Meetingbooking/assets/css/style.css">
</head>

<body>
  <header class="app-header" role="banner">
    <div class="app-header__bar">
      <a class="app-header__title" href="/Meetingbooking/dashboard.php">
        Mötesbokning
      </a>

      <?php if (isset($_COOKIE['userid'])): ?>
        <nav class="app-header__nav" aria-label="Huvudnavigation">
          <a class="app-header__link" href="/Meetingbooking/dashboard.php">Startsida</a>
          <a class="app-header__link" href="/Meetingbooking/meetingrooms.php">Mötesrum</a>
          <a class="app-header__link" href="/Meetingbooking/users.php">Användare</a>
        </nav>

        <div class="logout">
          <?php if ($username): ?>
            <p class="logged-in">Inloggad som <?= $username ?></p>
          <?php endif; ?>
          <a class="app-header__link app-header__link--logout" href="/Meetingbooking/logout.php">Logga ut</a>
        </div>

      <?php endif; ?>
    </div>
  </header>

  <main>
    <h1><?= $pageHeading ?? 'Saknar rubrik' ?></h1>
