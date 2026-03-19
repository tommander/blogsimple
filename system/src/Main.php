<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

/**
 * Entry class of the blog.
 */
final class Main
{
    private static Main|null $instance = null;

    public File $file;
    public Cache|null $cache;

    /** @var FileTypeEnum */
    public FileTypeEnum $contentDirname = FileTypeEnum::Pages;
    /** @var non-empty-string */
    public string $contentTitle = 'Homepage';
    /** @var non-empty-string */
    public string $contentName = 'home';
    public bool $debug = false;

    public static function getInstance(): Main
    {
        if (!(self::$instance instanceof Main)) {
            self::$instance = new Main();
        }
        return self::$instance;
    }

    private function __construct()
    {

        $this->cache = (Configuration::BLOG_NO_CACHE === true) ? null : new Cache();
        $this->file = new File();

        // Read query vars of the request
        $rawDebug = $_GET['debug'] ?? '';
        if (!is_string($rawDebug)) {
            $rawDebug = '';
        }
        $this->debug = (!empty($rawDebug));
        if ($this->debug) {
            return;
        }

        // Reset cache if requested
        $this->cache?->processActions();

        $post = $_GET[Configuration::BLOG_DIRNAME_POSTS] ?? null;
        if (is_string($post) && !empty($post)) {
            $this->setContent(FileTypeEnum::Posts, $post);
            return;
        }

        $page = $_GET[Configuration::BLOG_DIRNAME_PAGES] ?? null;
        $isPage = (is_string($page) && !empty($page));
        $realPage = ($isPage ? $page : 'home');
        $this->setContent(FileTypeEnum::Pages, $realPage);
    }

    /**
     * @param FileTypeEnum $type
     * @param non-empty-string $name
     */
    public function setContent(FileTypeEnum $type, string $name): void
    {
        $data = $this->file->getItem($type, $name);
        $title = $data['title'] ?? '';
        if (empty($title)) {
            $title = 'No title';
        }
        $name = (string) preg_replace('/[^A-Za-z0-9_-]/', '', $name);
        if (empty($name)) {
            return;
        }
        $this->contentTitle = $title;
        $this->contentName = $name;
        $this->contentDirname = $type;
    }

    public function htmltitle(): string
    {
        $right = htmlspecialchars(Configuration::BLOG_TITLE);
        $left = '';
        if (!empty($this->contentTitle)) {
            $left = htmlspecialchars($this->contentTitle) . ' | ';
        }
        return $left . $right;
    }

    public function menu(): string
    {
        $res = "<menu>";
        $res .= $this->file->listData(FileTypeEnum::Pages, File::CARD_HTML);
        $res .= '</menu>';
        return $res;
    }

    public function debugPage(): string
    {
        $testHtml = (string) file_get_contents(__DIR__ . '/../tests/fixtures/test_parsed.html');
        return sprintf(Configuration::TEXT_MAIN_DEBUG_PAGE_HTML, $testHtml);
    }

    public function md2html(string $md): string
    {
        $raw = str_replace('<archive_duration>', File::niceInterval(Configuration::BLOG_ARCHIVE_TIME), $md);
        $raw = str_replace('<list posts archived>', $this->file->listData(FileTypeEnum::Posts, File::CARD_MD_BIG, true), $raw);
        $raw = str_replace('<list posts current>', $this->file->listData(FileTypeEnum::Posts, File::CARD_MD_BIG, false), $raw);
        $raw = str_replace('<list posts last5>', $this->file->listData(FileTypeEnum::Posts, File::CARD_MD_BIG, false, 5), $raw);
        $raw = str_replace('<list pages nav>', $this->file->listData(FileTypeEnum::Pages, File::CARD_MD_BIG), $raw);
        $raw = str_replace('http://blog.example.com', Configuration::SITE_URL, $raw);

        $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter(Configuration::BLOG_MD_CONVERTER_CONFIG);
        $html = $converter->convert($raw);
        $htmlString = $html->getContent();
        return $htmlString;
    }

    public function cacheBypass(FileTypeEnum $dirName, string $fileName): string
    {
        $sourceFile = $dirName->path() . '/' . $fileName . '.md';

        if (
            !file_exists($sourceFile) ||
            !is_file($sourceFile) ||
            !is_readable($sourceFile)
        ) {
            if ($dirName !== FileTypeEnum::Pages || $fileName !== 'home') {
                return $this->cacheBypass(FileTypeEnum::Pages, 'home');
            }
            $safeSourceFile = htmlspecialchars($sourceFile);
            return sprintf(Configuration::TEXT_MAIN_SOURCE_FILE_ERROR_HTML, $safeSourceFile);
        }

        $raw = (string) file_get_contents($sourceFile);
        if ($dirName === FileTypeEnum::Posts) {
            $raw .= '<div><br><small>🗓️ Last modified: 21.12.2012 12:21</small></div>';
        }
        return $this->md2html($raw);
    }

    public function render(): void
    {
        $rawContent = match (true) {
            $this->debug => $this->debugPage(),
            ($this->cache instanceof Cache) => $this->cache->get($this->contentDirname, $this->contentName),
            default => $this->cacheBypass($this->contentDirname, $this->contentName),
        };

        // Hmmm... something is missing... sanitizer, maybe?

        echo $rawContent;
    }
}
