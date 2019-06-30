<?php

namespace App\GraphQL\Type;

class DateTimeType
{
    public static function serialize(\DateTimeInterface $value): string
    {
        return $value->format('Y-m-d H:i:s');
    }

    public static function parseValue($value): \DateTimeInterface
    {
        return new \DateTimeImmutable($value);
    }

    public static function parseLiteral($valueNode): string
    {
        return new \DateTimeImmutable($valueNode->value);
    }
}
