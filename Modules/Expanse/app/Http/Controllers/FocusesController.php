<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Modules\Expanse\Http\Resources\FocusResource;
use Modules\Expanse\Models\Focus;
use RuntimeException;

use function abort;
use function array_values;
use function sprintf;

class FocusesController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FocusResource::collection(array_values(Focus::all()))
            ->additional([
                'links' => [
                    'self' => route('expanse.focuses.index'),
                ],
            ]);
    }

    public function show(string $id): FocusResource
    {
        try {
            return (new FocusResource(new Focus($id)))
                ->additional([
                    'links' => [
                        'collection' => route('expanse.focuses.index'),
                    ],
                ]);
        } catch (RuntimeException $ex) {
            abort(
                Response::HTTP_NOT_FOUND,
                sprintf('%s not found', $id),
            );
        }
    }
}
