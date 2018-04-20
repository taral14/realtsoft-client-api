<?php
namespace RealtsoftApi;

class Client
{
    private $key;

    private $secret;

    private $url;

    private $statusCode;

    public function __construct($url, $key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->url = $url;
    }

    public function call($method, array $params = [], $type = 'GET')
    {
        if (!in_array($type, ['GET', 'POST', 'PUT', 'DELETE'])) {
            $type = 'GET';
        }

        $options = [
            CURLOPT_URL            => $this->url . '/api/' . $method,
            CURLOPT_CUSTOMREQUEST  => $type,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'auth: ' . $this->key . ':' . $this->generateSign($method, $params)
            ],
        ];

        $ch = curl_init();

        if ($type == 'GET') {
            $options[CURLOPT_URL] = $this->url . '/api/' . $method . '?' . http_build_query($params);
        } else {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            throw new \Exception($error);
        }

        $json = @json_decode($response);
        if(empty($json) && $json != []) {
            $json['success'] = false;
            $json['response'] = $response;
        }

        return $json;
    }

    private function generateSign($method, $params)
    {
        ksort($params);
        return base64_encode(hash_hmac('sha1', $method . md5(http_build_query($params)), $this->secret));
    }

    /**
     * @return int
     */

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}