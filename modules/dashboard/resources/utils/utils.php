<?php

function stdlog(string $string)
{
    file_put_contents('php://stdout', $string . "\n");
}

function cache(string $key, callable $valueGetter, int $timeToLive = 300)
{
    $cacheDir = __DIR__ . '/../../cache';
    if (!is_dir($cacheDir))
        mkdir($cacheDir);

    $file = $cacheDir . '/' . $key . '.ser';

    if (is_file($file)) {
        $content = json_decode(file_get_contents($file), true);
        $expireTime = $content['expires_at'];
        if (time() < $expireTime)
            return unserialize($content['content']);

        unlink($file);
    }

    $value = ($valueGetter)();
    file_put_contents($file, json_encode([
        'expires_at' => time() + $timeToLive,
        'created_at' => time(),
        'content' => serialize($value)
    ]));
    return $value;
}

function getCacheTimestamps(string $key)
{
    $cacheDir = __DIR__ . '/../../cache';
    if (!is_dir($cacheDir))
        mkdir($cacheDir);

    $file = $cacheDir . '/' . $key . '.ser';

    if (!is_file($file))
        return null;

    $content = json_decode(file_get_contents($file), true);

    $createdAt = date('H:i', $content['created_at']);
    $expiresAt = date('H:i', $content['expires_at']);

    echo "<div class='flex flex-col'>
            <small>LU : $createdAt </small>
            <small>NU : $expiresAt </small>
        </div>";
}

function clearCacheButton(string $cacheKey)
{
    return '
    <a href="/resources/actions/clear-cache.php?key=' . $cacheKey . '">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
        </svg>
    </a>
    ';
}

function config(?string $key = null)
{
    $config = $GLOBALS['dash_config'] ??= json_decode(file_get_contents(__DIR__ . '/../../config.json'), true, 512, JSON_THROW_ON_ERROR);
    return $key ? $config[$key] : $config;
}

function service(string $service)
{
    return config()['services'][$service];
}

function dd($object)
{
    header('content-type: application/json');
    echo json_encode($object);
    die;
}

function command(string $command, ?string $directory = null)
{
    $directory ??= getcwd();
    $original = getcwd();

    chdir($directory);
    $output = shell_exec($command);
    chdir($original);

    return $output;
}


function env(string $name, ?string $default=null): ?string
{
    if ($value = getenv($name))
        return $value;

    return $default;
}