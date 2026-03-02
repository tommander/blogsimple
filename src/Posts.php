<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Posts
{
    public const ARCHIVE_DURATION = (9*3600); // 9 hod
    /* 1 = url 2 = title 3 = date('',mdate) 4 = interval 5 = excerpt */
    public const POSTCARD = <<<'HTML'
        <div class="postcard">
            <div class="postcard-title"><a href="%1$s">%2$s</a></div>
            <div class="postcard-meta"><small><time xyz="%3$s">%4$s</time></small></div>
            <div class="postcard-text">%5$s</div>
        </div>
        HTML;
    /** @var array<non-empty-string, array{name: string, url: string, path: string, title: string, mdate: int, excerpt: string}> */
    public private(set) array $posts = [];

    public static function postArchiveUrl(): string
    {
        return Main::homeUrl(['post' => 'archive']);
    }

    public static function postListUrl(): string
    {
        return Main::homeUrl(['post' => 'list']);
    }

    public static function postUrl(string $name): string
    {
        return Main::homeUrl(['post' => $name]);
    }

    public static function niceInterval(int $seconds): string
    {
            return match (true) {
                $seconds < 60 => 'now',
                $seconds < 3600 => sprintf('%.0f min', intdiv($seconds, 60)),
                $seconds < 86400 => sprintf('%.0f hod', intdiv($seconds, 3600)),
                $seconds < (2*86400) => '1 den',
                $seconds < (5*86400) => sprintf('%.0f dny', intdiv($seconds, 86400)),
                $seconds >= (5*86400) => sprintf('%.0f dni', intdiv($seconds, 86400)),
            };        
    }

    public function last5(): string
    {
        $res = '';
        reset($this->posts);
        $i = 0;
        while ($i < 5) {
            // $name = key($this->posts->posts);
            $data = current($this->posts);
            $html = sprintf(
                Posts::POSTCARD,
                $data['url'], // 1 = url
                $data['title'],// 2 = title
                date('d.m.Y H:i:s', $data['mdate']),// 3 = date('',mdate)
                Posts::niceInterval(time() - $data['mdate']),// 4 = interval
                $data['excerpt'],// 5 = excerpt
            );
            $res .= $html;
            $i++;
            $next = next($this->posts);
            if ($next === false) {
                break;
            }
        }
        return $res;
    }



    public function refreshPosts(): void
    {
        $lst = scandir(__DIR__ . '/../posts/', SCANDIR_SORT_NONE);
        if (!is_array($lst)) {
            $lst = [];
        }

        $this->posts = [];
        foreach ($lst as $file) {
            if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md')) {
                continue;
            }
            $name = substr($file, 0, strlen($file)-3);
            $path = __DIR__ . '/../posts/' . $file;
            $content = (string) file_get_contents($path);
            $firstPara = 'Eh...';
            if (preg_match('/\n?#.+?\n\n(?<para1>.+?)(?:\s*\n\n|\s*$)/', $content, $matches) === 1) {
                $firstPara = $matches['para1'];
            }
            $excerpt = (strlen($firstPara) > 200) ? substr($firstPara, 0, 199) . '&hellip;' : $firstPara;
            $matches = [];
            if (preg_match('/\n?#\s*(?<title>[^\r\n\0$]+)\s*/', $content, $matches) === 1) {
                $title = $matches['title'];
            }

            $item = [
                'name' => $name,
                'url' => $this->postUrl($name),
                'path' => $path,
                'title' => $title,
                'excerpt' => $excerpt,
                'mdate' => filemtime($path),
            ];
            $this->posts[$name] = $item;
        }
    }
    public function postsList(bool $includeCurrent, bool $includeArchive): string
    {
        $res = '';
        $res .= match (true) {
            $includeCurrent && !$includeArchive => sprintf('<h2>Příspěvky</h2><p><a href="%1$s">Prispevky starsi nez %2$s</a></p>', $this->postArchiveUrl(), static::niceInterval(static::ARCHIVE_DURATION)),
            !$includeCurrent && $includeArchive => sprintf('<h2>Archiv</h2><p><a href="%1$s">Prispevky novejsi nez %2$s</a></p>', $this->postListUrl(), static::niceInterval(static::ARCHIVE_DURATION)),
            $includeCurrent && $includeArchive => '<h2>Všechny příspěvky</h2>',
            default => '<h2>Nějaká chyba</h2>',
        };

        foreach ($this->posts as $name => $data) {
            $seconds = (time() - $data['mdate']);
            $isArchive = ($seconds > static::ARCHIVE_DURATION);
            if (($isArchive && !$includeArchive) || (!$isArchive && !$includeCurrent)) {
                continue;
            }
            $intvlText = static::niceInterval($seconds);
            $content = file_get_contents($data['path']);
            if (!is_string($content)) {
                $content = '<p>Missing file/content. <details><summary>Details</summary><pre>' . htmlspecialchars($data['path']) . '</pre></details></p>';
            }

            $html = sprintf(
                static::POSTCARD,
                $this->postUrl($name), // 1 = url
                $data['title'],// 2 = title
                date('d.m.Y H:i:s', $data['mdate']),// 3 = date('',mdate)
                $intvlText,// 4 = interval
                $data['excerpt'],// 5 = excerpt
            );

            $res .= $html;
        }

        return $res;
    }
}
