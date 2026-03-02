<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Pages
{
    /** @var array<non-empty-string, array{name: string, url: string, path: string, title: string, mdate: int}> */
    public private(set) array $pages = [];

    public static function pageUrl(string $name): string
    {
        return Main::homeUrl(['page' => $name]);
    }

    public function refreshPages(): void
    {
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
