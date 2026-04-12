<?php

namespace App\Enums\ProjectEnums;

enum ProjectStatus: string
{
    case STATUS_OPEN = 'open';
    case STATUS_REQUESTED_BY_ADMIN = 'requested_by_admin';
    case STATUS_REQUESTED_BY_FREELANCER = 'requested_by_freelancer';
    case STATUS_RUNNING = 'running';
    case STATUS_REVISION = 'revision';
    case STATUS_COMPLETED = 'completed';
    case STATUS_DONE = 'done';
}