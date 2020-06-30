<?php

namespace MacPaw\MonologGkeFormatter;

use GuzzleHttp\Psr7\ServerRequest;
use Monolog\Formatter\JsonFormatter;

class GkeFormatter extends JsonFormatter
{
    public function format(array $record): string
    {
        $request = ServerRequest::fromGlobals();
        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,6);

        return parent::format(
            array_merge(
                $record['extra'],
                isset($debug[4])
                    ? [
                    'sourceLocation' => [
                        'file' => $debug[4]['file'],
                        'line' => $debug[4]['line'],
                        'function' => isset($debug[5]) && isset($debug[5]['class']) && isset($debug[5]['function'])
                            ? $debug[5]['class'] . $debug[5]['type'] . $debug[5]['function']
                            : (
                            isset($debug[5]) && isset($debug[5]['function'])
                                ? $debug[5]['function']
                                : ''
                            ),
                    ]
                ]
                    : [],
                preg_match('/cgi/', php_sapi_name())
                    ? [
                    'httpRequest' => [
                        'requestMethod' => $request->getMethod(),
                        'requestUrl' => $request->getUri()->__toString(),
                        'requestSize' => $request->getBody()->getSize(),
                        'protocol' => $request->getProtocolVersion(),
                        'referer' => $request->getHeaderLine('Referer'),
                        'userAgent' => $request->getHeaderLine('User-Agent'),
                        'remoteIp' => $request->getHeaderLine('X-Forwarded-For'),
                    ],
                ]
                    : [],
                [
                    'message' => $record['message'],
                    'thread' => $record['channel'],
                    'severity' => $record['level_name'],
                    'serviceContext' => $record['context'],
                    'timestamp' => $record['datetime']->getTimestamp(),
                ]
            )
        );
    }
}