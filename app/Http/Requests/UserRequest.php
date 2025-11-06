<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gerekirse burada yetkilendirme kontrolü yapılabilir.
        return true;
    }

    public function rules(): array
    {
        /**
         * Güncelleme işleminde mevcut kullanıcının e-postası benzersiz kontrolünden muaf tutulmalı.
         * Route parametresi olarak {id} varsa, onu alıyoruz.
         */
        $id = $this->route('id') ?? $this->route('user');

        return [
            'company_name' => ['required', 'string', 'max:100'],
            'name'         => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'],
            'surname'      => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'],
            'email'        => ['required', 'email', 'unique:users,email,' . $id],
            'phone'        => ['required', 'unique:users,phone,', 'regex:/^[0-9]{10}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Şirket adı zorunludur.',
            'name.required'         => 'İsim alanı zorunludur.',
            'surname.required'      => 'Soyisim alanı zorunludur.',
            'email.required'        => 'E-posta adresi zorunludur.',
            'email.unique'          => 'Bu e-posta adresi zaten kullanılıyor.',
            'phone.required'        => 'Telefon numarası zorunludur.',
            'phone.unique'          => 'Bu telefon numarası zaten kayıtlı. Lütfen farklı bir numara giriniz.',
            'phone.regex'           => 'Telefon numarası 10 haneli olmalıdır.',
            'name.regex'            => 'İsim sadece harf ve boşluk içerebilir.',
            'surname.regex'         => 'Soyisim sadece harf ve boşluk içerebilir.',
        ];
    }

    /**
     * Laravel’in default behavior’u HTML redirect’dir (422 JSON değil).
     * API projelerinde JSON hata dönmesi için `failedValidation()` override edilir.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
