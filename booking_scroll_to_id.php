<?php

$id = trim($_POST['id'] ?? '');
$bookingId = trim($_POST['booking_id' ?? '']);
$location = trim($_POST['location']);

header("Location: /Meetingbooking/$location?booking=$bookingId#$id");
exit;
