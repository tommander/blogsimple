<?php

declare(strict_types=1);

use Tommander\BlogSimple\Diagnostics;
use Tommander\BlogSimple\Main;

error_reporting(E_ALL);

$fileAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($fileAutoload)) {
    printf('<h1>Internal App Error</h1><p>Autoload file <q>%1$s</q> does not exist.</p>', htmlspecialchars($fileAutoload));
    exit(1);
}
require $fileAutoload;

$wantDiagnosticsRaw = $_GET['diagnostics'] ?? null;
$wantDiagnostics = (is_string($wantDiagnosticsRaw) ? (strtolower(trim($wantDiagnosticsRaw)) === 'true') : false);
if ($wantDiagnostics) {
    try {
        $diagnostics = new Diagnostics();
        $diagnose = $diagnostics->run() ? 'SUCCESS' : 'ERROR';
        echo '<p>' . htmlspecialchars($diagnose) . '</p><pre>' . htmlspecialchars($diagnostics->exportLog()) . '</pre>';
    } catch (\Throwable $error) {
        echo '<p>' . htmlspecialchars($error->__toString()) . '</p>';
    }
    exit(0);
}

$fileHeader = __DIR__ . '/../system/partials/header.php';
if (!file_exists($fileHeader)) {
    printf('<h1>Internal App Error</h1><p>Header file <q>%1$s</q> does not exist.</p>', htmlspecialchars($fileHeader));
    exit(1);
}

$fileFooter = __DIR__ . '/../system/partials/footer.php';
if (!file_exists($fileFooter)) {
    printf('<h1>Internal App Error</h1><p>Footer file <q>%1$s</q> does not exist.</p>', htmlspecialchars($fileFooter));
    exit(1);
}

if (!class_exists('Tommander\BlogSimple\Main')) {
    echo '<h1>Internal App Error</h1><p>Class <q>Main</q> is not autoloaded.</p>';
    exit(1);
}

$m = new Main();

include $fileHeader;
$m->render();
include $fileFooter;
