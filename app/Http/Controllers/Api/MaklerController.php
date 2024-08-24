<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowMaklerRequest;
use App\Strategy\VnrResolvingStrategyInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MaklerController extends Controller
{
    public function __construct(protected VnrResolvingStrategyInterface $resolvingStrategy) {}

    public function show(ShowMaklerRequest $request): JsonResponse
    {
        $response = $this->resolvingStrategy->resolve($request->validated())
            ?? throw new NotFoundHttpException('Makler not found', null, 400, ['Content-Type' =>'application/problem+json']);

        return response()->json($response);
    }
}
