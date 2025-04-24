<?php

return [
    'private_key' => file_exists(env('JWT_PRIVATE_KEY_PATH'))
        ? file_get_contents(env('JWT_PRIVATE_KEY_PATH'))
        : null,

    'public_key' => file_exists(env('JWT_PUBLIC_KEY_PATH'))
        ? file_get_contents(env('JWT_PUBLIC_KEY_PATH'))
        : null,
];
