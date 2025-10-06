<?php

namespace Beyou\Catalog\Domain\Enums;

enum VideoVisibility: string
{
    case PUBLIC = 'public';
    case SUBSCRIBERS_ONLY = 'subscribers_only';
    case PRIVATE = 'private';
}