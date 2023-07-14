<?php

namespace Modules\MangoOffice\Library;

/**
 * Class ApiCache
 * @package app\components
 * Компонент работы с API-кешем сайта.
 */
class ApiCache
{
    /**
     * @var string
     */
    public $keyParam;

    /**
     * Строит нормализованный ключ кеша по указанному ключу.
     * @param mixed $key
     * @return string
     */
    public function buildKey($key)
    {
        if (is_string($key) && ($decodedKey = urldecode($key))) {
            parse_str(parse_url($decodedKey, PHP_URL_QUERY), $keyData);

            if ($this->keyParam && !empty($keyData[$this->keyParam])) {
                return $this->keyPrefix . $keyData[$this->keyParam] . ':' . $decodedKey;
            }

            return $this->keyPrefix . $decodedKey;
        }

        return parent::buildKey($key);
    }
}