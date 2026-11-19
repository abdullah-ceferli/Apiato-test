<?php

namespace App\Containers\AppSection\TaskManagement\Models;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Task extends Model
{
    protected $fillable = ['title', 'description', 'is_completed'];

    protected $attributes = ['is_completed' => false, 'description' => null];

    protected $casts = ['is_completed' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
