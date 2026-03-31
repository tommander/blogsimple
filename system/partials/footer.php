<?php

/**
 * Blog footer file.
 */

$m = Tommander\BlogSimple\Main::getInstance();
?>
<footer>
    <div class="copyleft">
        <span class="copyleft-icon"><?= \Tommander\BlogSimple\Configuration::BLOG_FOOTER_ICON ?></span>
        <span class="copyleft-text"><a href="<?= \Tommander\BlogSimple\Configuration::BLOG_FOOTER_URL ?>">
        <?= htmlentities(string: \Tommander\BlogSimple\Configuration::BLOG_FOOTER, double_encode: false) ?>
        </a>
        </span>
    </div>
    <div>
        <small><?= \Tommander\BlogSimple\Cache::htmlStatus($m->cache); ?></small>
    </div>
</footer>
