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

    public function __construct()
    {
        $this->logger = new Logger();
        $this->cache = new Cache($this->logger);
        $this->file = new File($this->logger);

        $cache = $_GET['cache'] ?? null;
        $post = $_GET[File::FILETYPE_POST] ?? null;
        $page = $_GET[File::FILETYPE_PAGE] ?? null;
        $error = $_GET[File::FILETYPE_ERROR] ?? null;

        if ($cache === 'reset') {
            $this->logger->debug('Resetting cache');
            $this->cache->reset();
        }

        if (is_string($post) && !empty($post)) {
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
