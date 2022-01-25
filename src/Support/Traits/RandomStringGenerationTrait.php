<?php

namespace Pinnacle\OpenIdConnect\Support\Traits;

use Exception;
use Pinnacle\OpenIdConnect\Support\Exceptions\OpenIdConnectException;

trait RandomStringGenerationTrait
{
    /**
     * @throws OpenIdConnectException
     */
    private static function generateRandomString($length = 16): string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            try {
                $bytes = random_bytes($size);
            } catch (Exception $e) {
                throw new OpenIdConnectException('Error occurred while generating random string', 0, $e);
            }

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
