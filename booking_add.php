<?php
session_start();

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/classes/BookingManager.php';

$bm = new BookingManager(__DIR__ . '/data/booked.json');

$bookings = $bm->all();
$room = (int)($_POST['room'] ?? 0);
$user = $_COOKIE['userid'] ?? 0;
$date = trim($_POST['date']);
$time = trim($_POST['time']);

$alreadyBooked = false;

$redirect = $_POST['redirect'] ?? '/Meetingbooking/meetingrooms.php';
$redirect = strtok($_POST['redirect'], '?');

foreach ($bookings as $b) {
    if ($b['room'] === $room && $b['date'] === $date && $b['time'] === $time) {
        $_SESSION['error'] = "Rummet är redan bokat den tiden. Testa med en annan tid!";
        header("Location: $redirect");
        exit;
    }
}

$bm->addBooking($room, $user, $date, $time);

$_SESSION['success'] = "Bokningen är klar!";
header("Location: $redirect");
exit;
