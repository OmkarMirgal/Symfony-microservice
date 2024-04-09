<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Dynamically set environment variables based on Docker container configuration
$mysqlContainerInfo = `docker inspect --format='{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}:{{index .NetworkSettings.Ports "3306/tcp" 0}}' promotions-engine-docker_db-1`;
list(,,$Address,$Port) = explode(':', trim($mysqlContainerInfo));

$mysqlIpAddress = explode(' ',trim($Address));
$mysqlPort = explode(']',trim($Port));

putenv("DOCKER_DB_URL=mysql://root:password@127.0.0.1:$mysqlPort[0]/main?sslmode=disable&charset=utf8mb4");


if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}