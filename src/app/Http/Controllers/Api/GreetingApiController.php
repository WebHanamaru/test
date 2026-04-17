<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Greeting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GreetingApiController extends Controller
{
    /**
     * 一覧取得
     * GET /api/greetings
     */
    public function index(): JsonResponse
    {
        return response()->json(['data' => Greeting::all()]);
    }

    /**
     * 新規作成
     * POST /api/greetings
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'person'  => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $greeting = Greeting::create($validated);

        return response()->json(['data' => $greeting], 201);
    }

    /**
     * 更新
     * PUT /api/greetings/{greeting}
     */
    public function update(Request $request, Greeting $greeting): JsonResponse
    {
        $validated = $request->validate([
            'person'  => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $greeting->update($validated);

        return response()->json(['data' => $greeting]);
    }

    /**
     * 削除
     * DELETE /api/greetings/{greeting}
     */
    public function destroy(Greeting $greeting): JsonResponse
    {
        $greeting->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
