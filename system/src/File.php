<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Represents all public pages, posts and errors.
 *
 * @psalm-type BlogSimpleOneFileData = array{name: string, url: string, path: string, title: string, excerpt: string, mdate: int}
 * @psalm-type BlogSimpleFileList = array<string, array<string, BlogSimpleOneFileData>>
 * @psalm-type CardStyle = 'html'|'md-big'|'md-small'
 */
final class File
{
    use LoggerAwareTrait;

    public const CARD_HTML = 'html';
    public const CARD_MD_BIG = 'md-big';
    public const CARD_MD_SMALL = 'md-small';
    public const CARD_STYLES = [self::CARD_HTML, self::CARD_MD_BIG, self::CARD_MD_SMALL];

    /** @var BlogSimpleFileList */
    public array $data = [];
    private bool $refreshed = false;

    public function __construct(LoggerInterface|null $logger)
    {
        $logger && $this->setLogger($logger);
        $this->refreshData();
    }

    /**
     * Get the data of the specified file.
     *
     * @param FileTypeEnum $type File type (post/page/error)
     * @param non-empty-string $name File name (without extension)
     *
     * @return BlogSimpleOneFileData|null
     */
    public function getItem(FileTypeEnum $type, string $name): array|null
    {
        if (
            !isset($this->data[$type->value]) ||
            !isset($this->data[$type->value][$name])
        ) {
            return null;
        }
        return $this->data[$type->value][$name];
    }

    /**
     * Get the URL that leads to particular MD file
     *
     * @param FileTypeEnum $type File type (post/page/error)
     * @param non-empty-string $name File name (without extension)
     */
    public function itemUrl(FileTypeEnum $type, string $name): string
    {
        return Main::homeUrl([$type->value => $name]);
    }

    /**
     * Data MD file data to the instance property
     *
     * @param FileTypeEnum $type
     * @param non-empty-string $filename
     */
    private function addData(FileTypeEnum $type, string $filename): void
    {
        $trimmed = trim($filename);
        if (empty($trimmed) || strlen($trimmed) < 4) {
            $this->logger && $this->logger->error('Incorrect file name "{name}".', ['name' => $filename]);
            return;
        }

        /** @var non-empty-string */
        $name = substr($trimmed, 0, strlen($trimmed) - 3);
        $path = $type->path() . $name . '.md';
        $content = (string) file_get_contents($path);
        $title = 'No Title';
        if (preg_match('/\n?#\s*(?<title>[^\r\n\0$]+)\s*/', $content, $matches) === 1) {
            $title = $matches['title'];
        }
        $excerpt = '';
        if ($type === FileTypeEnum::Posts) {
            $firstPara = '';
            if (preg_match('/\n?#.+?\n\n(?<para1>.+?)(?:\s*\n\n|\s*$)/', $content, $matches) === 1) {
                $firstPara = $matches['para1'];
            }
            $excerpt = (strlen($firstPara) > 200) ? substr($firstPara, 0, 199) . '&hellip;' : $firstPara;
        }
        $item = [
            'name' => $name,
            'url' => $this->itemUrl($type, $name),
            'path' => $path,
            'title' => $title,
            'excerpt' => $excerpt,
            'mdate' => (int) filemtime($path),
        ];
        (!isset($this->data[$type->value]) && ($this->data[$type->value] = []));
        $this->data[$type->value][$name] = $item;
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

        $this->data = [];
        foreach (FileTypeEnum::cases() as $type) {
            $path = $type->path();
            $lst = scandir($path, SCANDIR_SORT_NONE);
            if (!is_array($lst)) {
                $lst = [];
            }

            $this->data[$type->value] = [];
            foreach ($lst as $file) {
                if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md') || empty($file)) {
                    continue;
                }
                /** @var non-empty-string $file */
                $this->addData($type, $file);
            }

            uasort($this->data[$type->value], function (mixed $a, mixed $b) use ($type): int {
                if (!is_array($a) || !is_array($b)) {
                    return 0;
                }

                return match (true) {
                    ($type === FileTypeEnum::Pages && $a['name'] === 'home') => -1,
                    ($type === FileTypeEnum::Pages && $b['name'] === 'home') => 1,
                    ($type === FileTypeEnum::Pages && $a['name'] === 'list') => -1,
                    ($type === FileTypeEnum::Pages && $b['name'] === 'list') => 1,
                    ($type === FileTypeEnum::Pages && $a['name'] === 'archive') => -1,
                    ($type === FileTypeEnum::Pages && $b['name'] === 'archive') => 1,
                    default => strcasecmp(
                        is_string($a['name']) ? $a['name'] : '',
                        is_string($b['name']) ? $b['name'] : '',
                    ),
                };
            });
        }
    }

    /**
     * List all files of a specific type (page/post/error) as HTML datacards appended to a continuous
     *
     * @param FileTypeEnum $type File type (post/page/error)
     * @param CardStyle $style Card style (html/md-big/md-small)
     * @param bool $postsArchived Show only archived posts (true) or only non-archived (false). No effect on pages/errors
     *
     * @return string Datacards as HTML
     */
    public function listData(FileTypeEnum $type, string $style, bool $postsArchived = false): string
    {
        $this->refreshData();
        $res = '';
        foreach ($this->data[$type->value] as /*$name => */$data) {
            if ($type === FileTypeEnum::Posts) {
                $isArchived = ((time() - ($data['mdate'] ?? 0)) > Configuration::BLOG_ARCHIVE_TIME);
                if ($postsArchived xor $isArchived) {
                    continue;
                }
            }

            $this->logger && $this->logger->debug('Datacard {type} "{title}"', ['type' => $type, 'title' => $data['title']]);
            $res .= static::datacard(
                $type,
                $style,
                $data['url'],
                $data['title'],
                '',
                $data['mdate'],
            );
        }
        return $res;
    }

    /**
     * Creates an HTML "card" (styled div container with data inside) for a
     *
     * @param FileTypeEnum $type File type (post/page/error)
     * @param CardStyle $style Card style (html/md-big/md-small)
     * @param string $url Url for that file
     * @param string $title Title of the document
     * @param string $excerpt Excerpt of the document (this class uses it only for posts, but it can be activated for other file types)
     * @param int $mdate File modified time (unix timestamp)
     *
     * @return string HTML "datacard" for the file
     */
    public static function datacard(FileTypeEnum $type, string $style, string $url, string $title, string $excerpt, int $mdate): string
    {
        $format = match ($style) {
            static::CARD_HTML => '<li><a class="page" href="%1$s">%2$s</a></li>',
            static::CARD_MD_SMALL => '[%2$s](%1$s)  ' . PHP_EOL,
            static::CARD_MD_BIG => match ($type) {
                FileTypeEnum::Pages => <<<'MD'
                    > **[%2$s](%1$s)**



                    MD,
                default => <<<'MD'
                    > **[%2$s](%1$s)**\
                    > <small>🗓️ %4$s</small>
                    >
                    > %5$s



                    MD,
            },
        };
        return sprintf(
            $format,
            $url,
            $title,
            date('d.m.Y H:i:s', $mdate),
            Helper::niceInterval(time() - $mdate),
            $excerpt,
        );
    }
}
