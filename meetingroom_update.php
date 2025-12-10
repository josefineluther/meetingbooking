<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/classes/MeetingroomManager.php';

$mm = new MeetingroomManager(__DIR__ . '/data/meetingrooms.json');

$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$capacity = (int)($_POST['capacity']) ?? 0;
$audio = (bool)($_POST['audio'] ?? false);
$tv = (bool)($_POST['tv'] ?? false);

if ($id > 0) {
    $mm->updateMeetingroom($id, $name, $capacity, $audio, $tv);
}

$redirect = $_POST['redirect'] ?? '/Meetingbooking/meetingrooms.php';
$redirect = strtok($redirect, '?');

$_SESSION['success'] = "Mötesrummet har uppdaterats!";
header("Location: $redirect");
exit;
