<?php
class Booking
{
    public function __construct(
        public int $id,
        public int $room,
        public int $user,
        public string $date,
        public string $time
    ) {}
}
