<?php

$id = trim($_POST['id'] ?? '');
$editId = trim($_POST['edit_id' ?? '']);
$location = trim($_POST['location']);

header("Location: /Meetingbooking/$location?edit=$editId#$id");
exit;
