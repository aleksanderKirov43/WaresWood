<?php

declare(strict_types=1);

namespace WaresWood\Admin;

use WaresWood\App\Paths;
use WaresWood\App\View;

/**
 * Помощник первичной настройки, работающий, пока нет файла конфигурации.
 *
 * Файл сознательно не создаётся автоматически: иначе в окне между деплоем и
 * первым входом пароль администратора мог бы задать кто угодно. Помощник только
 * считает хеш пароля и показывает готовое содержимое файла для загрузки по FTP.
 */
final class SetupController
{
    private const MIN_PASSWORD_LENGTH = 12;

    public function __construct(
        private readonly View $view,
        private readonly Paths $paths,
        private readonly Responder $responder,
    ) {
    }

    public function handle(Request $request): void
    {
        $this->responder->sendSecurityHeaders();

        $login = '';
        $error = null;
        $generatedConfig = null;

        if ($request->isPost() && $request->action() === 'setup') {
            $login = $request->string('login');
            $password = $request->string('password');
            $error = $this->validate($login, $password);

            if ($error === null) {
                $generatedConfig = $this->buildConfigFile($login, $password);
            }
        }

        $this->view->renderPage('setup', 'Первичная настройка', [
            'login' => $login,
            'error' => $error,
            'generatedConfig' => $generatedConfig,
            'configFile' => $this->paths->configFile,
            'authenticated' => false,
        ]);
    }

    private function validate(string $login, string $password): ?string
    {
        if ($login === '') {
            return 'Укажите логин.';
        }

        if (mb_strlen($password, 'UTF-8') < self::MIN_PASSWORD_LENGTH) {
            return sprintf('Пароль должен быть не короче %d символов.', self::MIN_PASSWORD_LENGTH);
        }

        return null;
    }

    private function buildConfigFile(string $login, string $password): string
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        return sprintf(
            "<?php\n\ndeclare(strict_types=1);\n\n"
            . "// Конфигурация администратора. Файл не входит в git и не перезаливается деплоем.\n"
            . "return [\n    'admin' => [\n        'login' => %s,\n        'password_hash' => %s,\n    ],\n];\n",
            var_export($login, true),
            var_export($hash, true),
        );
    }
}
