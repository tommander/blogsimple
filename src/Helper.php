<?php

namespace Tommander\BlogSimple;

class Helper
{
    public static function esclang(string $lang): string
    {
        return preg_replace('/[^A-Za-z0-9-]/', '', $lang) ?? '';
    }
}
