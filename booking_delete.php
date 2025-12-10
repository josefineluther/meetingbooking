<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/classes/BookingManager.php';

$bm = new BookingManager(__DIR__ . '/data/booked.json');

$id = (int)($_POST['id'] ?? 0);
$user = $_COOKIE['userid'] ?? 0;

if ($id > 0 && $user > 0) {
  $bm->delete($id, $user);
}

$_SESSION['success'] = "Rummet är nu avbokat";
header('Location: /Meetingbooking/dashboard.php');
exit;
