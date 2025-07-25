<?php

date_default_timezone_set('Europe/Paris');

require_once './resources/utils/utils.php';
require_once './resources/utils/curl.php';

$assetsDir = __DIR__ . '/resources/assets';
if (!is_dir($assetsDir))
    mkdir($assetsDir, true);

$tailwind = "$assetsDir/tailwind.js";
if (!is_file($tailwind)) {
    stdlog("Downloading tailwind.js");
    file_put_contents($tailwind, file_get_contents('https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4'));
}

$favicon = cache('favicon', function(){
    return "data:image/png;base64," . base64_encode(file_get_contents('../../img/utilux-64.png'));
}, 3600);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= $favicon ?>">
    <title>Dashboard</title>
</head>

<body>
    <style>
        :root {
            font-size: 14px;
        }

        .page-background-overlay,
        .page-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
        }

        .page-background-overlay {
            z-index: 1;
            filter: brightness(75%);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: 50% 50%;
        }

        .page-container {
            z-index: 2;
            max-height: 100vh;
            overflow: hidden;
        }

        .work-container {
            overflow: auto;
        }

        .work-container:not(:has(*)) {
            display: none;
        }

        body {
            color: white;
        }

        .card {
            border: solid 1px rgba(125, 125, 125, 40%);
            background-color: rgba(25, 25, 25, 33%);
            backdrop-filter: blur(4px);
            padding: 1em;
            border-radius: 4px;
            transition: all 100ms ease-in-out;
        }

        .card:hover {
            background-color: rgba(150, 150, 150, 33%);
            backdrop-filter: blur(8px);
        }

        .slot:empty {
            display: none;
        }
    </style>
    <script src="/resources/assets/tailwind.js"></script>
    <div class="page-background-overlay"
        style="
        <?php if ($background = config('style')['background'] ?? false) {
            if (str_starts_with($background, "#"))
                echo "background: $background";
            else if (is_file($background))
                echo "background-image: url(/resources/actions/get-background.php)";
        } ?>
    "></div>
    <div class="<?= config('style')['page-content-style'] ?? '' ?> p-5 gap-6 page-container">
        <?php
        foreach (config('blocks') as $service) {
            if (!service($service)['enabled'])
                continue;
        ?>
            <div class="flex flex-col flex-grow-1 work-container">
                <?php require service($service)['view'] ?>
            </div>
        <?php } ?>
    </div>

    <script>
        setTimeout(() => location.reload(), 60 * 1000 * 6)

        document.querySelectorAll('script,style').forEach(x => {
            document.body.appendChild(x);
        })

        const randomElement = (arr) => arr[Math.round(Math.random() * (arr.length - 1))];
    </script>
    <?php if (!config('style')['background'] ?? false) { ?>
        <script>
            let background = document.querySelector('.page-background-overlay');
            let type = randomElement(['linear-gradient', 'radial-gradient'])

            let hue = Math.round(Math.random() * 360);
            let startColor = `hsl(${hue}, 50%, 50%)`
            let endColor = `hsl(${hue}, ${25 + Math.round(Math.random()*50)}%, ${25 + Math.round(Math.random()*50)}%)`

            let direction = Math.round(Math.random() * 360) + "deg";

            let gradient = type === 'radial-gradient' ?
                `${type}(${startColor}, ${endColor})` :
                `${type}(${direction}, ${startColor}, ${endColor})`

            background.style.background = gradient
        </script>
    <?php } ?>

</body>

</html>