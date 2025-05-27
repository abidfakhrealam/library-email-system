<?php

namespace App\Services;

use App\Models\AssignedEmail;
use App\Models\AutomationRule;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    public function processNewEmail(AssignedEmail $email): void
    {
        $rules = AutomationRule::where('is_active', true)->get();

        foreach ($rules as $rule) {
            try {
                if ($this->matchesConditions($email, $rule->conditions)) {
                    $this->applyActions($email, $rule->actions);
                }
            } catch (\Exception $e) {
                Log::error("Automation rule {$rule->id} failed: " . $e->getMessage());
            }
        }
    }

    protected function matchesConditions(AssignedEmail $email, array $conditions): bool
    {
        foreach ($conditions as $condition) {
            $field = $condition['field'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            $emailValue = $this->getEmailFieldValue($email, $field);

            if (!$this->compare($emailValue, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function applyActions(AssignedEmail $email, array $actions): void
    {
        foreach ($actions as $action) {
            switch ($action['type']) {
                case 'assign':
                    $email->update(['assigned_to' => $action['user_id']]);
                    break;
                    
                case 'add_tag':
                    $email->tags()->syncWithoutDetaching($action['tag_id']);
                    break;
                    
                case 'change_status':
                    $email->update(['status' => $action['status']]);
                    break;
                    
                case 'set_priority':
                    $email->update(['priority' => $action['priority']]);
                    break;
            }
        }
    }

    protected function getEmailFieldValue(AssignedEmail $email, string $field)
    {
        return match($field) {
            'subject' => strtolower($email->subject),
            'from' => strtolower($email->from_email),
            'body' => strtolower($email->body_preview),
            default => $email->{$field}
        };
    }

    protected function compare($value, string $operator, $comparison): bool
    {
        return match($operator) {
            'equals' => $value == $comparison,
            'contains' => str_contains($value, $comparison),
            'starts_with' => str_starts_with($value, $comparison),
            'ends_with' => str_ends_with($value, $comparison),
            'greater_than' => $value > $comparison,
            'less_than' => $value < $comparison,
            default => false
        };
    }
}
