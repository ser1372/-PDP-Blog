<?php
namespace App\Enum;

enum PostStatusEnum: string
{
    case MODERATION = 'moderation';
    case PUBLISHED = 'published';
}
