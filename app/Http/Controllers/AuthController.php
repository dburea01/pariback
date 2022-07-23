<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\ValidateRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\SendEmailRegister;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $userRepository;

    private $passwordResetRepository;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository
    ) {
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'VALIDATED',
        ];

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        try {
            $user = User::where('email', $request->email)->first();
            $user->tokens()->delete();

            return response()->json([
                'token' => $user->createToken('authToken')->plainTextToken,
                'user' => $user->only(['id', 'name']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(StoreUserRequest $request)
    {
        try {
            $user = $this->userRepository->insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'CREATED',
                'token_validation_registration' => Str::random(20),
            ]);

            $user->notify(new SendEmailRegister());

            return new UserResource($user);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function validateRegistration(ValidateRegistrationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        try {
            $user = $this->userRepository->validateRegistration($user);
            // TODO : send email to the new user for confirmation
            return new UserResource($user);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'User deconnected'], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            DB::transaction(function () use ($request) {
                $this->passwordResetRepository->destroy($request->email);
                $this->passwordResetRepository->insert($request->email);
                // TODO : send the email with the token to reset the password
            });
        }

        return response()->json(['message' => trans('auth.forgot-password')]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->passwordResetRepository->destroy($request->email);
            $this->userRepository->modifyPassword($request->email, $request->password);
            DB::commit();
            // TODO : send email the password has been modified
            return response()->json(['message' => trans('auth.password-reset-ok')]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => trans('auth.password-reset-ko')]);
        }
    }
}
