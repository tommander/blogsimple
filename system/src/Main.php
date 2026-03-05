<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

class Main
{
    public private(set) File $file;
    public private(set) Cache $cache;
    public private(set) Logger $logger;

    public private(set) string $contentDirname = '';
    public private(set) string $contentTitle = '';
    public private(set) string $contentName = '';
    public private(set) bool $debug = false;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->cache = new Cache($this->logger);
        $this->file = new File($this->logger);

        $this->debug = (is_string($_GET['debug'] ?? null) && !empty($_GET['debug'] ?? ''));
        $cache = $_GET['cache'] ?? null;
        $post = $_GET[File::FILETYPE_POST] ?? null;
        $page = $_GET[File::FILETYPE_PAGE] ?? null;
        $error = $_GET[File::FILETYPE_ERROR] ?? null;

        if ($cache === 'reset') {
            $this->logger->debug('Resetting cache');
            $this->cache->reset();
        }

        if ($this->debug) {
            $this->logger->info('Debug recognized');
        } elseif (is_string($post) && !empty($post)) {
            $this->logger->info('Post "{name}" recognized', ['name' => $post]);
            $this->setContent(File::FILETYPE_POST, $post);
        } elseif (is_string($error) && !empty($error)) {
            $this->logger->info('Error "{name}" recognized', ['name' => $error]);
            $this->setContent(File::FILETYPE_ERROR, $error);
        } else {
            $isPage = (is_string($page) && !empty($page));
            $realPage = ($isPage ? $page : 'home');
            $this->logger->info('Page "{name}" recognized', ['name' => $realPage]);
            $this->setContent(File::FILETYPE_PAGE, $realPage);
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

    public function setContent(string $type, string $name): void
    {
        $data = $this->file->getItem($type, $name);
        $this->contentTitle = $data['title'] ?? 'No Title';
        $this->contentName = preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        $this->contentDirname = match ($type) {
            File::FILETYPE_POST => Configuration::BLOG_DIRNAME_POSTS,
            File::FILETYPE_PAGE => Configuration::BLOG_DIRNAME_PAGES,
            File::FILETYPE_ERROR => Configuration::BLOG_DIRNAME_ERRORS,
        };
    }

    public function htmltitle(): string
    {
        $right = htmlspecialchars(Configuration::BLOG_TITLE);
        $left = '';
        if (is_string($this->contentTitle) && !empty($this->contentTitle)) {
            $left = htmlspecialchars($this->contentTitle) . ' | ';
        }
        return $left . $right;
    }

    public function menu(): string
    {
        $res = "<menu>";
        $res .= $this->file->listData(File::FILETYPE_PAGE, File::CARD_HTML);
        $res .= '</menu>';
        return $res;
    }

    public function renderMdFile(): void
    {
        if ($this->debug) {
            ?>
            <ul>
                <li><kbd>SITE_URL</kbd>&nbsp;&rarr;&nbsp;<samp><?php Configuration::SITE_URL ?></samp></li>
                <li><kbd>BLOG_LOCALE</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_LOCALE ?></samp></li>
                <li><kbd>BLOG_TITLE</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_TITLE ?></samp></li>
                <li><kbd>BLOG_FOOTER</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_FOOTER ?></samp></li>
                <li><kbd>BLOG_ROOT</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_ROOT ?></samp></li>
                <li><kbd>BLOG_DIRNAME_SYSTEM</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_SYSTEM ?></samp></li>
                <li><kbd>BLOG_DIRNAME_PUBLIC</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_PUBLIC ?></samp></li>
                <li><kbd>BLOG_DIRNAME_CACHE</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_CACHE ?></samp></li>
                <li><kbd>BLOG_DIRNAME_LOGS</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_LOGS ?></samp></li>
                <li><kbd>BLOG_DIRNAME_PAGES</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_PAGES ?></samp></li>
                <li><kbd>BLOG_DIRNAME_POSTS</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_POSTS ?></samp></li>
                <li><kbd>BLOG_DIRNAME_ERRORS</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_ERRORS ?></samp></li>
                <li><kbd>BLOG_DIRNAME_TRASH</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIRNAME_TRASH ?></samp></li>
                <li><kbd>BLOG_DIR_SYSTEM</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_SYSTEM ?></samp></li>
                <li><kbd>BLOG_DIR_PUBLIC</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_PUBLIC ?></samp></li>
                <li><kbd>BLOG_DIR_CACHE</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_CACHE ?></samp></li>
                <li><kbd>BLOG_DIR_LOGS</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_LOGS ?></samp></li>
                <li><kbd>BLOG_DIR_PAGES</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_PAGES ?></samp></li>
                <li><kbd>BLOG_DIR_POSTS</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_POSTS ?></samp></li>
                <li><kbd>BLOG_DIR_ERRORS</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_ERRORS ?></samp></li>
                <li><kbd>BLOG_DIR_TRASH</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_DIR_TRASH ?></samp></li>
                <li><kbd>BLOG_ARCHIVE_TIME</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_ARCHIVE_TIME ?></samp></li>
                <li><kbd>BLOG_CACHE_TTL</kbd>&nbsp;&rarr;&nbsp;<samp><?= Configuration::BLOG_CACHE_TTL ?></samp></li>
            </ul>
            <?php
            return;
        }

        echo $this->cache->get(
            $this->contentDirname,
            $this->contentName,
            function (string $raw) {
                $mdOptions = [
                    'allow_unsafe_links' => false,
                    'max_nesting_level' => 25,
                    'max_delimiters_per_line' => 15,
                    'disallowed_raw_html' => [
                        'disallowed_tags' => [
                            'title', 'textarea', 'style', 'xmp', 'iframe', 'noembed', 'noframes',
                            'script', 'plaintext', 'embed', 'object', 'audio', 'video', 'body', 
                            'head', 'form', 'input', 'textarea', 'output', 'select', 'button',
                        ],
                    ],
                ];
                $raw = str_replace('<archive_duration>', Helper::niceInterval(Configuration::BLOG_ARCHIVE_TIME), $raw);
                $raw = str_replace('<list posts archived>', $this->file->listData(File::FILETYPE_POST, File::CARD_MD_BIG, true), $raw);
                $raw = str_replace('<list posts current>', $this->file->listData(File::FILETYPE_POST, File::CARD_MD_BIG, false), $raw);
                $raw = str_replace('<list posts last5>', $this->file->listData(File::FILETYPE_POST, File::CARD_MD_BIG, false), $raw);
                $raw = str_replace('<list pages nohome>', $this->file->listData(File::FILETYPE_PAGE, File::CARD_MD_SMALL) , $raw);
                return (new \League\CommonMark\GithubFlavoredMarkdownConverter($mdOptions))->convert($raw)->getContent();
            }
        );
    }
}
