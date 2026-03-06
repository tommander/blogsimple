<?php
/**
 * @var int $startTimestamp
 * @var \Tommander\BlogSimple\Main $m
 */
?>
        </main>
        <footer>
            <div><?= htmlentities(string: \Tommander\BlogSimple\Configuration::BLOG_FOOTER, double_encode: false) ?></div>
            <div>
            <small><samp><?php
                $webRenderTime = (hrtime(true) - $startTimestamp);
                echo htmlspecialchars(Tommander\BlogSimple\Helper::niceIntervalNs($webRenderTime));
            ?></samp></small></div>
            <div><small><?php
            if ($m->cache) {
                printf(
                    '<samp>Hits: %1$d</samp> <samp>Misses: %2$d</samp> <samp>CS: %5$s</samp> <samp>CR: %3$s</samp> <samp>CW: %4$s</samp>',
                    $m->cache->hits,
                    $m->cache->misses,
                    \Tommander\BlogSimple\Helper::niceBytes($m->cache->cacheRead),
                    \Tommander\BlogSimple\Helper::niceBytes($m->cache->cacheWrite),
                    \Tommander\BlogSimple\Helper::niceBytes(\Tommander\BlogSimple\Cache::cacheSize()),
                ); // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect
             // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect
            } else { ?>
            <samp>Cache Disabled</samp>
            <?php } ?>
             </small></div>
        </footer>
    </div>
</body>
</html>