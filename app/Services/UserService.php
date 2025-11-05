<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function createUser(array $data)
    {
        // Doğrulama
        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:100',
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'], // Türkçe harfler ve boşluk
            'surname' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'], // Türkçe harfler ve boşluk
            'email' => 'required|email|unique:users',
            'phone' => ['required', 'string', 'regex:/^[0-9]{13}$/'], // 13 haneli rakamlar
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Firma yoksa oluştur.
        $company = Company::firstOrCreate(['name' => $data['company_name']]);

        $userData = [
            'company_id' => $company->id,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ];

        return $this->repo->create($userData);
    }
}
