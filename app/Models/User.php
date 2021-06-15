<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property string $phone_number
 * @property int $role_id
 * @property int $department_id
 * @property boolean $status
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    const TYPE_STATUS = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'full_name',
        'password',
        'department_id',
        'phone_number',
        'role_id',
        'image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes has default value.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * Get the bookings for the user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Get the tokens for the user.
     */
    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getUsers($data)
    {
        $query = User::where('status', User::TYPE_STATUS);

        if (!empty($data['filter_name'])) {
            $query->where('full_name', 'LIKE', "%{$data['filter_name']}%");
        }

        $sortBy = [
            'name' => 'full_name',
            'created_at' => 'created_at'
        ];

        if (!empty($data['sort']) && isset($sortBy[$data['sort']])) {
            $sort = $sortBy[$data['sort']];
        } else {
            $sort = $sortBy['created_at'];
        }

        if (!empty($data['order']) && $data['order'] === 'desc') {
            $order = 'desc';
        } else {
            $order = 'asc';
        }

        $query->orderBy($sort, $order);

        if (!empty($data['limit'])) {
            if ($data['limit'] < 1) {
                $data['limit'] = 10;
            }
        } else {
            $data['limit'] = 10;
        }

        if (empty($data['page'])) {
            $data['page'] = 1;
        }

        return $query->paginate($data['limit'], ['*'], 'page', $data['page']);
    }

}
