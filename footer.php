        </main>
        <footer>
            <div><?= htmlentities(string: $m->copyright, double_encode: false) ?></div>
            <div><small><samp><?php 
                $webRenderTime = (hrtime(true) - $startTimestamp);
                echo htmlspecialchars(Tommander\BlogSimple\Helper::niceIntervalNs($webRenderTime));
            ?></samp></small></div>
            <div><small><?php
                printf(
                    '<samp>Hits: %1$d</samp> <samp>Misses: %2$d</samp> <samp>CS: %6$s</samp> <samp>CR: %3$s</samp> <samp>CW: %4$s</samp> <samp>IR: %5$s</samp>',
                    $m->cache->hits,
                    $m->cache->misses,
                    \Tommander\BlogSimple\Helper::niceBytes($m->cache->cacheRead),
                    \Tommander\BlogSimple\Helper::niceBytes($m->cache->cacheWrite),
                    \Tommander\BlogSimple\Helper::niceBytes($m->cache->ioRead),
                    \Tommander\BlogSimple\Helper::niceBytes(\Tommander\BlogSimple\Cache::cacheSize()),
                );
            ?></small></div>
        </footer>
    </div>
</body>
</html>