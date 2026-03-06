<?php

declare(strict_types=1);

use Tommander\BlogSimple\Main;

error_reporting(E_ALL);

/** @psalm-suppress UnusedVariable */
$startTimestamp = hrtime(true);

$fileAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($fileAutoload)) {
    ?>
    <h1>Internal App Error</h1>
    <p>Autoload file <q><?= htmlspecialchars($fileAutoload) ?></q> does not exist.</p>
    <?php
    exit(1);
}

$fileHeader = __DIR__ . '/../system/partials/header.php';
if (!file_exists($fileHeader)) {
    ?>
    <h1>Internal App Error</h1>
    <p>Header file <q><?= htmlspecialchars($fileHeader) ?></q> does not exist.</p>
    <?php
    exit(1);
}

$fileFooter = __DIR__ . '/../system/partials/footer.php';
if (!file_exists($fileFooter)) {
    ?>
    <h1>Internal App Error</h1>
    <p>Footer file <q><?= htmlspecialchars($fileFooter) ?></q> does not exist.</p>
    <?php
    exit(1);
}

require $fileAutoload;
if (!class_exists('Tommander\BlogSimple\Main')) {
    ?>
    <h1>Internal App Error</h1>
    <p>Class <q>Main</q> is not autoloaded.</p>
    <?php
    exit(1);
}

$m = new Main();

include $fileHeader;
$m->render();
include $fileFooter;
