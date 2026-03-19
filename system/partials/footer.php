<?php

/**
 * Blog footer file.
 */

$m = Tommander\BlogSimple\Main::getInstance();
?>
<footer>
    <div>
        <?= htmlentities(string: \Tommander\BlogSimple\Configuration::BLOG_FOOTER, double_encode: false) ?>
    </div>
    <div>
        <?= \Tommander\BlogSimple\File::datacard(\Tommander\BlogSimple\FileTypeEnum::Pages, \Tommander\BlogSimple\File::CARD_HTMLAONLY, 'index.php?debug=y', 'Debug', '', 0) ?>
    </div>
    <div>
        <small><?= \Tommander\BlogSimple\Cache::htmlStatus($m->cache); ?></small>
    </div>
</footer>
