<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => AsArrayObject::class
    ];

    /**
     * Reference to the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reference to the day the action was performed on.
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }
}
