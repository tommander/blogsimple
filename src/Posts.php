<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;

class Posts extends File
{
    use LoggerAwareTrait;

    /** @var array<non-empty-string, array{name: string, url: string, path: string, title: string, mdate: int, excerpt?: string}> */
    public private(set) array $posts = [];
    private bool $refreshed = false;

    public function getPost(string $name): array
    {
        if (!isset($this->posts[$name])) {
            return [];
        }
        return $this->posts[$name];
    }

    public function refreshPosts(): void
    {
        if ($this->refreshed) {
            return;
        }
        $this->refreshed = true;

        $lst = scandir(Configuration::BLOG_DIR_POSTS, SCANDIR_SORT_NONE);
        if (!is_array($lst)) {
            $lst = [];
        }

        $this->posts = [];
        foreach ($lst as $file) {
            if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md')) {
                continue;
            }
            $name = substr($file, 0, strlen($file)-3);
            $path = Configuration::BLOG_DIR_POSTS . $file;
            $content = (string) file_get_contents($path);
            $title = 'No Title ';
            if (preg_match('/\n?#\s*(?<title>[^\r\n\0$]+)\s*/', $content, $matches) === 1) {
                $title = $matches['title'];
            }
            $firstPara = '';
            if (preg_match('/\n?#.+?\n\n(?<para1>.+?)(?:\s*\n\n|\s*$)/', $content, $matches) === 1) {
                $firstPara = $matches['para1'];
            }
            $excerpt = (strlen($firstPara) > 200) ? substr($firstPara, 0, 199) . '&hellip;' : $firstPara;
            $matches = [];

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

    public function listPosts(bool $archive): string
    {
        $this->refreshPosts();
        $res = '';
        foreach ($this->posts as $post) {
            if (!is_array($post)) {
                $this->logger->warning('Strange value for a post "{val}"', ['val' => var_export($post, true)]);
                continue;
            }

            $isArchived = ((time() - ($post['mdate'] ?? 0)) > Configuration::BLOG_ARCHIVE_TIME);
            if ($archive xor $isArchived) {
                continue;
            }

            $res .= static::postcard(
                $post['url'] ?? 'http://example.com',
                $post['title'] ?? 'Default Title',
                $post['excerpt'] ?? 'Default Excerpt',
                $post['mdate'] ?? time(),
            );
        }

        return $res;
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

    public static function postArchiveUrl(): string
    {
        return Main::homeUrl(['page' => 'archive']);
    }

    public static function postListUrl(): string
    {
        return Main::homeUrl(['page' => 'list']);
    }

    public static function postUrl(string $name): string
    {
        return Main::homeUrl(['post' => $name]);
    }

    public static function postcard(string $url, string $title, string $excerpt, int $mdate): string
    {
        return sprintf(
            <<<'HTML'
            > *[%2$s](%1$s)*
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
