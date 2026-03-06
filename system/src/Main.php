<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;

final class Main
{
    use LoggerAwareTrait;

    public File $file;
    public Cache|null $cache;

    public string $contentDirname = '';
    public string $contentTitle = '';
    public string $contentName = '';
    public bool $debug = false;

    public function __construct()
    {
        // Instantiate object properties
        /** @psalm-suppress RedundantCondition */
        if (Configuration::BLOG_NO_LOG !== true) {
            $this->setLogger(new Logger());
        }
        $this->cache = (Configuration::BLOG_NO_CACHE === true) ? null : new Cache($this->logger);
        $this->file = new File($this->logger);

        // Read query vars of the request
        $rawDebug = $_GET['debug'] ?? '';
        if (!is_string($rawDebug)) {
            $rawDebug = '';
        }
        $this->debug = (!empty($rawDebug));
        $cache = $_GET['cache'] ?? null;
        $post = $_GET[File::FILETYPE_POST] ?? null;
        $page = $_GET[File::FILETYPE_PAGE] ?? null;
        $error = $_GET[File::FILETYPE_ERROR] ?? null;

        // Reset cache if requested
        if ($cache === 'reset') {
            $this->logger && $this->logger->debug('Resetting cache');
            $this->cache && $this->cache->reset();
        }

        // Set the current page
        if ($this->debug) {
            $this->logger && $this->logger->info('Debug recognized');
        } elseif (is_string($post) && !empty($post)) {
            $this->logger && $this->logger->info('Post "{name}" recognized', ['name' => $post]);
            $this->setContent(File::FILETYPE_POST, $post);
        } elseif (is_string($error) && !empty($error)) {
            $this->logger && $this->logger->info('Error "{name}" recognized', ['name' => $error]);
            $this->setContent(File::FILETYPE_ERROR, $error);
        } else {
            $isPage = (is_string($page) && !empty($page));
            $realPage = ($isPage ? $page : 'home');
            $this->logger && $this->logger->info('Page "{name}" recognized', ['name' => $realPage]);
            $this->setContent(File::FILETYPE_PAGE, $realPage);
        }

        // The rendering will be handled by $this->renderMdFile()
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
        $this->contentName = (string) preg_replace('/[^A-Za-z0-9_-]/', '', $name);
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
        if (!empty($this->contentTitle)) {
            $left = htmlspecialchars($this->contentTitle) . ' | ';
        }
        return $left . $right;
    }

    public function menu(): string
    {
        $res = "<menu>";
        $res .= $this->file->listData(File::FILETYPE_PAGE, File::CARD_HTML);
        $res .= File::datacard('', File::CARD_HTML, 'index.php?debug=y', '🐞 Debug', '', 0);
        $res .= '</menu>';
        return $res;
    }

    public function debugPage(): string
    {
        // $testMd = file_get_contents(__DIR__ . '/../tests/fixtures/test_unparsed.md');
        $testHtml = file_get_contents(__DIR__ . '/../tests/fixtures/test_parsed.html');
        return <<<HTML
        <h1>Debug</h1>
        <p>Tato stránka je určena vývojářům/testerům, kteří ji mohou najít v <code>/system/src/Main.php</code> v metodě <code>Main::debugPage()</code>. Odtud níže si přidávejte a mažte, co klávesnice ráčí.</p>
        $testHtml
        HTML;
    }

    public function md2html(string $md): string
    {
        $raw = str_replace('<archive_duration>', Helper::niceInterval(Configuration::BLOG_ARCHIVE_TIME), $md);
        $raw = str_replace('<list posts archived>', $this->file->listData(File::FILETYPE_POST, File::CARD_MD_BIG, true), $raw);
        $raw = str_replace('<list posts current>', $this->file->listData(File::FILETYPE_POST, File::CARD_MD_BIG, false), $raw);
        $raw = str_replace('<list posts last5>', $this->file->listData(File::FILETYPE_POST, File::CARD_MD_BIG, false), $raw);
        $raw = str_replace('<list pages nohome>', $this->file->listData(File::FILETYPE_PAGE, File::CARD_MD_BIG), $raw);
        $raw = str_replace('http://blog.example.com', Configuration::SITE_URL, $raw);
        $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter(Configuration::BLOG_MD_CONVERTER_CONFIG);
        $html = $converter->convert($raw);
        $htmlString = $html->getContent();
        return $htmlString;
    }

    public function cacheBypass(string $dirName, string $fileName): string
    {
        $sourceFile = Configuration::BLOG_DIR_PUBLIC . $dirName . '/' . $fileName . '.md';
        $this->logger && $this->logger->debug('Cache bypass "{dir}/{file}", source file "{src}".', ['dir' => $dirName, 'file' => $fileName, 'src' => $sourceFile]);

        if (
            !file_exists($sourceFile) ||
            !is_file($sourceFile) ||
            !is_readable($sourceFile)
        ) {
            $this->logger && $this->logger->error('Source file does not exist/is not readable.');
            $safeSourceFile = htmlspecialchars($sourceFile);
            return <<<HTML
            <h1>Error</h1>
            <p>Source file <code>$safeSourceFile</code> does not exist or is not readable.</p>
            HTML;
        }

        $raw = (string) file_get_contents($sourceFile);
        $this->logger && $this->logger->debug('Raw len = {len}', ['len' => strlen($raw)]);
        $md = $this->md2html($raw);
        $this->logger && $this->logger->debug('Md len = {len}', ['len' => strlen($md)]);
        return $md;
    }

    public function render(): void
    {
        if ($this->debug) {
            echo $this->debugPage();
            return;
        }

        $rawContent = null;
        if ($this->cache) {
            // TODO $cacheContent = null;
            $rawContent = $this->cache->get($this->contentDirname, $this->contentName);
        }

        if ($rawContent !== null) {
            echo $rawContent;
            return;
        }

        $rawContent = $this->cacheBypass(
            $this->contentDirname,
            $this->contentName,
        );
        // Hmmm... something is missing... sanitizer, maybe?
        echo $rawContent;
    }
}
