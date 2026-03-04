<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Main
{
    public private(set) Pages $pages;
    public private(set) Posts $posts;
    public private(set) Errors $errors;
    public private(set) Cache $cache;
    public private(set) Logger $logger;

    public private(set) string $subtitle = '';
    public private(set) string $mdfile = '';


    public function __construct()
    {
        $this->pages = new Pages();
        $this->posts = new Posts();
        $this->errors = new Errors();
        $this->cache = new Cache();
        $this->logger = new Logger();

        $cache = $_GET['cache'] ?? null;
        $post = $_GET['post'] ?? null;
        $page = $_GET['page'] ?? null;
        $error = $_GET['error'] ?? null;

        if ($cache === 'reset') {
            $this->cache->reset();
        }

        if (is_string($post) && !empty($post)) {
            $this->logger->debug('Post "{name}" recognized', ['name' => $post]);
            $this->setPost($post);
        } elseif (is_string($page) && !empty($page)) {
            $this->logger->debug('Page "{name}" recognized', ['name' => $post]);
            $this->setPage($page);
        } elseif (is_string($error) && !empty($error)) {
            $this->logger->debug('Error "{name}" recognized', ['name' => $post]);
            $this->setError($error);
        } else {
            $this->setPage('home');
        }
    }

    public static function homeUrl(array $query = []): string
    {
        return static::url('/index.php', $query);
    }

    public static function url(string $path = '/index.php', array $query = []): string
    {
        if (!str_starts_with($path, '/')) {
            return Configuration::SITE_URL;
        }

        $queryStr = '';
        if (count($query) > 0) {
            $queryStr = '?' . http_build_query($query);
        }

        return Configuration::SITE_URL . $path . $queryStr;
    }

    public function setPage(string $name): void
    {
        $this->pages->refreshPages();
        $data = $this->pages->getPage($name);
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);

        $this->subtitle = $data['title'] ?? 'No Title';
        $this->mdfile = "pages/{$nameSafe}.md";
    }

    public function setPost(string $name): void
    {
        $this->posts->refreshPosts();
        $data = $this->posts->getPost($name);
        $nameSafe = preg_replace('/[^A-Za-z0-9_-]/', '', $name);

        $this->subtitle = $data['title'] ?? 'No Title';
        $this->mdfile = "posts/{$nameSafe}.md";
    }

    public function htmltitle(): string
    {
        $right = htmlspecialchars(Configuration::BLOG_TITLE);
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
        $this->mdfile = "errors/{$nameSafe}.md";

    }

    public function menu(): string
    {
        $res = "<menu>";
        $this->pages->refreshPages();
        $res .= $this->pages->listPages(true);
        $res .= '</menu>';
        return $res;
    }

    public function renderMdFile(): void
    {
        $this->pages->refreshPages();
        $this->posts->refreshPosts();

//use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;

        echo $this->cache->get(
            $this->mdfile,
            function (string $raw) {
                $mdOptions = [
                    /*'html_input' => 'escape',*/ //because https://commonmark.thephpleague.com/2.x/extensions/disallowed-raw-html/
                    
                    'allow_unsafe_links' => false,
                    'max_nesting_level' => 25,
                    'max_delimiters_per_line' => 15,
                    'disallowed_raw_html' => [
                        'disallowed_tags' => ['title', 'textarea', 'style', 'xmp', 'iframe', 'noembed', 'noframes', 'script', 'plaintext', 'embed', 'object', 'audio', 'video', 'body', 'head'],
                    ],
                ];
                $raw = str_replace('<archive_duration>', Helper::niceInterval(Configuration::BLOG_ARCHIVE_TIME), $raw);
                $raw = str_replace('<list posts archived>', $this->posts->listPosts(true), $raw);
                $raw = str_replace('<list posts current>', $this->posts->listPosts(false), $raw);
                $raw = str_replace('<list posts last5>', $this->posts->last5(), $raw);
                $raw = str_replace('<list pages nohome>', $this->pages->listPages(false) , $raw);
                return (new \League\CommonMark\GithubFlavoredMarkdownConverter($mdOptions))->convert($raw)->getContent();
            }
        );
    }
}
