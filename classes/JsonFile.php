
<?php
class JsonFile {
    public function __construct(private string $path) {}

    public function read(): array {
        if (!file_exists($this->path)) return [];
        $json = file_get_contents($this->path);
        if ($json === false) throw new RuntimeException("Kunde inte läsa {$this->path}");
        $data = json_decode($json, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Ogiltig JSON i {$this->path}: " . json_last_error_msg());
        }
        return $data;
    }

    public function write(array $data): void {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) throw new RuntimeException("Kunde inte koda JSON för {$this->path}");
        if (file_put_contents($this->path, $json, LOCK_EX) === false) {
            throw new RuntimeException("Kunde inte skriva till {$this->path}");
        }
    }
}
