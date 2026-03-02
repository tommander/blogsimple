<?php

declare(strict_types=1);

error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
$m = new Tommander\BlogSimple\Main();
include __DIR__. '/header.php';

if (!isset($m) || !class_exists('Tommander\BlogSimple\Main') || !($m instanceof Tommander\BlogSimple\Main)): ?>
    <h1>Internal App Error</h1>
    <p>App is not initialized.</p>
    <?php exit(1); ?>
<?php endif;

$m->renderMdFile();

include __DIR__. '/footer.php';
