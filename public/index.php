<?php

/**
 * Blog's main index php file.
 */

declare(strict_types=1);

use Tommander\BlogSimple\Configuration;
use Tommander\BlogSimple\Main;

error_reporting(E_ALL);

$errorFileNotFoundHtml = '<h1>Internal App Error</h1><p>%1$s file <q>%2$s</q> does not exist.</p>';
$errorMainMissing = '<h1>Internal App Error</h1><p>Class <q>Main</q> is not autoloaded.</p>';

$fileAutoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($fileAutoload)) {
    printf($errorFileNotFoundHtml, 'Autoload', htmlspecialchars($fileAutoload));
    exit(1);
}
require $fileAutoload;

if (!class_exists('Tommander\BlogSimple\Main')) {
    echo $errorMainMissing;
    exit(1);
}

$fileHeader = Configuration::BLOG_DIR_PARTIALS . 'header.php';
if (!file_exists($fileHeader)) {
    printf($errorFileNotFoundHtml, 'Header', htmlspecialchars($fileHeader));
    exit(1);
}

$fileNav = Configuration::BLOG_DIR_PARTIALS . 'nav.php';
if (!file_exists($fileNav)) {
    printf($errorFileNotFoundHtml, 'Navigation', htmlspecialchars($fileNav));
    exit(1);
}

$fileMain = Configuration::BLOG_DIR_PARTIALS . 'main.php';
if (!file_exists($fileMain)) {
    printf($errorFileNotFoundHtml, 'Main', htmlspecialchars($fileMain));
    exit(1);
}

$fileFooter = Configuration::BLOG_DIR_PARTIALS . 'footer.php';
if (!file_exists($fileFooter)) {
    printf($errorFileNotFoundHtml, 'Footer', htmlspecialchars($fileFooter));
    exit(1);
}

$main = Main::getInstance();

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'">
    <link rel="stylesheet" href="style.css">
    <title><?= $main->htmltitle() ?></title>
</head>
<body>
    <div id="container">
<?php
include $fileHeader;
include $fileNav;
include $fileMain;
include $fileFooter;
?>
    </div>
</body>
</html>