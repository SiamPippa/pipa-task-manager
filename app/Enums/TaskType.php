<?php

namespace App\Enums;

class TaskType
{
    public const FEATURE = 'feature';

    public const BUG = 'bug';

    public const CHANGE_REQUEST = 'change_request';

    public static function labels(): array
    {
        return [
            self::FEATURE => 'Feature',
            self::BUG => 'Bug',
            self::CHANGE_REQUEST => 'Change Request',
        ];
    }

    public static function label(string $type): string
    {
        return self::labels()[$type] ?? 'Unknown';
    }

    public static function values(): array
    {
        return array_keys(self::labels());
    }
}
