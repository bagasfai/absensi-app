<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Config;

class TelegramController extends Controller
{
    protected $telegram;
    protected $apiToken;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        $this->apiToken = Config::get('services.telegram.token');;
    }

    public function sendMessage($id, $message = "")
    {
        try {
            $response = $this->telegram->sendMessage([
                'chat_id' => $id,
                'text'    => $message,
            ]);

            // Do something with the response if needed
            return response()->json($response);
        } catch (\Telegram\Bot\Exceptions\TelegramResponseException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sendReminderMessage($chatId, $message)
    {
        try {
            $response = $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text'    => $message,
            ]);

            // Do something with the response if needed
            return response()->json($response);
        } catch (\Telegram\Bot\Exceptions\TelegramResponseException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
