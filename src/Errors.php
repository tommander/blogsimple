<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Errors
{
    /** @var array<non-empty-string, array{name: string, url: string, path: string, title: string, mdate: int}> */
    public private(set) array $errors = [];

    public static function errorUrl(string $name): string
    {
        return Main::homeUrl(['error' => $name]);
    }

    public function refreshErrors(): void
    {
        $lst = scandir(__DIR__ . '/../errors/', SCANDIR_SORT_NONE);
        if (!is_array($lst)) {
            $lst = [];
        }

        $this->errors = [];
        foreach ($lst as $file) {
            if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md')) {
                continue;
            }
            $name = substr($file, 0, strlen($file)-3);
            $path = __DIR__ . '/../errors/' . $file;
            $content = (string) file_get_contents($path);
            $title = 'No Title ';
            if (preg_match('/\n?#\s*(?<title>[^\r\n\0$]+)\s*/', $content, $matches) === 1) {
                $title = $matches['title'];
            }

            $item = [
                'name' => $name,
                'url' => $this->errorUrl($name),
                'path' => $path,
                'title' => $title,
                'mdate' => filemtime($path),
            ];
            $this->errors[$name] = $item;
        }
    }
}
