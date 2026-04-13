<?php

namespace Modules\Space\Enums;

enum SpaceTypeEnum: string
{
    case Apartment = 'apartment';
    case Room      = 'room';
    case Studio    = 'studio';
    case Villa     = 'villa';
    case House     = 'house';
}
