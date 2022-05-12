<?php

namespace App\Util;

use App\Enum\GermanState;
use Doctrine\Migrations\Version\State;
use JetBrains\PhpStorm\ArrayShape;

class Util
{

    /**
     * @param string $jwt
     * @param string $secret
     * @return bool
     */
    public static function isJwtValid(string $jwt, string $secret): bool
    {

        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        $base64_url_header = Util::_base64url_encode($header);
        $base64_url_payload = Util::_base64url_encode($payload);
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = Util::_base64url_encode($signature);

        $is_signature_valid = ($base64_url_signature === $signature_provided);

        if ($is_token_expired || !$is_signature_valid) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param GermanState|null $state
     * @return array
     */
    public static function notAHoliday(GermanState $state = null): array
    {
        if ($state === null) {
            return array(
                'isHoliday' => false
            );
        } else {
            return array(
                'state' => $state->value,
                'isHoliday' => false
            );
        }
    }


    /**
     * @param $data
     * @return string
     */
    private static function _base64url_encode($data): string
    {
        $b64 = base64_encode($data);
        $url = strtr($b64, '+/', '-_');
        return rtrim($url, '=');
    }


}
