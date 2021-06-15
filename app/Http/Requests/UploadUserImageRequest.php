<?php

namespace App\Http\Requests;

class UploadUserImageRequest extends APIRequest
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
            'image' => 'required|image|dimensions:max_with=128,max_height=128|mimes:jpg,jpeg,png|max:5000'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image.required'   => trans('user.image_required'),
            'image.image'      => trans('user.image_image'),
            'image.dimensions' => trans('user.image_dimensions', ['width' => '128', 'height' => '128']),
            'image.mimes'      => trans('user.image_mimes', ['name' => 'jpg,jpeg,png']),
            'image.max'        => trans('user.image_max', ['name' => '5']),
        ];
    }
}
