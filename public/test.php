<?php

declare(strict_types=1);

/** @var int */
$int = 1;
/** @var float */
$flt = 1.234;

/**
 * @psalm-suppress InvalidArgument https://github.com/vimeo/psalm/issues/11721
 */
echo sprintf('%.*f', $int, $flt);
