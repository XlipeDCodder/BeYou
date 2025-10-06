<?php

namespace Beyou\Catalog\Domain\Enums;

enum VideoStatus: string
{
    case UPLOADING = 'uploading';
    case PROCESSING = 'processing';
    case PUBLISHED = 'published';
    case FAILED = 'failed';
    case PRIVATE = 'private';
}