<?php
namespace Dns\Socket;

use Cake\Cache\Cache;
use Dns\Socket\Contracts\SocketInterface;

/**
 * CachedResponseSocket is a cacheing decorator to another socket.
 *
 * @author Steve Found (DnSMedia)
 */
class CachedResponseSocket extends AbstractSocket implements SocketInterface
{
    /**
     * The cache key
     * @var string key
     */
    private $key;

    /**
     * Config settings key
     * @var string
     */
    protected $config;

    /**
     * Create the object and assign a cache key.
     *
     * @param SocketInterface $socket   The socket being cached
     * @param mixed $cacheConfig        string - config name, array - CakePHP config settings, null - hour default
     */
    public function __construct(SocketInterface $socket, $cacheConfig = 'default')
    {
        parent::__construct($socket);
        $this->configKey = $cacheConfig;
    }

    /**
     * Configure the cache
     * @param mixed $cacheConfig  string - config name, array - CakePHP config settings, null - hour default
     * @return void
     */
    public function config($cacheConfig)
    {
        $this->configKey = $cacheConfig;
    }

    /**
     * Discard the cached data
     * @return void
     */
    public function forget()
    {
        Cache::delete($this->key, $this->configKey);
    }

    /**
     * Issues a PUT request to the specified URI
     *
     * @param string $uri       Either a full URL or just the path.
     * @param array $data       The query data for the URL.
     * @param array $options    The config options stored with Client::config()
     * @return mixed            Result of request or false on failure
     */
    public function put($uri, array $data = [], array $options = [])
    {
        $this->key = $this->buildKey('put', $uri, $data, $options);
        $response = Cache::read($this->key, $this->configKey);
        if (!$response) {
            $response = parent::put($uri, $data, $options);
            if ($response && isset($response)) {
                Cache::write($this->key, $response, $this->configKey);
            } else {
                $response = false;
            }
        }
        return $response;
    }

    /**
     * Issues a DELETE request to the specified URI
     *
     * @param string $uri       Either a full URL or just the path.
     * @param array $data       The query data for the URL.
     * @param array $options    The config options stored with Client::config()
     * @return mixed            Result of request or false on failure
     */
    public function delete($uri, array $data = [], array $options = [])
    {
        $this->key = $this->buildKey('delete', $uri, $data, $options);
        $response = Cache::read($this->key, $this->configKey);
        if (!$response) {
            $response = parent::delete($uri, $data, $options);
            if ($response && isset($response)) {
                Cache::write($this->key, $response, $this->configKey);
            } else {
                $response = false;
            }
        }
        return $response;
    }

    /**
     * Issues a GET request to the specified URI
     *
     * @param string $uri       Either a full URL or just the path.
     * @param array $data       The query data for the URL.
     * @param array $options    The config options stored with Client::config()
     * @return mixed            Result of request or false on failure
     */
    public function get($uri, array $data = [], array $options = [])
    {
        $this->key = $this->buildKey('get', $uri, $data, $options);
        $response = Cache::read($this->key, $this->configKey);
        if (!$response) {
            $response = parent::get($uri, $data, $options);
            if ($response && isset($response)) {
                Cache::write($this->key, $response, $this->configKey);
            } else {
                $response = false;
            }
        }
        return $response;
    }

    /**
     * Issues a POST request to the specified URI
     *
     * @param string $uri       Either a full URL or just the path.
     * @param array $data       The query data for the URL.
     * @param array $options    The config options stored with Client::config()
     * @return mixed            Result of request or false on failure
     */
    public function post($uri, array $data = [], array $options = [])
    {
        $this->key = $this->buildKey('post', $uri, $data, $options);
        $response = Cache::read($this->key, $this->configKey);
        if (!$response) {
            $response = parent::post($uri, $data, $options);
            if ($response && isset($response)) {
                Cache::write($this->key, $response, $this->configKey);
            } else {
                $response = false;
            }
        }
        return $response;
    }

    /**
     * Generate a URL based on the scoped client options. (Taken from Cake\Network\Http\Client)
     *
     * @param string $action    The name of the action being performed
     * @param string $url       Either a full URL or just the path.
     * @param string|array $query The query data for the URL.
     * @param array $options    The config options stored with Client::config()
     * @return string An md5 encoded key for the request
     */
    protected function buildKey($action, $url, $query = [], $options = [])
    {
        if (empty($options) && empty($query)) {
            return md5($url);
        }
        if ($query) {
            $q = (strpos($url, '?') === false) ? '?' : '&';
            $url .= $q;
            $url .= is_string($query) ? $query : http_build_query($query);
        }
        if (preg_match('#^https?://#', $url)) {
            $out = $url;
        } else {
            $defaults = [
                'host' => null,
                'port' => null,
                'scheme' => 'http',
            ];
            $options += $defaults;
            $defaultPorts = [
                'http' => 80,
                'https' => 443
            ];
            $out = $options['scheme'] . '://' . $options['host'];
            if ($options['port'] && $options['port'] != $defaultPorts[$options['scheme']]) {
                $out .= ':' . $options['port'];
            }
            $out .= '/' . ltrim($url, '/');
        }
        return md5($action . $out);
    }
}
