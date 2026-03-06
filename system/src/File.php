<?php

declare(strict_types=1);

namespace Tommander\BlogSimple;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * @psalm-type BlogSimpleOneFileData = array{name: string, url: string, path: string, title: string, excerpt: string, mdate: int}
 * @psalm-type BlogSimpleFileList = array<string, array<string, BlogSimpleOneFileData>>
 */
final class File
{
    use LoggerAwareTrait;

    /** @var non-empty-string */
    public const FILETYPE_POST = 'post';
    /** @var non-empty-string */
    public const FILETYPE_PAGE = 'page';
    /** @var non-empty-string */
    public const FILETYPE_ERROR = 'error';

    public const CARD_HTML = 'html';
    public const CARD_MD_BIG = 'md-big';
    public const CARD_MD_SMALL = 'md-small';

    /** @var BlogSimpleFileList */
    public array $data = [];
    private bool $refreshed = false;
    /** @var non-empty-array<non-empty-string, non-empty-string> */
    public array $filesDirPaths;

    public function __construct(LoggerInterface|null $logger)
    {
        $logger && $this->setLogger($logger);
        $this->filesDirPaths = [
            static::FILETYPE_POST => Configuration::BLOG_DIR_POSTS,
            static::FILETYPE_PAGE => Configuration::BLOG_DIR_PAGES,
            static::FILETYPE_ERROR => Configuration::BLOG_DIR_ERRORS,
        ];

        $this->refreshData();
    }

    /**
     * @return BlogSimpleOneFileData|null
     */
    public function getItem(string $type, string $name): array|null
    {
        if (
            !isset($this->data[$type]) ||
            !isset($this->data[$type][$name])
        ) {
            return null;
        }
        return $this->data[$type][$name];
    }

    public function itemUrl(string $type, string $name): string
    {
        return Main::homeUrl([$type => $name]);
    }

    /**
     * @param non-empty-string $type
     */
    private function addData(string $type, string $filename): void
    {
        $name = substr($filename, 0, strlen($filename) - 3);
        $path = $this->filesDirPaths[$type] . $filename;
        $content = (string) file_get_contents($path);
        $title = 'No Title';
        if (preg_match('/\n?#\s*(?<title>[^\r\n\0$]+)\s*/', $content, $matches) === 1) {
            $title = $matches['title'];
        }
        $excerpt = '';
        if ($type === static::FILETYPE_POST) {
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
        (!isset($this->data[$type]) && ($this->data[$type] = []));
        $this->data[$type][$name] = $item;
    }

    private function refreshData(): void
    {
        if ($this->refreshed) {
            return;
        }
        $this->refreshed = true;

        $this->data = [];
        foreach ([static::FILETYPE_POST, static::FILETYPE_PAGE, static::FILETYPE_ERROR] as $type) {
            $lst = scandir($this->filesDirPaths[$type], SCANDIR_SORT_NONE);
            if (!is_array($lst)) {
                $lst = [];
            }

            $this->data[$type] = [];
            foreach ($lst as $file) {
                if (in_array($file, ['.', '..'], true) || !str_ends_with($file, '.md')) {
                    continue;
                }
                $this->addData($type, $file);
            }

            uasort($this->data[$type], function (mixed $a, mixed $b) use ($type): int {
                if (!is_array($a) || !is_array($b)) {
                    return 0;
                }

                return match (true) {
                    ($type === static::FILETYPE_PAGE && $a['name'] === 'home') => -1,
                    ($type === static::FILETYPE_PAGE && $b['name'] === 'home') => 1,
                    ($type === static::FILETYPE_PAGE && $a['name'] === 'list') => -1,
                    ($type === static::FILETYPE_PAGE && $b['name'] === 'list') => 1,
                    ($type === static::FILETYPE_PAGE && $a['name'] === 'archive') => -1,
                    ($type === static::FILETYPE_PAGE && $b['name'] === 'archive') => 1,
                    default => strcasecmp(
                        is_string($a['name']) ? $a['name'] : '',
                        is_string($b['name']) ? $b['name'] : '',
                    ),
                };
            });
        }
    }

    public function listData(string $type, string $style, bool $postsArchived = false): string
    {
        $this->refreshData();
        $res = '';
        foreach ($this->data[$type] as /*$name => */$data) {
            // if (!is_array($data)) {
            //     $this->logger->warning('Strange value for a file data "{val}"', ['val' => var_export($data, true)]);
            //     continue;
            // }

            if ($type === static::FILETYPE_POST) {
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

    public static function datacard(string $type, string $style, string $url, string $title, string $excerpt, int $mdate): string
    {
        $format = match ($style) {
            static::CARD_HTML => '<li><a class="page" href="%1$s">%2$s</a></li>',
            static::CARD_MD_SMALL => '[%2$s](%1$s)  ' . PHP_EOL,
            static::CARD_MD_BIG => match ($type) {
                static::FILETYPE_PAGE => <<<'MD'
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
