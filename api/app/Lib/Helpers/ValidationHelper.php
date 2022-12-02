<?php

namespace App\Helpers;

class ValidationHelper
{
    public const PASSWORD_REGEXP = "/^(?=(.*[a-z]){2,})(?=(.*[A-Z]){1,})(?=(.*[0-9]){1,}).{6,}$/";
    public const PHONE_REGEXP    = "/^((8|\+994|\+995|\+7|\+996|\+998|\+993)[\- ]?)?\(?\d{3,5}\)?[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}[\- ]?\d{1}(([\- ]?\d{1})?[\- ]?\d{1})?$/";
    public const CYRILIC_REGEXP  = "/^[а-яё ]+$/iu";
    public const LATINIC_REGEXP  = "/^[a-z.@-\d]+$/iu";
    public const EMAIL_REGEXP    = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
    public const DIGITS_REGEXP   = "/^\d+$/";

    /**
     * Removes any non digit symbols from phone number
     *
     * @param string $phone
     *
     * @return string
     */
    public static function replacePhoneNumber(string $phone): string
    {
        return preg_replace("/^(\+7|7|8)|[\D]/", "", $phone);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function mbUcFirst(string $value): string
    {
        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($value, 1, mb_strlen($value, 'UTF-8'), 'UTF-8');
    }

    /**
     * @param array $array
     *
     * @return void
     */
    public static function sanitizeArray(array &$array)
    {

        array_walk_recursive($array, function (&$value, $key) {
            if (is_bool($value) || is_numeric($value) || is_null($value)) {
                return;
            }

            $value = trim(strip_tags($value));
        });

        return $array;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public static function sanitizeString(string &$value)
    {
        if (!$value) {
            return "";
        }

        $value = strip_tags($value);
    }
}
