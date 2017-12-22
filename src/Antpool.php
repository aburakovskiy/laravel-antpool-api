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
     * Make API call
     *
     * @param string $type
     * @param string $coin BTC, LTC, ETH, ZEC, DAS
     * @return mixed
     * @throws \Exception
     */
    public function get($type, $coin = 'BTC')
    {
        $nonce = time();
        $hmac_message = $this->username . $this->key . $nonce;
        $hmac = strtoupper(hash_hmac('sha256', $hmac_message, $this->secret, false));

        $post_fields = array(
            'key' => $this->key,
            'nonce' => $nonce,
            'signature' => $hmac,
            'coin' => $coin
        );

        $post_data = '';
        foreach ($post_fields as $key => $value) {
            $post_data .= $key . '=' . $value . '&';
        }
        rtrim($post_data, '&');

        $apiData = $this->api_get($type, $post_fields, $post_data);
        return $apiData;
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
     * @throws \Exception
     */
    public function api_get($type, $post_fields, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://antpool.com/api/' . $type . '.htm');
        // todo: switch to public cert
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, count($post_fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // set large timeout because API lak sometimes
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        curl_close($ch);

        // check if curl was timed out
        if ($result === false) {
            throw new \Exception('Error: No API connect');
        }

        // validate JSON
        $result_json = json_decode($result, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception('Error: read broken JSON from API - JSON Error: ' . json_last_error() . ' (' . $result . ')');
        }

        if ($result_json['message'] == 'ok') {
            return $result_json['data'];
        } else {
            throw new \Exception('API Error: ' . print_r($result_json, true));
        }
    }
}