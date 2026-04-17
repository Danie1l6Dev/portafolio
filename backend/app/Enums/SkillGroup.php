<?php

namespace App\Enums;

enum SkillGroup: string
{
    case Backend = 'Backend';
    case Frontend = 'Frontend';
    case Database = 'Base de datos';
    case DevOps = 'DevOps';
    case Tools = 'Tools';

    public static function values(): array
    {
        return array_map(
            static fn (self $group) => $group->value,
            self::cases()
        );
    }
}

