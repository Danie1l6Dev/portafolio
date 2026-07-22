<?php

declare(strict_types=1);

namespace App\Enums;

enum AchievementType: string
{
    case Hackathon = 'hackathon';
    case Certification = 'certification';
    case Award = 'award';
    case Recognition = 'recognition';

    public function label(): string
    {
        return match ($this) {
            self::Hackathon => 'Hackathon',
            self::Certification => 'Certificación',
            self::Award => 'Premio',
            self::Recognition => 'Reconocimiento',
        };
    }
}
