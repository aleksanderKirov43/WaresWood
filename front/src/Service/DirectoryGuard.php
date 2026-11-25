<?php

declare(strict_types=1);

namespace WaresWood\Service;

use WaresWood\Exception\StorageException;

/**
 * Создание рабочих каталогов вместе с защитными .htaccess.
 *
 * Каталоги data/ и uploads/ не входят в деплой, поэтому их не получится
 * подготовить заранее в репозитории — приложение создаёт их само при первой записи.
 */
final class DirectoryGuard implements DirectoryGuardInterface
{
    /**
     * Полный запрет доступа. Директивы обёрнуты в IfModule, чтобы на Apache 2.2
     * не получить 500-ю ошибку из-за отсутствия mod_authz_core.
     */
    private const DENY_ALL = <<<'HTACCESS'
        <IfModule mod_authz_core.c>
            Require all denied
        </IfModule>
        <IfModule !mod_authz_core.c>
            Order allow,deny
            Deny from all
        </IfModule>
        HTACCESS;

    /**
     * Каталог с загрузками должен отдавать картинки, но никогда не выполнять код.
     * php_flag сознательно не используется: при работе PHP через FPM он приводит к 500.
     */
    private const DENY_SCRIPTS = <<<'HTACCESS'
        <FilesMatch "(?i)\.(php|phar|phtml|php[0-9]|pl|py|cgi|sh|shtml|asp|aspx|htaccess)$">
            <IfModule mod_authz_core.c>
                Require all denied
            </IfModule>
            <IfModule !mod_authz_core.c>
                Order allow,deny
                Deny from all
            </IfModule>
        </FilesMatch>

        <IfModule mod_mime.c>
            RemoveHandler .php .phar .phtml .php3 .php4 .php5 .php7 .php8 .cgi .pl .py
            RemoveType .php .phar .phtml
        </IfModule>

        <IfModule mod_rewrite.c>
            RewriteEngine Off
        </IfModule>

        Options -ExecCGI -Indexes
        HTACCESS;

    public function ensureProtectedDirectory(string $path): void
    {
        $this->createDirectory($path);
        $this->writeHtaccess($path, self::DENY_ALL);
    }

    public function ensurePublicUploadDirectory(string $path): void
    {
        $this->createDirectory($path);
        $this->writeHtaccess($path, self::DENY_SCRIPTS);
    }

    private function createDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0755, true) && !is_dir($path)) {
            throw new StorageException(sprintf('Не удалось создать каталог «%s»', $path));
        }
    }

    private function writeHtaccess(string $directory, string $contents): void
    {
        $file = $directory . '/.htaccess';

        if (is_file($file)) {
            return;
        }

        // Отсутствие .htaccess не должно блокировать работу админки, поэтому
        // ошибка записи только логируется: на nginx-хостингах файл всё равно не нужен.
        if (@file_put_contents($file, $contents . "\n") === false) {
            error_log(sprintf('WaresWood: не удалось создать защитный .htaccess в «%s»', $directory));
        }
    }
}
