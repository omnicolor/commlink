<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use DirectoryIterator;
use Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Pennant\Feature;
use Spatie\Permission\Models\Role;
use Stringable;

/**
 * @psalm-suppress UnusedClass
 */
class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse | View
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

        if ('application/json' !== $request->header('Accept')) {
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
        return new JsonResponse(UserResource::collection(User::all()));
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
            $path = explode('/', $patch['path']);
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
                        $user->assignRole($path[2]);
                    } else {
                        $user->removeRole($path[2]);
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
     * Return a single User as JSON.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }
}
