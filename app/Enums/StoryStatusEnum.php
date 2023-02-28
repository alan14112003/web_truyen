<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class StoryStatusEnum extends Enum
{
    const UNFINISHED = 0;
    const COMPLETE = 1;

    public static function getArrayView() {
        return [
            'Chưa hoàn' => self::UNFINISHED,
            'Đã hoàn' => self::COMPLETE,
        ];
    }

    public static function checkStatusByValue($value): bool
    {
        if ($value === self::UNFINISHED)
            return false;
        return true;
    }

    public static function getNameByValue($value) {
        return array_search($value, self::getArrayView(), true);
    }
}
