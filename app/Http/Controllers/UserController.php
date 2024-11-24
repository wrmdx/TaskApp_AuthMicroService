<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repository\IUserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $users = $this->userRepository->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function store(UserCreateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user = $this->userRepository->create($data);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->find($id);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified user
     *
     * @param UserUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->userRepository->update($id, $data);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'User updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userRepository->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users by email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchByEmail(Request $request): JsonResponse
    {
        try {
            $email = $request->get('email');
            $user = $this->userRepository->findByEmail($email);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'User found successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error searching user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth()->user();

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'Profile retrieved successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|string|min:8|confirmed',
            ]);

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $updatedUser = $this->userRepository->update($user->id, $data);

            return response()->json([
                'status' => 'success',
                'data' => $updatedUser,
                'message' => 'Profile updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
