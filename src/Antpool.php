<?php

/**
 *** original antpool-php-api release
 * @author      Sebastian Lutz <lutz@baebeca.de>
 * @copyright   Baebeca Solutions
 * @email       lutz@baebeca.de
 * @pgp         0x5AD0240C
 * @link        https://www.baebeca.de
 * @link-github https://github.com/Elompenta/antpool-php-api
 * @project     antpool-php-api
 * @license     GNU GENERAL PUBLIC LICENSE Version 2
 *
 *** forked laravel-antpool-api
 * @package     aburakovskiy\laravel-antpool-api
 * @author      Alexander Burakovskiy <alexander.burakovskiy@gmail.com>
 */
namespace Aburakovskiy\LaravelAntpoolApi;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Antpool
{
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $secret;

    /**
     * Constructor
     * $antpool = new Antpool(ANTPOOL_USERNAME, ANTPOOL_API_KEY, ANTPOOL_API_SECRET)
     *
     * @param string $username
     * @param string $key
     * @param string $secret
     * @throws \Exception
     */
    public function __construct($username, $key, $secret)
    {
        if (!function_exists('curl_exec')) {
            throw new \Exception("Error: Please install PHP curl extension to use this lib.");
        }

        $this->username = $username;
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Test if the type can support the pageSize parameter
     *
     * @param $type
     * @return bool
     */
    function hasPageSizeParameter($type) {
        return $type === 'workers' || $type === 'paymentHistory';
    }

    /**
     * Make API call
     *
     * @param string $type
     * @param string $coin BTC, LTC, ETH, ZEC, DAS
     * @param int $page_size default 10
     * @param int $page
     * @return mixed
     * @throws \Exception
     */
    public function get($type, $coin = 'BTC', $page_size = 10, $page = 1)
    {
        $nonce = time();
        $hmac_message = $this->username . $this->key . $nonce;
        $hmac = strtoupper(hash_hmac('sha256', $hmac_message, $this->secret, false));

        $post_fields = array(
            'key' => $this->key,
            'nonce' => $nonce,
            'signature' => $hmac,
            'coin' => $coin,
            'page' => $page,
        );

        if($this->hasPageSizeParameter($type))
            $post_fields = array_merge( $post_fields, array('pageSize' => $page_size));

        $post_data = '';
        foreach ($post_fields as $key => $value) {
            $post_data .= $key . '=' . $value . '&';
        }
        rtrim($post_data, '&');

        return $this->api_get($type, $post_fields, $post_data);
    }

    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */

    /**
     * Using CURL to issue a GET request
     *
     * @param string $type
     * @param array $post_fields
     * @param string $post_data
     * @return mixed
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     */
    public function api_get($type, $post_fields, $post_data)
    {
        $client = new Client();
        $response = json_decode($client->request(
            'POST',
            'https://antpool.com/api/' . $type . '.htm?'.$post_data,
            ["headers" => [
                'Accept' => 'application/json',
            ]]
        )->getBody()->getContents());

        if ($response->message === 'ok') {
            return $response->data;
        } else {
            throw new \Exception('API Error: ' . print_r($response, true));
        }
    }
}