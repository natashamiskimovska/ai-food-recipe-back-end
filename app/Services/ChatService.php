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

        return Chat::query()->when(isset($userId), function ($query) use ($userId) {
            $query->where('user_id', '=', $userId);
        })->get();
    }

    /**
     * @throws GuzzleException
     */
    public function askToChatGpt(array $data)
    {
        $message = "Make me a meal for" . $data['type'];
        if (isset($data['include_only'])) {
            $message .= 'With only these ingredients' . $data['include_only'];
        }

        if (isset($data['can_include'])) {
            $message .= 'Can include these ingredients' . $data['can_include'];
        }

        if (isset($data['limit_calories'])) {
            $message .= 'Limit calories to ' . $data['limit_calories'];
        }

        $message .= 'Format of the response: Name and then the recipe';

//        $response = $this->httpClient->post(config('chat_gpt.completion_endpoint'), [
//            'json' => [
//                'model' => 'gpt-3.5-turbo-16k-0613',
//                'messages' => [
//                    ['role' => 'user', 'content' => $message],
//                ],
//            ],
//        ]);
//
//        if ($response->getStatusCode() != 200) {
//            return ['There was an error while processing the data. Please try again'];
//        }
//
//        $chatGptResponse = json_decode($response->getBody(), true)['choices'][0]['message']['content'];
//        $imageName = preg_split('/:/', $chatGptResponse, 2, PREG_SPLIT_NO_EMPTY)[0];
//        $generatedImage = $this->generateImage($imageName);


        $chatGptResponse = "Egg and Avocado Breakfast Toast\n\nIngredients:\n- 2 slices of whole wheat bread\n- 2 eggs\n- 1 ripe avocado\n- Salt and pepper to taste\n- Optional toppings: cherry tomatoes, feta cheese, or red pepper flakes\n\nInstructions:\n1. Toast the slices of whole wheat bread to desired crispness.\n2. Meanwhile, cut the avocado in half, remove the pit, and scoop the flesh into a bowl. Mash the avocado with a fork until smooth.\n3. In a non-stick pan, fry the eggs to your preferred doneness (sunny-side up, over-easy, etc.).\n4. Spread the mashed avocado evenly onto the toasted bread slices.\n5. Place a fried egg on top of each slice.\n6. Sprinkle salt and pepper over the eggs.\n7. If desired, add some cherry tomatoes, crumbled feta cheese, or red pepper flakes on top for extra flavor.\n8. Serve immediately and enjoy your Egg and Avocado Breakfast Toast!";
        $imageName = preg_split('/\n/', $chatGptResponse, 2, PREG_SPLIT_NO_EMPTY)[0];
        $generatedImage = ["url" => "https://oaidalleapiprodscus.blob.core.windows.net/private/org-im0U23HE8xFAiXTgg0yp2zWY/user-T7lSD2y6unbug111netpPKax/img-jcBHHzZu6KJq8siVeWfS2LfZ.png?st=2023-11-02T13%3A29%3A40Z&se=2023-11-02T15%3A29%3A40Z&sp=r&sv=2021-08-06&sr=b&rscd=inline&rsct=image/png&skoid=6aaadede-4fb3-4698-a8f6-684d7786b067&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2023-11-01T22%3A41%3A07Z&ske=2023-11-02T22%3A41%3A07Z&sks=b&skv=2021-08-06&sig=QG5e11j9mwpYTQu/BvSw2FVFCePRi%2BSH4DFEmGhrTfY%3D"];

        return $this->createUserRecord($chatGptResponse, $imageName, $generatedImage['url']);
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
        $message = "Generate image for " . $imageName;
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
