<?php
/**
 * Here is your custom functions.
 */


use support\Response;

if (!function_exists('uuid')) {
    /**
     * Generates a UUID (Universally Unique Identifier)
     *
     * A UUID is a unique identifier designed to be globally unique across all possible computers.
     * This function generates a version 4 (random) UUID with the correct variant bits set.
     *
     * @return string A UUID formatted as a string
     */
    function uuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('jsonWithCode')){
    function jsonWithCode(array $data,int $code = 200,array $header = []): Response
    {
        return response(json_encode($data,JSON_UNESCAPED_UNICODE),$code,$header);
    }
}


if (!function_exists('random_string'))
{
    function random_string(int $length = 16): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}