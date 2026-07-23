<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'priority', 'due_date', 'assignee', 'project', 'tags', 'user_id'];

    protected $casts = [
        'due_date' => 'date',
        'tags' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function scopeStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopePriority($query, ?string $priority)
    {
        return $priority ? $query->where('priority', $priority) : $query;
    }

    public function scopeSearch($query, ?string $search)
    {
        return $search
            ? $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('project', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%")
                    ->orWhere('assignee', 'like', "%{$search}%");
            })
            : $query;
    }
}
