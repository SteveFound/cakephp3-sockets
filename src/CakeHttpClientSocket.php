<?php
namespace Dns\Socket;

use Cake\Network\Http\Client;
use Dns\Socket\Contracts\SocketInterface;

/**
 * This class wraps a standard CakePHP HTTP Client class to make it conform to our Socket interface.
 */
class CakeHttpClientSocket implements SocketInterface
{
    /**
     * @var Client
     */
    protected $_client;

    /**
     * Class Constructor
     * @param   Client $client  The Cake\Network\Http\Client we are wrapping
     */
    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    /**
     * Issues a PUT request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function put($uri, array $data = [], array $options = [])
    {
        $response = $this->_client->put($uri, $data, $options);
        if ($response->isOk()) {
            return $response->body;
        }
        return false;
    }

    /**
     * Issues a DELETE request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Query to append to URI
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function delete($uri, array $data = [], array $options = [])
    {
        $response = $this->_client->delete($uri, $data, $options);
        if ($response->isOk()) {
            return $response->body;
        }
        return false;
    }

    /**
     * Issues a GET request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request. Either a string uri, or a uri array
     * @param array $data    Querystring parameters to append to URI
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function get($uri, array $data = [], array $options = [])
    {
        $response = $this->_client->get($uri, $data, $options);
        if ($response->isOk()) {
            return $response->body;
        }
        return false;
    }

    /**
     * Issues a POST request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request.
     * @param array $data    Array of POST data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function post($uri, array $data = [], array $options = [])
    {
        $response = $this->_client->post($uri, $data, $options);
        if ($response->isOk()) {
            return $response->body;
        }
        return false;
    }
}
