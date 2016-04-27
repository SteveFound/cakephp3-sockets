<?php

namespace Dns\Socket\Contracts;

interface SocketInterface
{
    /**
     * Issues a PUT request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Array of PUT data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function put($uri, array $data, array $options);

    /**
     * Issues a DELETE request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request
     * @param array $data    Query to append to URI
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function delete($uri, array $data, array $options);

    /**
     * Issues a GET request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request. Either a string uri, or a uri array
     * @param array $data    Querystring parameters to append to URI
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function get($uri, array $data, array $options);

    /**
     * Issues a POST request to the specified URI, query, and request.
     *
     * @param mixed $uri     URI to request.
     * @param array $data    Array of POST data keys and values.
     * @param array $options Additional request options
     * @return mixed         Result of request, either false on failure or the response to the request.
     */
    public function post($uri, array $data, array $options);
}
