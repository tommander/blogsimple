<?php

// Not Implemented
exit(1);


declare(strict_types=1);

use Tommander\BlogSimple\Main;

error_reporting(E_ALL);

$startTimestamp = hrtime(true);

$fileAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($fileAutoload)) {
    ?>
    <h1>Internal App Error</h1>
    <p>Autoload file <q><?= htmlspecialchars(is_string($fileAutoload) ? $fileAutoload : '') ?></q> does not exist.</p>
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
if (!isset($m) || !($m instanceof Main)) {
    ?>
    <h1>Internal App Error</h1>
    <p>Class <q>Main</q> cannot be instantiated.</p>
    <?php
    exit(1);
}

$token = preg_match('/^[[A-Fa-f0-9]{}$/', '', $_POST['action'] ?? '');
$action = preg_replace('/[^[A-Za-z0-9_-]/', '', $_POST['action'] ?? '');
