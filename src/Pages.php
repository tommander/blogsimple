<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Pages
{
    /** @var array<non-empty-string, array{name: string, url: string, path: string, title: string, mdate: int}> */
    public private(set) array $pages = [];
    private bool $refreshed = false;

    public static function pageUrl(string $name): string
    {
        return Main::homeUrl(['page' => $name]);
    }

    public function getPage(string $name): array
    {
        if (!isset($this->pages[$name])) {
            return [];
        }
        return $this->pages[$name];
    }

    public function listPages(bool $html): string
    {
        $this->refreshPages();
        $res = '';
        foreach ($this->pages as $name => $data) {
            if ($name === 'home') {
                continue;
            }
            $res .= sprintf(
                $html ? "<li><a class=\"page\" href=\"%2\$s\">%1\$s</a></li>\n" : "- [%1\$s](%2\$s)\n",
                $data['title'],
                $data['url']
            );
        }
        return $res;
    }



    public function refreshPages(): void
    {
        if ($this->refreshed) {
            return;
        }
        $this->refreshed = true;

        $lst = scandir(__DIR__ . '/../pages/', SCANDIR_SORT_NONE);
        if (!is_array($lst)) {
            $lst = [];
        }

        $this->pages = [];
        foreach ($lst as $file) {
            if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md')) {
                continue;
            }
            $name = substr($file, 0, strlen($file)-3);
            $path = __DIR__ . '/../pages/' . $file;
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
                'mdate' => filemtime($path),
            ];
            $this->pages[$name] = $item;
        }
    }
}
