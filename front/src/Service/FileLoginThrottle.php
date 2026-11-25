<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\AuthException;

/**
 * Файловый счётчик неудачных попыток входа.
 *
 * Ключи хранятся в виде хеша, чтобы в файле на диске не оседали IP-адреса.
 * Просроченные записи вычищаются при каждой записи, поэтому файл не растёт.
 */
final class FileLoginThrottle implements LoginThrottleInterface
{
    private const MAX_ATTEMPTS = 5;

    private const WINDOW_SECONDS = 900;

    private const LOCK_SECONDS = 900;

    public function __construct(
        private readonly string $storageFile,
        private readonly DirectoryGuardInterface $directoryGuard,
    ) {
    }

    public function assertNotLocked(string $key): void
    {
        $entry = $this->read()[$this->hash($key)] ?? null;

        if ($entry === null) {
            return;
        }

        $lockedUntil = (int) ($entry['locked_until'] ?? 0);

        if ($lockedUntil <= time()) {
            return;
        }

        throw new AuthException(sprintf(
            'Слишком много неудачных попыток входа. Повторите через %d мин.',
            (int) ceil(($lockedUntil - time()) / 60),
        ));
    }

    public function registerFailure(string $key): void
    {
        $entries = $this->read();
        $hash = $this->hash($key);
        $now = time();

        $entry = $entries[$hash] ?? null;
        $firstAttempt = (int) ($entry['first_attempt'] ?? 0);
        $attempts = (int) ($entry['attempts'] ?? 0);

        // Окно наблюдения истекло — начинаем отсчёт заново.
        if ($firstAttempt === 0 || $now - $firstAttempt > self::WINDOW_SECONDS) {
            $firstAttempt = $now;
            $attempts = 0;
        }

        ++$attempts;

        $entries[$hash] = [
            'first_attempt' => $firstAttempt,
            'attempts' => $attempts,
            'locked_until' => $attempts >= self::MAX_ATTEMPTS ? $now + self::LOCK_SECONDS : 0,
        ];

        $this->write($entries);
    }

    public function reset(string $key): void
    {
        $entries = $this->read();
        unset($entries[$this->hash($key)]);

        $this->write($entries);
    }

    /**
     * @return array<string, array<string, int>>
     */
    private function read(): array
    {
        if (!is_file($this->storageFile)) {
            return [];
        }

        $contents = @file_get_contents($this->storageFile);

        if ($contents === false || trim($contents) === '') {
            return [];
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, array<string, int>> $entries
     */
    private function write(array $entries): void
    {
        $this->directoryGuard->ensureProtectedDirectory(dirname($this->storageFile));

        $now = time();
        $entries = array_filter(
            $entries,
            static function (mixed $entry) use ($now): bool {
                if (!is_array($entry)) {
                    return false;
                }

                $lockedUntil = (int) ($entry['locked_until'] ?? 0);
                $firstAttempt = (int) ($entry['first_attempt'] ?? 0);

                return $lockedUntil > $now || $now - $firstAttempt <= self::WINDOW_SECONDS;
            },
        );

        $json = json_encode($entries, JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            return;
        }

        // Ошибка записи не должна ломать вход: троттлинг — вспомогательный механизм.
        @file_put_contents($this->storageFile, $json, LOCK_EX);
    }

    private function hash(string $key): string
    {
        return hash('sha256', $key);
    }
}
