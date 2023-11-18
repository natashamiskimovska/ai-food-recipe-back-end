<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;

class ChatService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => config('chat_gpt.base_uri'),
            'headers' => [
                'Authorization' => 'Bearer ' . config('chat_gpt.api_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->type == User::MEMBER_ROLE) {
            $userId = $user->getKey();
        }

        return Chat::query()
            ->when(isset($userId), function ($query) use ($userId) {
                $query->where('user_id', '=', $userId);
            })->get();
    }

    /**
     * @throws GuzzleException
     */
    public function askToChatGpt(array $data)
    {
        $message = "Make me a meal for" . $data['type'];
        if (isset($data['can_include']) && $data['include_only']) {
            $message .= 'With only these ingredients' . $data['can_include'];
        }

        if (isset($data['can_include']) && !$data['include_only']) {
            $message .= 'Can include these ingredients' . $data['can_include'];
        }

        if (isset($data['limit_calories'])) {
            $message .= 'Limit calories to ' . $data['limit_calories'];
        }

        $message .= 'Format of the response: Name and then the recipe';

        $response = $this->httpClient->post(config('chat_gpt.completion_endpoint'), [
            'json' => [
                'model' => 'gpt-3.5-turbo-16k-0613',
                'messages' => [
                    ['role' => 'user', 'content' => $message],
                ],
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            return ['There was an error while processing the data. Please try again'];
        }

        $chatGptResponse = json_decode($response->getBody(), true)['choices'][0]['message']['content'];
        $splitResponse = preg_split('/\n/', $chatGptResponse, 2, PREG_SPLIT_NO_EMPTY);
        $generatedImage = $this->generateImage($splitResponse[0]);

        return $this->createUserRecord($splitResponse[1], $splitResponse[0], $generatedImage['url']);
    }

    public function createUserRecord(string $chatGptResponse, string $name, string $imageUrl)
    {
        /** @var User $user */
        $user = Auth::user();

        $data['user_id'] = $user->getKey();
        $data['name'] = $name;
        $data['message'] = json_encode($chatGptResponse);
        $data['image_url'] = $imageUrl;

        return Chat::create($data)->toArray();
    }

    public function generateImage(string $imageName)
    {
        $message = $imageName;
        $response = $this->httpClient->post(config('chat_gpt.image_endpoint'), [
            'json' => [
                "prompt" => $message,
                "n" => 1,
                "size" => "1024x1024"
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            return [];
        }

        return json_decode($response->getBody(), true)['data'][0];
    }
}
