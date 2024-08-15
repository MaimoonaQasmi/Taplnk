<?php

namespace App\Http\Requests;

use App\Models\Vcard;
use Illuminate\Foundation\Http\FormRequest;

class CreateVcardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = Vcard::$rules;
        $rules['profile_img'] = 'mimes:jpg,bmp,png,apng,avif,jpeg,';
        $rules['cover_img'] = 'mimes:jpg,bmp,png,apng,avif,jpeg,mp4,mpeg,ogg,webm,3gp,mov,flv,avi,wmv,ts|max:10240';
        return $rules;
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'url_alias.string' => 'The URL Alias field is required.',
            'name.string' => 'The name field is required.',
            'url_alias.min' => 'The URL Alias must be at least 6 characters.',
            'url_alias.max' => 'The URL Alias not be grater then 24 characters.',
            'url_alias.unique' => 'The URL Alias must unique.',
            'cover_img.max' => 'The cover video must not be greater than 10 MB.',
        ];
    }
}
