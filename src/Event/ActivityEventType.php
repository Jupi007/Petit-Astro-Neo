<?php

declare(strict_types=1);

namespace App\Event;

enum ActivityEventType: string
{
    case Created = 'created';
    case Modified = 'modified';
    case Removed = 'removed';
    case Restored = 'restored';
    case Published = 'published';
    case Unpublished = 'unpublished';
    case DraftRemoved = 'draft_removed';
    case TranslationAdded = 'translation_added';
    case TranslationCopied = 'translation_copied';
    case TranslationRemoved = 'translation_removed';
}
