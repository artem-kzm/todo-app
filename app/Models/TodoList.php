<?php

namespace App\Models;

use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string title
 * @property int created_by
 *
 * @property TodoListItem[]|Eloquent\Collection items
 *
 * @method static static|Eloquent\Builder belongsToUser(User $user)
 */
class TodoList extends Model
{
    use SoftDeletes;
    use HasFactory;

    public function createdBy(): Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeBelongsToUser(Eloquent\Builder $query, User $user): Eloquent\Builder
    {
        return $query->where('created_by', $user->id);
    }

    public function items(): Relations\HasMany
    {
        return $this->hasMany(TodoListItem::class);
    }
}
