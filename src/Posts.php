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

    public function getPost(string $name): array
    {
        if (!isset($this->posts[$name])) {
            return [];
        }
        return $this->posts[$name];
    }

    public static function postcard(string $url, string $title, string $excerpt, int $mdate): string
    {
        return sprintf(
            static::POSTCARD,
            $url,
            $title,
            date('d.m.Y H:i:s', $mdate),
            Helper::niceInterval(time() - ((int) $mdate)),
            $excerpt,
        );
    }

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

    public function last5(): string
    {
        $res = '';
        $keys = array_keys($this->posts);
        for ($i = 0; $i < 5; $i++) {
            if ($i >= count($keys)) {
                break;
            }
            $data = $this->posts[$keys[$i]];
            if (!is_array($data)) {
                continue;
            }
            $res .= static::postcard(
                $data['url'] ?? 'http://example.com',
                $data['title'] ?? 'Default Title',
                $data['excerpt'] ?? 'Default Excerpt',
                $data['mdate'] ?? time(),
            );
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
            $includeCurrent && !$includeArchive => sprintf('<h2>Příspěvky</h2><p><a href="%1$s">Prispevky starsi nez %2$s</a></p>', $this->postArchiveUrl(), Helper::niceInterval(static::ARCHIVE_DURATION)),
            !$includeCurrent && $includeArchive => sprintf('<h2>Archiv</h2><p><a href="%1$s">Prispevky novejsi nez %2$s</a></p>', $this->postListUrl(), Helper::niceInterval(static::ARCHIVE_DURATION)),
            $includeCurrent && $includeArchive => '<h2>Všechny příspěvky</h2>',
            default => '<h2>Nějaká chyba</h2>',
        };

        foreach ($this->posts as $name => $data) {
            $seconds = (time() - $data['mdate']);
            $isArchive = ($seconds > static::ARCHIVE_DURATION);
            if (($isArchive && !$includeArchive) || (!$isArchive && !$includeCurrent)) {
                continue;
            }

            $res .= static::postcard(
                $this->postUrl($name),
                $data['title'],
                $data['excerpt'],
                $data['mdate']
            );
        }

        return $res;
    }
}
