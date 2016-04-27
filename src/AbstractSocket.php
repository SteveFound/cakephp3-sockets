<?php
namespace Dns\Socket;

use Dns\Socket\Contracts\SocketInterface;

abstract class AbstractSocket implements SocketInterface
{
    /**
     * @var SocketInterface
     */
    private $__socket;

    /**
     * Decorate another socket
     * @param SocketInterface $socket   The socket being wrapped
     */
    public function __construct(SocketInterface $socket)
    {
        $this->__socket = $socket;
    }

    /**
     *
     * Issues a DELETE request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function delete($uri, array $data, array $options)
    {
        return $this->__socket->delete($uri, $data, $options);
    }

    /**
     * Issues a POST request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function post($uri, array $data, array $options)
    {
        return $this->__socket->post($uri, $data, $options);
    }

    /**
     * Issues a GET request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function get($uri, array $data, array $options)
    {
        return $this->__socket->get($uri, $data, $options);
    }

    /**
     * Issues a PUT request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function put($uri, array $data, array $options)
    {
        return $this->_socket->put($uri, $data, $options);
    }
}
