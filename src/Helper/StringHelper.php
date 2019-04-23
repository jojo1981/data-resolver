<?php
/*
 * This file is part of the jojo1981/data-resolver package
 *
 * Copyright (c) 2019 Joost Nijhuis <jnijhuis81@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed in the root of the source code
 */
namespace Jojo1981\DataResolver\Helper;

/**
 * @package Jojo1981\DataResolver\Helper
 */
final class StringHelper
{
    private function __construct()
    {
        // prevent getting an instance of this class
    }

    /**
     * @param string $text
     * @param bool $capitalizeFirstChar
     * @return string
     */
    public static function toCamelCase(string $text, bool $capitalizeFirstChar = false): string
    {
        if ($capitalizeFirstChar) {
            $text[0] = \strtoupper($text[0]);
        }

        return \preg_replace_callback(
            '/_([a-z])/',
            static function (array $matches): string {
                return \strtoupper($matches[1]);
            },
            $text
        );
    }

    /**
     * @param string $input
     * @return string
     */
    public static function camelToSnakeCase(string $input): string
    {
        if (0 === \preg_match('/[A-Z]/', $input)) {
            return $input;
        }

        return \strtolower(\preg_replace_callback(
            '/([a-z])([A-Z])/',
            static function (array $parts): string {
                return $parts[1] . '_' . \strtolower($parts[2]);
            },
            $input
        ));
    }
}