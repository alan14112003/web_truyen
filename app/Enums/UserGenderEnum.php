<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;


final class UserGenderEnum extends Enum
{
    public const FEMALE = 0;
    public const MALE = 1;
    public const LGBT = 2;
    public const SECRET = 3;


    public static function getArrayView() {
        return [
            'Nữ' => self::FEMALE,
            'Nam' => self::MALE,
            'LGBT' => self::LGBT,
            'Bí mật' => self::SECRET,
        ];
    }
    public static function getNameByValue($value) {
        return array_search($value, self::getArrayView(), true);
    }
}
