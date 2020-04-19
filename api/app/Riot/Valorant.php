<?php

namespace App\Riot;

use GuzzleHttp\Client;

class Valorant
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * @var object
     */
    protected $config;

    /**
     * Valorant constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = (object) $config;

        $this->http = new Client();
    }

    /**
     * @param $token
     *
     * @return mixed
     */
    public function setToken($token)
    {
        $this->config->token = $token;
    }

    /**
     * @return array[]
     */
    protected function getDefaultHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->config->token,
            'X-Riot-Entitlements-JWT' => $this->config->entitlements,
            'User-Agent' => 'ShooterGame/36 Windows/10.0.18362.1.256.64bit',
        ];
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->request('get', $this->config->url . '/token', [], [
            'headers' => [
                'Authorization' => $this->config->secret,
            ]
        ]);
    }

    /**
     * @param $name
     * @param $tag
     *
     * @return mixed
     */
    public function getUid($name, $tag)
    {
        return $this->request('get', $this->config->url . '/uid', compact('name', 'tag'), [
            'headers' => [
                'Authorization' => $this->config->secret,
            ]
        ]);
    }

    /**
     * @param $player
     * @param int $from
     * @param null $to
     *
     * @return mixed
     */
    public function getMatchHistory($player, $from = 0, $to = null)
    {
        return $this->request('get', 'https://pd.eu.a.pvp.net/match-history/v1/history/' . $player, [
            'startIndex' => $from,
            'endIndex' => $to ?: $from + 20,
        ]);
    }

    /**
     * @param $uid
     *
     * @return mixed
     */
    public function getMatchDetails($uid)
    {
        return $this->request('get', 'https://pd.eu.a.pvp.net/match-details/v1/matches/' . $uid);
    }

    /**
     * @param $uids
     *
     * @return mixed
     */
    public function getNames($uids)
    {
        return $this->request('put', 'https://pd.eu.a.pvp.net/name-service/v2/players', $uids);
    }

    /**
     * @param $method
     * @param $path
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    protected function request($method, $path, $data = [], $options = [])
    {
        $method = mb_strtolower($method);

        if (isset($options['headers'])) {
            $options['headers'] = array_merge($options['headers'], $this->getDefaultHeaders());
        } else {
            $options['headers'] = $this->getDefaultHeaders();
        }

        if ($method === 'get') {
            $options['query'] = $data;
        } else {
            $options['json'] = $data;
        }

        $time = microtime(true);

        $response = $this->http->request($method, $path, $options)->getBody();

        app('log')->info(mb_strtoupper($method) . ' ' . $path . ' took ' . round(microtime(true) - $time, 3));

        return json_decode((string) $response);
    }
}
