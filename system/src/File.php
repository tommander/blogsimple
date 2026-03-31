<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

/**
 * Represents all public pages and posts.
 *
 * @psalm-type BlogSimpleOneFileData = array{url: string, path: string, title: string, excerpt: string, mdate: int, pin?: bool, archived?: bool}
 * @psalm-type BlogSimpleFileList = array<string, BlogSimpleOneFileData>
 * @psalm-type CardStyle = 'html'|'md'
 */
final class File
{
    public const CARD_HTML = 'html';
    public const CARD_MD = 'md';

    /** @var BlogSimpleFileList */
    public array $data = [];
    private bool $refreshed = false;

    public function __construct()
    {
        $this->refreshData();
    }

    public static function homeUrl(array $query = []): string
    {
        return Configuration::SITE_URL . '/index.php' . ((count($query) > 0) ? '?' . http_build_query($query) : '');
    }

    public static function niceInterval(int $seconds): string
    {
        $prepend = (($seconds < 0) ? '-' : '');
        ($seconds < 0) && ($seconds *= -1);
            $text = match (true) {
                $seconds < 60 => sprintf('%d sec', $seconds),
                $seconds < 3600 => sprintf('%.0f min', intdiv($seconds, 60)),
                $seconds < 86400 => sprintf('%.0f hr', intdiv($seconds, 3600)),
                $seconds < (2 * 86400) => '1 day',
                $seconds >= (2 * 86400) => sprintf('%.0f days', intdiv($seconds, 86400)),
            };
        return $prepend . $text;
    }

    /**
     * Get the data of the specified file.
     *
     * @param non-empty-string $name File name (without extension)
     *
     * @return BlogSimpleOneFileData|null
     */
    public function getItem(string $name): array|null
    {
        return $this->data[$name];
    }

    public function hasItem(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Read all MD files
     */
    private function refreshData(): void
    {
        if ($this->refreshed) {
            return;
        }
        $this->refreshed = true;

        $thedata = [
            'home' => [
                'title' => 'Homepage',
                'excerpt' => '',
                'pin' => true,
            ],
            'list' => [
                'title' => 'Articles',
                'excerpt' => '',
                'pin' => true,
            ],
            'archive' => [
                'title' => 'Archive',
                'excerpt' => '',
                'pin' => true,
            ],
            'about' => [
                'title' => 'About me',
                'excerpt' => '',
                'pin' => true,
            ],

            'bad-ass' => [
                'title' => 'Bad Ass',
                'excerpt' => '',
                'pin' => false,
                'archived' => false,
            ],
        ];

        foreach ($thedata as $name => &$data) {
            $data['url'] = self::homeUrl(['article' => $name]);
            $data['path'] = Configuration::BLOG_DIR_ARTICLES . $name . '.md';
            $data['mdate'] = (int) filemtime($data['path']);
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->data = $thedata;
    }

    /**
     * List all files of a specific type (page/post) as HTML datacards appended to a continuous
     *
     * @param CardStyle $style Card style (html/md-big/md-small)
     * @param bool $postsArchived Show only archived posts (true) or only non-archived (false). No effect on pages.
     *
     * @return string Datacards as HTML
     */
    public function listData(string $style, bool $postsArchived = false, int|null $limit = null): string
    {
        $counter = 0;

        $tempData = $this->data;
        if ($style !== static::CARD_HTML) {
            uasort(
                $tempData,
                /**
                 * @param BlogSimpleOneFileData $a
                 * @param BlogSimpleOneFileData $b
                 */
                fn ($a, $b) => (($a['mdate'] === $b['mdate']) ? strcasecmp($a['title'], $b['title']) : (-1 * ($a['mdate'] <=> $b['mdate'])))
            );
        }

        $res = '';
        foreach ($tempData as /*$name => */$data) {
            if (is_int($limit)) {
                if ($counter >= $limit) {
                    break;
                }
                $counter += 1;
            }

            $isArchived = ((time() - ($data['mdate'] ?? 0)) > Configuration::BLOG_ARCHIVE_TIME);
            if ($postsArchived xor $isArchived) {
                continue;
            }

            if ($style === static::CARD_HTML && (!isset($data['pin']) || $data['pin'] !== true)) {
                continue;
            }

            if ($style !== static::CARD_HTML && (isset($data['pin']) && $data['pin'] === true)) {
                continue;
            }

            $res .= static::datacard(
                $style,
                $data['url'],
                $data['title'],
                $data['excerpt'],
                $data['mdate'],
            );
        }
        return "<div>\n\n" . $res . "\n\n</div>";
    }

    /**
     * Creates an HTML "card" (styled div container with data inside) for a
     *
     * @param CardStyle $style Card style (html/md-big/md-small)
     * @param string $url Url for that file
     * @param string $title Title of the document
     * @param string $excerpt Excerpt of the document (this class uses it only for posts, but it can be activated for other file types)
     * @param int $mdate File modified time (unix timestamp)
     *
     * @return string HTML "datacard" for the file
     */
    public static function datacard(string $style, string $url, string $title, string $excerpt, int $mdate): string
    {
        $format = match ($style) {
            static::CARD_HTML => '<li><a class="page" href="%1$s">%2$s</a></li>',
            static::CARD_MD => <<<'MD'
                > **[%2$s](%1$s)**\
                > <small>🗓️ %4$s</small>
                >
                > %5$s



                MD,
        };
        return sprintf(
            $format,
            $url,
            $title,
            date('d.m.Y H:i:s', $mdate),
            self::niceInterval(time() - $mdate),
            $excerpt,
        );
    }
}
