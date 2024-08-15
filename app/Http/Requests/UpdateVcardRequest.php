<?php

namespace App\Http\Requests;

use App\Models\Vcard;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVcardRequest extends FormRequest
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
        $rules['url_alias'] = $rules['url_alias'].','.$this->route('vcard')->id;
        $rules['current_password'] = 'nullable|min:6';
        $rules['profile_img'] = 'mimes:jpg,bmp,png,apng,avif,jpeg,';
        $rules['cover_img'] = 'mimes:jpg,bmp,png,apng,avif,jpeg,mp4,mpeg,ogg,webm,3gp,mov,flv,avi,wmv,ts|max:10240';
        $rules['qr_code_download_size'] = ['numeric', 'in:100,200,300,400,500'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'url_alias.string' => 'The URL Alias field is required.',
            'name.string' => 'The name field is required.',
            'url_alias.min' => 'The URL Alias must be at least 6 characters.',
            'url_alias.max' => 'The URL Alias not be grater then 24 characters.',
            'url_alias.unique' => 'The URL Alias must unique.',
            'occupation.string' => 'The occupation field is required.',
            'description.string' => ' The description field is required.',
            'first_name.string' => 'The first name field is required.',
            'last_name.string' => 'The last name field is required.',
            'is_paid' => 'You don\'t make a paid appointment, because you have not set credentials in settings.',
            'cover_img.max' => 'The cover video must not be greater than 10 MB.',
        ];
    }
}
