<?php

/**
 * Blog header file.
 */

?><!DOCTYPE html>
<html lang="<?= Tommander\BlogSimple\Helper::esclang(\Tommander\BlogSimple\Configuration::BLOG_LOCALE) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <?php /** @psalm-suppress UnnecessaryVarAnnotation */
    /** @var Tommander\BlogSimple\Main $m */ ?>
    <title><?= $m->htmltitle() ?></title>
</head>
<body>
    <div id="container">
        <header><a href="<?= Tommander\BlogSimple\Configuration::SITE_URL ?>"><?= htmlspecialchars(\Tommander\BlogSimple\Configuration::BLOG_TITLE) ?></a></header>
        <nav><?= $m->menu(); ?></nav>
        <main>
            
