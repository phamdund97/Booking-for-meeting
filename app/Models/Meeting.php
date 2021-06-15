<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'meeting';

    /**
     * Class Post
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property int $capacity
     * @property string $description
     * @property string $image
     * @property int $location_id
     * @property int $status
     * @property string $created_at
     * @property string $updated_at
     */
    protected $fillable = [
        'name',
        'capacity',
        'description',
        'location_id',
        'image',
        'status',
    ];

    /**
     * Get the bookings for the meeting.
     */
    public function booking()
    {
        return $this->hasMany(Booking::class);
    }

    public function getLocation(){
        return $this->belongsTo(Location::class,'location_id','id','locations');
    }

    /**
     * Get the all data of meeting.
     */
    public static function scopeMeetingAll()
    {
        return self::has('getLocation')
        ->join('booking', 'booking.meeting_id', '=', 'meeting.id')
        ->join('locations', 'meeting.location_id', '=', 'locations.id')
        ->select('meeting.id as meeting_id', 'meeting.name as meeting_name',
            'meeting.capacity as meeting_capacity', 'meeting.description as meeting_description',
            'meeting.image as meeting_image', 'booking.time_start', 'booking.time_end',
            'locations.name as location_name');
    }

    /**
     * Get the data of meeting by location id.
     */
    public static function scopeMeetingById($id)
    {
        return self::where('location_id', $id)
        ->join('booking', 'booking.meeting_id', '=', 'meeting.id')
        ->join('locations', 'meeting.location_id', '=', 'locations.id')
        ->select('meeting.id as meeting_id', 'meeting.name as meeting_name',
            'meeting.capacity as meeting_capacity', 'meeting.description as meeting_description',
            'meeting.image as meeting_image', 'booking.time_start', 'booking.time_end',
            'locations.name as location_name');
    }

    /**
     * Get the data of booking and meeting info by meeting id.
     */
    public static function getDataByMeeting($id)
    {
        return self::join('booking', 'booking.meeting_id', '=', 'meeting.id')
        ->where('meeting.id', '=', $id)
        ->select('meeting.id as meeting_id', 'meeting.name as meeting_name',
            'meeting.capacity as meeting_capacity', 'meeting.image as meeting_image',
            'booking.time_start', 'booking.time_end');
    }
}
