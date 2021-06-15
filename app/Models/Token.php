<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Token
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $hash_token
 * @property int $type
 * @property string $created_at
 * @property string $updated_at
 */
class Token extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'hash_token',
        'type',
    ];

    protected $table = 'tokens';
}
