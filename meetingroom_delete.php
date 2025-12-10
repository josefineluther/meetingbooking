<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/classes/MeetingroomManager.php';

$mm = new MeetingroomManager(__DIR__ . '/data/meetingrooms.json');

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $mm->delete($id);
}

$redirect = $_POST['redirect'] ?? '/Meetingbooking/meetingrooms.php';

$_SESSION['success'] = "Mötesrummet har tagits bort";
header("Location: $redirect");
exit;
