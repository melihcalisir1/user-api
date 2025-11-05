<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        try {
            $user = $this->service->createUser($request->all());
            return response()->json([
                'message' => 'Kullanıcı başarıyla eklendi.',
                'data' => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        // Filtreleme için opsiyonel parametreleri alıyoruz
        $filters = $request->only(['company_id']);

        $users = $this->service->listUsers($filters);

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = $this->service->updateUser($request->all(), $id);

            return response()->json([
                'message' => 'Kullanıcı başarıyla güncellendi.',
                'data' => $user
            ], 200);
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
