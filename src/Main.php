<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use DateInterval;
use DateTimeImmutable;

class Main
{
    public const SITE_URL = 'http://localhost/blogsimple';
    public private(set) string $lang = 'cs-CZ';
    public private(set) string $title = 'Blog';
    public private(set) string $copyright = '&copy; 2026 Já.';
    public private(set) Pages $pages;
    public private(set) Posts $posts;
    public private(set) Errors $errors;

    public private(set) string $subtitle = '';
    public private(set) string|null $urlquery = null;
    public private(set) string|null $mdfile = 'pages/home.md';
    public private(set) string|null $content = null;


    public function __construct()
    {
        $this->pages = new Pages();
        $this->pages->refreshPages();

        $this->posts = new Posts();
        $this->posts->refreshPosts();

        $post = $_GET['post'] ?? null;
        if (is_string($post) && !empty($post)) {
            $this->setPost($post);
        }

        $page = $_GET['page'] ?? null;
        if (is_string($page) && !empty($page)) {
            $this->setPage($page);
        }

        $error = $_GET['error'] ?? null;
        if (is_string($error) && !empty($error)) {
            $this->setError($error);
        }
    }

    public function setPage(string $name): void
    {
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->subtitle = $this->pages->pages[$name]['title'] ?? 'No Title';
        $this->urlquery = "page={$nameSafe}";
        $this->mdfile = "pages/{$nameSafe}.md";
        $this->content = null;
    }

    public function setPost(string $name): void
    {
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->subtitle = $this->pages->pages[$name]['title'] ?? 'No Title';
        $this->urlquery = "post={$nameSafe}";
        $this->mdfile = ($nameSafe === 'list' || $nameSafe === 'archive') ? null : "posts/{$nameSafe}.md";
        $this->content = ($nameSafe === 'list') ? $this->posts->postsList(true, false) : ($nameSafe === 'archive' ? $this->posts->postsList(false, true) : null);
    }

    public function setError(string $name): void
    {
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->subtitle = 'Error';
        $this->urlquery = "error={$nameSafe}";
        $this->mdfile = "errors/{$nameSafe}.md";
        $this->content = null;
    }

    public function toc(): string
    {
        $res = sprintf("- [Příspěvky](%1\$s)\n", Posts::postListUrl());
        foreach ($this->pages->pages as $name => $data) {
            if ($name === 'home') {
                continue;
            }
            $res .= sprintf("- [%1\$s](%2\$s)\n", $data['title'], $data['url']);
        }
        return $res;
    }

    public function menu(): string
    {
        $res = "<menu>";
        $res .= "<li><a class=\"home\" href=\"" . static::SITE_URL . "\">Hlavní stránka</a></li>";
        $res .= "<li><a class=\"post\" href=\"".Posts::postListUrl()."\">Příspěvky</a></li>";
        $res .= "<li><a class=\"archive\" href=\"".Posts::postArchiveUrl()."\">Archiv</a></li>";
        foreach ($this->pages->pages as $name => $data) {
            if ($name === 'home') {
                continue;
            }
            $res .= "<li><a class=\"page\" href=\"{$data['url']}\">{$data['title']}</a></li>";
        }
        $res .= '</menu>';
        return $res;
    }


    public static function homeUrl(array $query = []): string
    {
        return static::url('/index.php', $query);
    }

    public static function url(string $path = '/index.php', array $query = []): string
    {
        if (!str_starts_with($path, '/')) {
            return static::SITE_URL;
        }

        $queryStr = '';
        if (count($query) > 0) {
            $queryStr = '?' . http_build_query($query);
        }

        return static::SITE_URL . $path . $queryStr;
    }

    public function renderMdFile(): void
    {
        if (is_string($this->content)) {
            echo $this->content;
            return;
        }
    
        $path = __DIR__ . '/../' . $this->mdfile;
        if (!file_exists($path) || !is_file($path) || !is_readable($path)) {
            echo 'WTF: ' . $path;
            return;
            // $raw = (string) file_get_contents($path);
        }
        
        $raw = (string) file_get_contents($path);
        $raw = str_replace('%%TOC%%', $this->toc(), $raw);
        $raw = str_replace('%%LAST5%%', $this->posts->last5(), $raw);
        echo (new \League\CommonMark\GithubFlavoredMarkdownConverter())->convert($raw)->getContent();
    }
}
