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
 * @property int todo_list_id
 * @property bool is_done
 *
 * @method static static|Eloquent\Builder whereList(TodoList $todoList)
 */
class TodoListItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'is_done' => 'boolean'
    ];

    public function todoList(): Relations\BelongsTo
    {
        return $this->belongsTo(TodoList::class);
    }

    public static function createForList(TodoList $todoList, string $title): static
    {
        $todoListItem = new static();
        $todoListItem->title = $title;
        $todoListItem->todo_list_id = $todoList->id;
        $todoListItem->save();

        return $todoListItem;
    }

    public function scopeWhereList(Eloquent\Builder $query, TodoList $todoList): Eloquent\Builder
    {
        return $query->where('todo_list_id', $todoList->id);
    }

    public function markAsDone(): void
    {
        $this->is_done = true;
        $this->save();
    }
}
