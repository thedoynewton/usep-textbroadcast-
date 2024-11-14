<?php

namespace App\Services;

use App\Models\MessageTemplate;
use App\Models\MessageCategory;

class TemplatesService
{
    public function getMessageTemplates()
    {
        return MessageTemplate::paginate(10);
    }
    public function getMessageCategories()
    {
        return MessageCategory::paginate(10);
    }

}
