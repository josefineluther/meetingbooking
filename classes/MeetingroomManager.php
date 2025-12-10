
<?php
require_once __DIR__ . '/JsonFile.php';
require_once __DIR__ . '/Meetingroom.php';
require_once __DIR__ . '/BookingManager.php';

class MeetingroomManager
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

    public function add(Meetingroom $meetingroom): void
    {
        $meetingrooms = $this->all();
        $meetingrooms[] = [
            'id' => $meetingroom->id,
            'name' => $meetingroom->name,
            'capacity' => $meetingroom->capacity,
            'tv' => $meetingroom->tv,
            'audio' => $meetingroom->audio
        ];
        $this->store->write($meetingrooms);
    }

    public function delete(int $id): void
    {
        $meetingrooms = $this->all();

        $meetingrooms = array_filter($meetingrooms, fn($m) => $m['id'] !== $id);

        $this->store->write(array_values($meetingrooms));

        $bm = new BookingManager(__DIR__ . '/../data/booked.json');
        $bm->removeBookingsByRoom($id);
    }

    public function updateMeetingroom(int $id, string $name, int $capacity, bool $audio, bool $tv): void
    {
        $meetingrooms = $this->all();
        foreach ($meetingrooms as &$m) {
            if ($m['id'] === $id) {
                $m['name'] = $name;
                $m['capacity'] = $capacity;
                $m['audio'] = $audio;
                $m['tv'] = $tv;
                break;
            }
        }
        $this->store->write($meetingrooms);
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
