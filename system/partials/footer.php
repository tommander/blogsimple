<?php

/**
 * Blog footer file.
 */

$m = Tommander\BlogSimple\Main::getInstance();
?>
<footer>
    <div class="copyleft">
        <span class="copyleft-icon">&copy;</span>
        <span class="copyleft-text">
        <?= htmlentities(string: \Tommander\BlogSimple\Configuration::BLOG_FOOTER, double_encode: false) ?>
        </span>
    </div>
    <div>
        <small><?= \Tommander\BlogSimple\Cache::htmlStatus($m->cache); ?></small>
    </div>
</footer>
