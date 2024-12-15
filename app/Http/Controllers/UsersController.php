<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateTokenRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\CarbonImmutable;
use DirectoryIterator;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\PersonalAccessToken as Token;
use Spatie\Permission\Models\Role;
use Stringable;

class UsersController extends Controller
{
    public function createToken(
        CreateTokenRequest $request,
        User $user
    ): JsonResponse {
        $expires = $request->input('expires_at');
        if (null !== $expires) {
            $expires = new CarbonImmutable($expires);
        }
        $token = $user->createToken($request->input('name'), ['*'], $expires);
        return new JsonResponse(
            [
                'id' => explode('|', $token->plainTextToken)[0],
                'expires_at' => $token->accessToken->expires_at,
                'last_used' => null,
                'name' => $token->accessToken->name,
                'plainText' => $token->plainTextToken,
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    public function deleteToken(
        User $user,
        int $token_id,
        Request $request,
    ): JsonResponse {
        $token = Token::findOrFail($token_id);
        /** @var User Only authenticated requests can make it here. */
        $request_user = $request->user();
        abort_if(
            User::class !== $token->tokenable_type
                || $token->tokenable_id !== $user->id
                || !$request_user->is($user),
            JsonResponse::HTTP_FORBIDDEN,
            'Forbidden',
        );
        $token->delete();
        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Get a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return new JsonResponse(UserResource::collection(User::all()));
    }

    /**
     * Return a single User as JSON.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        foreach ($request->input('patch', []) as $patch) {
            if (
                !array_key_exists('path', $patch)
                || !array_key_exists('op', $patch)
                || !array_key_exists('value', $patch)
            ) {
                return new JsonResponse(
                    ['error' => 'Invalid patch'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }
            $path = explode('/', (string)$patch['path']);
            switch ($path[1]) {
                case 'features':
                    try {
                        $feature = 'App\\Features\\' . $path[2];
                        $feature = new $feature();
                    } catch (Error) {
                        return new JsonResponse(
                            ['error' => 'Invalid feature'],
                            JsonResponse::HTTP_NOT_FOUND
                        );
                    }
                    if ('true' === $patch['value']) {
                        Feature::for($user)->activate($feature::class);
                    } else {
                        Feature::for($user)->deactivate($feature::class);
                    }
                    break;
                case 'roles':
                    $role = Role::find($path[2]);
                    if (null === $role) {
                        return new JsonResponse(
                            ['error' => 'Invalid role'],
                            JsonResponse::HTTP_NOT_FOUND
                        );
                    }
                    if ('true' === $patch['value']) {
                        $user->assignRole($role);
                    } else {
                        $user->removeRole($role);
                    }
                    break;
            }
        }
        return new JsonResponse(
            new UserResource($user),
            JsonResponse::HTTP_ACCEPTED
        );
    }

    /**
     * View the admin interface for users.
     */
    public function view(Request $request): View
    {
        $features = [];
        foreach (new DirectoryIterator(app_path('Features')) as $file) {
            if ($file->isDot()) {
                continue;
            }

            // Unlikely to happen, but just in case there's something non-PHP in
            // the Features directory...
            if ('text/x-php' !== mime_content_type($file->getPathname())) {
                continue; // @codeCoverageIgnore
            }

            $class = 'App\\Features\\' . $file->getBasename('.php');
            /** @var Stringable */
            $feature = new $class();
            $features[(string)$feature] = $feature;
        }
        ksort($features);

        return view(
            'users.index',
            [
                'features' => $features,
                'roles' => Role::all(),
                'user' => $request->user(),
                'users' => User::all(),
            ]
        );
    }
}
