<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
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
        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:100',
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'], // Türkçe harfler ve boşluk
            'surname' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'], // Türkçe harfler ve boşluk
            'email' => 'required|email|unique:users',
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'], // 10 haneli rakamlar
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

    public function listUsers(array $filters = [])
    {
        // Filtreleme işlemini UserRepository'e ileterek yapıyoruz
        return $this->repo->getAll($filters);
    }

    public function updateUser(array $data, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->trashed()) {
            throw new \Exception('Silinmiş bir kullanıcı güncellenemez.');
        }

        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:100',
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'], // Türkçe harfler ve boşluk
            'surname' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZçÇğĞıİöÖşŞüÜ\s]+$/'], // Türkçe harfler ve boşluk
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'], // 10 haneli rakamlar
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $company = $user->company;

        if (isset($data['company_name']) && $data['company_name'] !== $company->name) {
            $company = Company::firstOrCreate(['name' => $data['company_name']]);
        }

        $user->update([
            'company_id' => $company->id,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);

        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            throw new \Exception('Kullanıcı bulunamadı.');
        }

        if ($user->trashed()) {
            throw new \Exception('Bu kullanıcı zaten silinmiş.');
        }

        $user->delete();

        return $user;
    }
}
