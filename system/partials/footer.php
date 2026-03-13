<?php
/**
 * Blog footer file.
 *
 * @psalm-suppress UnnecessaryVarAnnotation
 * @var \Tommander\BlogSimple\Main $m
 */
?>
        </main>
        <footer>
            <div>
                <?= htmlentities(string: \Tommander\BlogSimple\Configuration::BLOG_FOOTER, double_encode: false) ?>
            </div>
            <div>
                <small><samp><?php
                echo htmlspecialchars(Tommander\BlogSimple\Helper::niceIntervalNs($m->nanosecondsFromBlogRenderStart()));
                ?></samp></small>
            </div>
            <div>
                <small><?= \Tommander\BlogSimple\Cache::htmlStatus($m->cache); ?></small>
            </div>
        </footer>
    </div>
</body>
</html>