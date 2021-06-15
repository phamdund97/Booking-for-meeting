<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Department extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'departments';

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
