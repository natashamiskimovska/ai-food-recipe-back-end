<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Response\CustomResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(public UserService $userService, public CustomResponse $customResponse)
    {
    }

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('index', User::class);

        $data = $this->userService->index();

        return $this->customResponse->success(data: $data->toArray());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::find($id);

        $this->authorize('update', $user);

        $validated = $request->validated();

        $data = $this->userService->update($user, $validated);

        return $this->customResponse->success(data: $data->toArray());
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
        $user = User::find($id);

        $this->authorize('show', $user);

        return $this->customResponse->success(data: $user->toArray());
    }
}
