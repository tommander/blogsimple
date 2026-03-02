<!DOCTYPE html>
<html lang="<?= Tommander\BlogSimple\Helper::esclang($m->lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fonts.css">
    <link rel="stylesheet" href="style.css">
    <title><?= htmlspecialchars($m->title) ?></title>
</head>
<body>
    <div id="container">
        <header><a href="<?= Tommander\BlogSimple\Main::SITE_URL ?>"><?= htmlspecialchars($m->title) ?></a></header>
        <nav><?= $m->menu(); ?></nav>
        <main>
            
