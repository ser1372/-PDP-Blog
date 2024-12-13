<?php
namespace App\Enum;

enum RoleEnum: string
{
    case ADMIN = 'ROLE_ADMIN';
    case AUTHOR = 'ROLE_AUTHOR';
    case USER = 'ROLE_USER';
}
