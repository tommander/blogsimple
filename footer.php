        </main>
        <footer>
            <div><?= htmlentities(string: $m->copyright, double_encode: false) ?></div>
            <div><small><?php 
                $webRenderTime = (hrtime(true) - $startTimestamp);
                echo htmlspecialchars(Tommander\BlogSimple\Posts::niceIntervalNs($webRenderTime));
            ?></small></div>
            <div><small><?php
                printf(
                    'Hits: %1$d Misses: %2$d CR: %3$d CW: %4$d IR: %5$d',
                    $m->cache->hits,
                    $m->cache->misses,
                    $m->cache->cacheRead,
                    $m->cache->cacheWrite,
                    $m->cache->ioRead,
                );
            ?></small></div>
        </footer>
    </div>
</body>
</html>