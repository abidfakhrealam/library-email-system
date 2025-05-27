<?php

namespace App\Traits;

use App\Models\EmailTemplate;

trait HasEmailTemplates
{
    public function getTemplateFor(string $purpose): ?EmailTemplate
    {
        return EmailTemplate::where('purpose', $purpose)
            ->where(function($query) {
                $query->whereNull('tag_id')
                    ->orWhere('tag_id', $this->tags->pluck('id'));
            })
            ->orderBy('tag_id', 'desc') // Prefer tag-specific templates
            ->first();
    }

    public function applyTemplate(string $purpose): string
    {
        $template = $this->getTemplateFor($purpose);

        if (!$template) {
            return '';
        }

        return str_replace(
            ['{{subject}}', '{{from}}', '{{date}}'],
            [$this->subject, $this->from_name, $this->received_at->format('Y-m-d')],
            $template->content
        );
    }
}
