<?php


namespace App\Enum;


final class SpotifyUserRepeatMode
{

    public const TRACK = 'track';
    public const CONTEXT = 'context';
    public const OFF = 'off';

    public static function isValueValid(string $value): bool
    {
        return in_array($value, self::getAvailableValues(), true);
    }

    public static function getAvailableValues(): array
    {
        return [self::TRACK, self::CONTEXT, self::OFF];
    }
}
