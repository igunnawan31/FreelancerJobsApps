<?php

namespace App\Enums\RatingEnums;

enum RatingType: string
{
    case COMMUNICATION = 'communication';
    case QUALITY = 'quality';
    case TIMELINESS = 'timeliness';
    case PROFESSIONALISM = 'professionalism';
}