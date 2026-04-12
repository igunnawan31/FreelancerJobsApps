<?php

namespace App\Enums\UserEnums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case FREELANCER = 'freelancer';
    case CLIENT = 'client';
}