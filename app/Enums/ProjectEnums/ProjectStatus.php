<?php

namespace App\Enums\ProjectEnums;

enum ProjectStatus: string
{
    case STATUS_OPEN = 'open';
    case STATUS_HOLD = 'hold';
    case STATUS_RUNNING = 'running';
    case STATUS_REVISION = 'revision';
    case STATUS_COMPLETED = 'completed';
}