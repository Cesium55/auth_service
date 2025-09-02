<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;


class TelegramAuthController extends Controller
{
    /*
        Class for tg login widget
    */

    public function getUserByTGID(Request $request)
    {
        $user = User::where("tg_id", $request->tg_id)->first();
        if (!$user) {
            abort(404, "User not found");
        }
        return response()->json(["data" => $user]);
    }

    public function link_tg(Request $request, UserService $userService)
    {

        $user_data = $userService->auth($request->bearerToken() ?? "");


        $telegramData = $request->all();

        $requiredFields = ['id', 'auth_date', 'hash'];
        foreach ($requiredFields as $field) {
            if (empty($telegramData[$field])) {
                return response()->json([
                    'success' => false,
                    'message' => "Отсутствует обязательное поле: $field"
                ], 400);
            }
        }

        $isValid = $this->validateTelegramData($telegramData);
        if (!$isValid) {
            return response()->json([
                'success' => false,
                'message' => 'Неверная подпись данных'
            ], 401);
        }

        $this->updateUserTGId($userService, $user_data->id, $telegramData["id"]);

        return response()->json([
            "data" => [
                'success' => true,
                'received_at' => now()->toIso8601String(),
                'telegram_id' => $telegramData["id"]
            ]
        ]);


    }

    protected function updateUserTGId(UserService $userService, $user_id, int $tg_id)
    {
        try {
            $user = $userService->get($user_id);
            $user->tg_id = $tg_id;
            $user->save();
        } catch (Exception) {
            abort(409, "This telegram ID is already in use");
        }
    }

    protected function validateTelegramData($data)
    {
        $botToken = config('services.tg.token');
        if (!$botToken) {
            return true;
        }

        $checkHash = $data['hash'];
        unset($data['hash']);

        ksort($data);
        $dataCheckArr = [];

        foreach ($data as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }

        $dataCheckString = implode("\n", $dataCheckArr);
        $secretKey = hash('sha256', $botToken, true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return Str::lower($hash) === Str::lower($checkHash);
    }
}
