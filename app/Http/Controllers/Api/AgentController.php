<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowAgentRequest;
use App\Strategy\AidResolvingStrategyInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AgentController extends Controller
{
    public function __construct(protected AidResolvingStrategyInterface $resolvingStrategy) {}

    public function show(ShowAgentRequest $request): JsonResponse
    {
        $response = $this->resolvingStrategy->resolve($request->validated())
            ?? throw new NotFoundHttpException('Agent not found', null, 400,
                ['Content-Type' => 'application/json']);

        return response()->json($response);
    }
}
