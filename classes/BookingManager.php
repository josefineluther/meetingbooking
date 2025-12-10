 <?php
    require_once __DIR__ . '/JsonFile.php';
    require_once __DIR__ . '/Booking.php';

    class BookingManager
    {
        private JsonFile $store;

        public function __construct(string $filePath)
        {
            $this->store = new JsonFile($filePath);
        }

        public function all(): array
        {
            return $this->store->read();
        }

        public function addBooking(int $room, int $user, string $date, string $time): void
        {
            $bookings = $this->all();

            $newBooking = [
                'id' => $this->nextId(),
                'room' => $room,
                'user' => $user,
                'date' => $date,
                'time' => $time
            ];

            $bookings[] = $newBooking;

            $this->store->write($bookings);
        }

        public function removeBookingsByRoom(int $room): void
        {
            $bookings = $this->all();
            $bookings = array_values(array_filter($bookings, fn($b) => $b['room'] !== $room));
            $this->store->write($bookings);
        }

        public function delete(int $id, int $user): void
        {
            $bookings = $this->all();
            $bookings = array_values(array_filter($bookings, fn($b) =>  !($b['id'] === $id && $b['user'] === $user)));
            $this->store->write($bookings);
        }

        public function nextId(): int
        {
            $max = 0;
            foreach ($this->all() as $m) {
                if ($m['id'] > $max) $max = $m['id'];
            }
            return $max + 1;
        }
    }
