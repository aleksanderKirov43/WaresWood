<?php

declare(strict_types=1);

/**
 * Точка входа админки каталога.
 *
 * Корнем сайта считается каталог на два уровня выше: на сервере это public_html,
 * локально — корень репозитория. Относительно него лежат data/ и uploads/.
 */

require_once dirname(__DIR__) . '/src/autoload.php';

use WaresWood\Admin\AdminController;
use WaresWood\App\Container;

$container = new Container(dirname(__DIR__, 2));

(new AdminController($container, $container->request(), $container->responder()))->handle();
