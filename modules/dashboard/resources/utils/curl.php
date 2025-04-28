<?php

function parseHeaders(string $headers)
{
    $assoc = [];

    $lines = explode("\n", $headers);
    $lines = array_values(array_filter($lines, fn($x) => str_contains($x, ':')));

    foreach ($lines as $line) {
        $line = preg_replace("/\r$/", '', $line);
        list($headerName, $headerValue) = explode(':', $line, 2);
        $assoc[trim($headerName)] = trim($headerValue);
    }

    return $assoc;
}

function curl(string $url, array $getParams = [], array $portParams = [], array $headers = [], ?callable $curlModifier = null)
{
    $urlGetParams = count($getParams) ? '?' . http_build_query($getParams, '', '&') : '';
    $url = trim($url . $urlGetParams);

    stdlog("Fetching $url");
    $handle = curl_init($url);

    $isJSONRequest = str_starts_with($headers['content-type'] ?? '', 'application/json');
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_HEADER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($handle, CURLOPT_TIMEOUT, 10);


    if (count($portParams)) {
        $postFields = $isJSONRequest ?
            json_encode($portParams, JSON_THROW_ON_ERROR) :
            $portParams;

        curl_setopt($handle, CURLOPT_POSTFIELDS, $postFields);
    }

    $headers['user-agent'] = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/112.0';

    $headersStrings = [];
    foreach ($headers as $key => &$value)
        $headersStrings[] = "$key: $value";

    curl_setopt($handle, CURLOPT_HTTPHEADER, $headersStrings);


    if ($curlModifier) ($curlModifier)($handle);

    if (!($result = curl_exec($handle)))
        throw new RuntimeException(sprintf('Curl error %s: %s', curl_errno($handle), curl_error($handle)));

    $headerSize = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
    curl_close($handle);

    $resHeaders = substr($result, 0, $headerSize);
    $resHeaders = parseHeaders($resHeaders);

    if ($nextURL = ($resHeaders['location'] ?? null)) {
        return curl(
            $nextURL,
            $getParams,
            $postFields,
            $headers
        );
    }

    $resBody = substr($result, $headerSize);

    if (str_starts_with($resHeaders['content-type'] ?? '', 'application/json') && $resBody)
        return json_decode($resBody, true, flags: JSON_THROW_ON_ERROR);

    return $resBody;
}