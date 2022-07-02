<?php

namespace App\Engine\Checks;

use App\Enums\CharacterEnum;

class FairyCharacterCheck extends CharacterCheck
{
    function getCharacter(): CharacterEnum
    {
        return CharacterEnum::FAIRY();
    }
}
