<?php

namespace App\Enum;

enum BlogArticleStatus: string
{
    case PUBLISHED = 'published';
    case DRAFT = 'draft';
    case DELETED = 'deleted';
}