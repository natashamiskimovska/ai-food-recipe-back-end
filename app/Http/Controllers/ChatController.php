<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\Chat;
use App\Services\ChatService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Response\CustomResponse;

class ChatController extends Controller
{
    public function __construct(public ChatService $chatService, public CustomResponse $customResponse)
    {
    }

    /**
     */
    public function index()
    {
        $response = $this->chatService->index();

        return $this->customResponse->success('Success', $response->toArray());
    }

    /**
     * @throws GuzzleException
     */
    public function chat(ChatRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $data = $this->chatService->askToChatGpt($validated);

        return $this->customResponse->success('Success', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $chat = Chat::find($id);

        $this->authorize('show', [$chat]);

        return $this->customResponse->success(data: $chat->toArray());
    }
}
