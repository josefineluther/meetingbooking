<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/classes/MeetingroomManager.php';

$mm = new MeetingroomManager(__DIR__ . '/data/meetingrooms.json');

$name = trim($_POST['name'] ?? '');
$capacity = (int)($_POST['capacity']) ?? 0;
$tv = isset($_POST['tv']) && $_POST['tv'] == '1';
$audio = isset($_POST['audio']) && $_POST['audio'] == '1';

if ($name) {
    $meetingroom = new Meetingroom($mm->nextId(), $name, $capacity, $tv, $audio);
    $mm->add($meetingroom);
}

$_SESSION['success'] = "Mötesrummet har lagts till!";
header('Location: /Meetingbooking/meetingrooms.php');
exit;
