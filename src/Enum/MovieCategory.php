<?php

namespace App\Enum;

enum MovieCategory: string
{
    case ACTION = 'akcja';
    case COMEDY = 'komedia';
    case DRAMA = 'dramat';
    case HORROR = 'horror';
    case ANIMATION = 'animacja';
    case FAMILY = 'familijny';
    case SCI_FI = 'sci-fi';
    case DOCUMENTARY = 'dokumentalny';
    case OTHER = 'inne';

    public function label(): string
    {
        return match ($this) {
            self::ACTION => 'Akcja',
            self::COMEDY => 'Komedia',
            self::DRAMA => 'Dramat',
            self::HORROR => 'Horror',
            self::ANIMATION => 'Animacja',
            self::FAMILY => 'Familijny',
            self::SCI_FI => 'Sci-Fi',
            self::DOCUMENTARY => 'Dokumentalny',
            self::OTHER => 'Inne',
        };
    }

    public static function choices(): array
    {
        $choices = [];

        foreach (self::cases() as $case) {
            $choices[$case->label()] = $case;
        }

        return $choices;
    }

    public static function labels(): array
    {
        $labels = [];

        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }

        return $labels;
    }
}