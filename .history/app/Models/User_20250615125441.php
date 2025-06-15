<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email'
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_user', 'user_id', 'category_id');
    }
}
