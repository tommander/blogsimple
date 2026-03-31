<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

/**
 * Entry class of the blog.
 *
 * @psalm-import-type BlogSimpleOneFileData from File
 */
final class Main
{
    private static Main|null $instance = null;

    public File $file;
    public Cache|null $cache;

    /** @var non-empty-string */
    public string $contentTitle = 'Homepage';
    /** @var non-empty-string */
    public string $contentName = 'home';

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

        // Reset cache if requested
        $this->cache?->processActions();

        $article = $_GET['article'] ?? null;
        if (is_string($article) && !empty($article)) {
            $this->setContent($article);
            return;
        }
    }

    /**
     * @param non-empty-string $name
     */
    public function setContent(string $name): void
    {
        $data = $this->file->getItem($name);
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
        $res .= $this->file->listData(File::CARD_HTML);
        $res .= '</menu>';
        return $res;
    }

    public function md2html(string $md): string
    {
        $raw = str_replace('<archive_duration>', File::niceInterval(Configuration::BLOG_ARCHIVE_TIME), $md);
        $raw = str_replace('<articles archived>', $this->file->listData(File::CARD_MD, true), $raw);
        $raw = str_replace('<articles current>', $this->file->listData(File::CARD_MD, false), $raw);
        $raw = str_replace('<articles last5>', $this->file->listData(File::CARD_MD, false, 5), $raw);
        $raw = str_replace('http://blog.example.com', Configuration::SITE_URL, $raw);
        $rawtemp = preg_replace('/<article:([^>]+)>/', Configuration::SITE_URL . '/index.php?article=$1', $raw);
        if (is_string($rawtemp)) {
            $raw = $rawtemp;
        }

        $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter(Configuration::BLOG_MD_CONVERTER_CONFIG);
        $html = $converter->convert($raw);
        $htmlString = $html->getContent();
        return $htmlString;
    }

    public function cacheBypass(string $fileName): string
    {
        $sourceFile = Configuration::BLOG_DIR_ARTICLES . $fileName . '.md';

        if (
            empty($fileName) ||
            !$this->file->hasItem($fileName) ||
            !file_exists($sourceFile) ||
            !is_file($sourceFile) ||
            !is_readable($sourceFile)
        ) {
            if ($fileName !== 'home') {
                return $this->cacheBypass('home');
            }
            $safeSourceFile = htmlspecialchars($sourceFile);
            return sprintf(Configuration::TEXT_MAIN_SOURCE_FILE_ERROR_HTML, $safeSourceFile);
        }

        /** @var BlogSimpleOneFileData */
        $thedata = $this->file->getItem($fileName);
        $raw = (string) file_get_contents($sourceFile);
        $raw .= '<div><small>🗓️ Last modified: ' . date('d.m.Y H:i', $thedata['mdate']) . '</small></div>';
        $html = $this->md2html($raw);
        ($this->cache instanceof Cache) && file_put_contents(Configuration::BLOG_DIR_CACHE . Cache::cacheFilename($fileName), $html);
        return $html;
    }

    public function render(): void
    {
        $rawContent = match (true) {
            ($this->cache instanceof Cache) => $this->cache->get($this->contentName) ?? $this->cacheBypass($this->contentName),
            default => $this->cacheBypass($this->contentName),
        };

        // Hmmm... something is missing... sanitizer, maybe?

        echo $rawContent;
    }
}
