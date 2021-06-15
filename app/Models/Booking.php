<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Meeting;

/**
 * Class Booking
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string $time_start
 * @property string $time_end
 * @property string $participants
 * @property string $description
 * @property int $user_id
 * @property int $meeting_id
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'booking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'time_start',
        'time_end',
        'participants',
        'description',
        'user_id',
        'meeting_id',
        'status'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::Class);
    }
}
