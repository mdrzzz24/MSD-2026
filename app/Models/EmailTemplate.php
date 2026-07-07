<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'subject',
        'html_content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeApproval($query)
    {
        return $query->where('type', 'approval');
    }

    public function scopeRejection($query)
    {
        return $query->where('type', 'rejection');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Replace placeholders in the template with actual data.
     */
    public function render(array $data = []): string
    {
        $content = $this->html_content;

        foreach ($data as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }
}
