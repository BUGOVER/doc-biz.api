<?php
declare(strict_types=1);

/**
 * @param array $data
 * @return array
 */
function ng_data_iterate(array $data): array
{
    $itemData = [];

    foreach ($data as $datum) {
        $itemData = $datum;
    }

    return $itemData;
}

/**
 * @param string $key
 * @return mixed|null
 */
function get_request_attribute(string $key = '')
{
    $params = app('request')->attributes->all();

    if (empty($key)) {
        return $params;
    }

    if (false !== strpos($key, '.')) {
        return \Illuminate\Support\Arr::get($params, $key, null);
    }

    return !empty($params[$key]) ? $params[$key] : null;
}

/**
 * @return string
 */
function generate_token()
{
    return md5(time() . str_random(64));
}

/**
 * @param array $data
 * @return array
 */
function removeIds(array $data): array
{
    $data = (array)$data;

    return array_filter($data, function ($value) use ($data) {
        if (strpos($value, '_id') != false) {
            unset($value);
        }
    }, ARRAY_FILTER_USE_KEY);
}
