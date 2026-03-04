<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;

class Pages extends File
{
    use LoggerAwareTrait;

    /** @var array<non-empty-string, array{name: string, url: string, path: string, title: string, mdate: int}> */
    public private(set) array $pages = [];
    private bool $refreshed = false;

    public function getPage(string $name): array
    {
        if (!isset($this->pages[$name])) {
            return [];
        }
        return $this->pages[$name];
    }

    public function refreshPages(): void
    {
        if ($this->refreshed) {
            return;
        }
        $this->refreshed = true;

        $lst = scandir(Configuration::BLOG_DIR_PAGES, SCANDIR_SORT_NONE);
        if (!is_array($lst)) {
            $lst = [];
        }

        $this->pages = [];
        foreach ($lst as $file) {
            if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md')) {
                continue;
            }
            $name = substr($file, 0, strlen($file)-3);
            $path = Configuration::BLOG_DIR_PAGES . $file;
            $content = (string) file_get_contents($path);
            $title = 'No Title ';
            if (preg_match('/\n?#\s*(?<title>[^\r\n\0$]+)\s*/', $content, $matches) === 1) {
                $title = $matches['title'];
            }

            $item = [
                'name' => $name,
                'url' => $this->pageUrl($name),
                'path' => $path,
                'title' => $title,
                'excerpt' => '',
                'mdate' => filemtime($path),
            ];
            $this->pages[$name] = $item;
        }
    }
    
    public function listPages(bool $html): string
    {
        $this->refreshPages();
        $res = '';
        foreach ($this->pages as $name => $data) {
            $res .= static::pagecard(
                $html,
                $data['url'],
                $data['title'],
                '',
                $data['mdate'],
            );
        }
        return $res;
    }

    public static function pageUrl(string $name): string
    {
        return Main::homeUrl(['page' => $name]);
    }

    public static function pagecard(bool $html, string $url, string $title, string $excerpt, int $mdate): string
    {
        return sprintf(
            $html
                ? "<li><a class=\"page\" href=\"%1\$s\">%2\$s</a></li>\n"
                : "[%2\$s](%1\$s)  \n",
            $url,
            $title,
            date('d.m.Y H:i:s', $mdate),
            Helper::niceInterval(time() - ((int) $mdate)),
            $excerpt,
        );
    }

    public static function pagecardhtml(string $url, string $title, string $excerpt, int $mdate): string
    {
        return sprintf(
            <<<'HTML'
                > *[%2$s](%1$s)*\
                > 🗓️ %4$s
                >
                > %5$s
            HTML,
            $url,
            $title,
            date('d.m.Y H:i:s', $mdate),
            Helper::niceInterval(time() - ((int) $mdate)),
            $excerpt,
        );
    }
}
