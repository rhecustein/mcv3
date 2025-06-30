<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class QrEncryptHelper
{
    public static function encrypt($input)
    {
        return base64_encode(Crypt::encryptString($input));
    }

    public static function decrypt($input)
    {
        try {
            return Crypt::decryptString(base64_decode($input));
        } catch (\Exception $e) {
            return null;
        }
    }
}
