<?php

namespace Test\EndToEnd\KongClient;

use Http\Message\Formatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TestFormatter implements Formatter
{

    /**
     * Formats a request.
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    public function formatRequest(RequestInterface $request)
    {
        $content = $request->getBody()->getContents();
        $request->getBody()->rewind();

        return sprintf(
            '%s %s %s %s',
            $request->getMethod(),
            $request->getUri()->__toString(),
            $request->getProtocolVersion(),
            $content
        );
    }

    /**
     * Formats a response.
     *
     * @param ResponseInterface $response
     *
     * @return string
     */
    public function formatResponse(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();
        $response->getBody()->rewind();

        return sprintf(
            '%s %s %s %s',
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getProtocolVersion(),
            $content
        );
    }
}
