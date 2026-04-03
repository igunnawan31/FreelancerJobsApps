<?php

namespace App\Enums\UserEnums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case COORDINATOR = 'coordinator';
    case FREELANCER = 'freelancer';
    case CLIENT = 'client';
}