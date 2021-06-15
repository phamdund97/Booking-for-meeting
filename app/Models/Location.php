<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Location
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 */
class Location extends Model
{
    use HasFactory;

    public $table='locations';

    public $fillable = [
        'name'
    ];
    public function getMeeting(){
        return $this->hasMany(Meeting::class);
    }

}
