<?php

namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateUserRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'      => 'required|email|unique:users',
            'full_name'  => 'required|min:5|max:70',
            'department' => 'required|numeric',
            'password'   => 'required|min:8',
            'phone'      => 'numeric',
            'role'       => 'required|numeric',
            'image' => 'required|image|dimensions:max_with=128,max_height=128|mimes:jpg,jpeg,png|max:5000'
        ];
    }

    /**
     * Get the validation messages that apply to the request
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'      => trans('user.email_required'),
            'email.email'         => trans('user.email_email'),
            'email.unique'        => trans('user.email_unique'),
            'full_name.required'  => trans('user.full_name_required'),
            'full_name.min'       => trans('user.full_name_min', ['name' => '5']),
            'full_name.max'       => trans('user.full_name_max', ['name' => '70']),
            'department.required' => trans('user.department_required'),
            'department.numeric'  => trans('user.department_numeric'),
            'password.required'   => trans('user.password_required'),
            'password.min'        => trans('user.password_min', ['name' => '8']),
            'phone.numeric'       => trans('user.phone_numeric'),
            'role.required'       => trans('user.role_required'),
            'role.numeric'        => trans('user.role_numeric'),
            'image.required'   => trans('user.image_required'),
            'image.image'      => trans('user.image_image'),
            'image.dimensions' => trans('user.image_dimensions', ['width' => '128', 'height' => '128']),
            'image.mimes'      => trans('user.image_mimes', ['name' => 'jpg,jpeg,png']),
            'image.max'        => trans('user.image_max', ['name' => '5']),
        ];
    }


}
