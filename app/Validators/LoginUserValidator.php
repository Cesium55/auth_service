<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginUserValidator{

    static $rules = [
        "email" => "required|email",
        "password" => "required|string|min:8"
    ];

    public static function validate($data){
        $validator = Validator::make($data, self::$rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

}
