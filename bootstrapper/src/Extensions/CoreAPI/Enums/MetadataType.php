<?php

namespace __PLUGIN__\Extensions\CoreAPI\Enums;

enum MetadataType: string
{
    case POST = 'post';
    case USER = 'user';
    case TERM = 'term';
    case COMMENT = 'comment';
    case BLOG = 'blog';
}
