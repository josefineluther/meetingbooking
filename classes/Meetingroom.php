<?php
class Meetingroom
{
    public function __construct(
        public int $id,
        public string $name,
        public int $capacity,
        public bool $tv,
        public bool $audio
    ) {}
}
