<?php
use DI\Container;
use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathDetector;
use Slim\Exception\HttpNotFoundException;

require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set("Asia/Jakarta");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$basePathDetector = new BasePathDetector($_SERVER);
$app->setBasePath($basePathDetector->getBasePath());

// Set up database connection
require_once __DIR__ . '/../app/config/database.php';

// Set up dependencies
require_once __DIR__ . '/../app/config/dependencies.php';

require_once __DIR__ . '/../app/routes/config.php';

require_once __DIR__ . '/../app/helper/common.php';

$app->run();