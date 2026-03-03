<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use DateInterval;
use DateTimeImmutable;

class Main
{
    public const SITE_URL = 'http://localhost/blogsimple';
    public private(set) Pages $pages;
    public private(set) Posts $posts;
    public private(set) Errors $errors;
    public private(set) Cache $cache;
    public private(set) Logger $logger;


    public private(set) string $lang = 'cs-CZ';
    public private(set) string $title = 'Blog';
    public private(set) string $copyright = '&copy; 2026 Já.';
    public private(set) string $subtitle = '';
    public private(set) string|null $urlquery = null;
    public private(set) string|null $mdfile = 'pages/home.md';
    public private(set) string|null $content = null;


    public function __construct()
    {
        $this->pages = new Pages();
        // $this->pages->refreshPages();

        $this->posts = new Posts();
        // $this->posts->refreshPosts();

        $this->errors = new Errors();
        // $this->errors->refreshErrors();

        $this->cache = new Cache();
        $this->logger = new Logger();

        $post = $_GET['post'] ?? null;
        if (is_string($post) && !empty($post)) {
            $this->logger->debug('Post "{name}" recognized', ['name' => $post]);
            $this->setPost($post);
        }

        $page = $_GET['page'] ?? null;
        if (is_string($page) && !empty($page)) {
            $this->logger->debug('Post "{name}" recognized', ['name' => $post]);
            $this->setPage($page);
        }

        $error = $_GET['error'] ?? null;
        if (is_string($error) && !empty($error)) {
            $this->logger->debug('Post "{name}" recognized', ['name' => $post]);
            $this->setError($error);
        }

        $this->logger->debug('No recognized quey var - homepage it is, then.');
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

    public function setPage(string $name): void
    {
        $this->pages->refreshPages();
        $data = $this->pages->getPage($name);
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);

        $this->subtitle = $data['title'] ?? 'No Title';
        $this->urlquery = "page={$nameSafe}";
        $this->mdfile = "pages/{$nameSafe}.md";
        $this->content = null;
    }

    public function setPost(string $name): void
    {
        $this->posts->refreshPosts();
        $data = $this->posts->getPost($name);
        if ($name === 'archive') {
          $data['title'] = 'Archív';
        }
        if ($name === 'list') {
          $data['title'] = 'Příspěvky';
        }
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->subtitle = $data['title'] ?? 'No Title';
        $this->urlquery = "post={$nameSafe}";
        $this->mdfile = ($nameSafe === 'list' || $nameSafe === 'archive') ? null : "posts/{$nameSafe}.md";
        $this->content = ($nameSafe === 'list') ? $this->posts->postsList(true, false) : ($nameSafe === 'archive' ? $this->posts->postsList(false, true) : null);
    }

    public function htmltitle(): string
    {
        $right = htmlspecialchars($this->title);
        $left = '';
        if (is_string($this->subtitle) && !empty($this->subtitle)) {
            $left = htmlspecialchars($this->subtitle) . ' | ';
        }
        return $left . $right;
    }

    public function setError(string $name): void
    {
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->subtitle = 'Error';
        $this->urlquery = "error={$nameSafe}";
        $this->mdfile = "errors/{$nameSafe}.md";
        $this->content = null;

        ////         $res = sprintf("- [Příspěvky](%1\$s)\n", Posts::postListUrl());

    }

    public function menu(): string
    {
        $res = "<menu>";
        $res .= "<li><a class=\"home\" href=\"" . static::SITE_URL . "\">Hlavní stránka</a></li>";
        $res .= "<li><a class=\"post\" href=\"".Posts::postListUrl()."\">Příspěvky</a></li>";
        $res .= "<li><a class=\"archive\" href=\"".Posts::postArchiveUrl()."\">Archiv</a></li>";
        $this->pages->refreshPages();
        $res .= $this->pages->listPages(true);
        $res .= '</menu>';
        return $res;
    }

    public function renderMdFile(): void
    {
        if ($this->mdfile === null) {
            echo $this->content;
            return;
        }
        $this->posts->refreshPosts();
        echo $this->cache->get($this->mdfile, function (string $raw) {
            $raw = str_replace('%%TOC%%', $this->pages->listPages(false), $raw);
            $raw = str_replace('%%LAST5%%', $this->posts->last5(), $raw);
            return (new \League\CommonMark\GithubFlavoredMarkdownConverter())->convert($raw)->getContent();
        });
    }
}
