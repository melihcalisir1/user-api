<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\UserRequest;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function store(UserRequest $request)
    {
        try {
            $user = $this->service->createUser($request->validated());

            return response()->json([
                'message' => 'Kullanıcı başarıyla eklendi.',
                'data' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index(UserRequest $request)
    {
        // Filtreleme için opsiyonel parametreleri alıyoruz
        $filters = $request->only(['company_id']);

        $users = $this->service->listUsers($filters);

        return response()->json($users);
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $user = $this->service->updateUser($request->validated(), $id);
            return response()->json([
                'message' => 'Kullanıcı başarıyla güncellendi.',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->service->deleteUser($id);

            return response()->json([
                'message' => 'Kullanıcı başarıyla silindi.',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
