<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'category_user', 'category_id', 'user_id');
    }
}
