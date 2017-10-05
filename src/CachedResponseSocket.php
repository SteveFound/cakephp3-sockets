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
    protected $key;

    /**
     * Config settings key
     * @var string
     */
    protected $configKey;

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
     * Set the key
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Return the key
     * @param string $key
     */
    public function getKey()
    {
        return $this->key;
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
}
