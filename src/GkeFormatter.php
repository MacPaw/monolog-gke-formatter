<?php

namespace MacPaw\MonologGkeFormatter;

use GuzzleHttp\Psr7\ServerRequest;
use Monolog\Formatter\JsonFormatter;

class GkeFormatter extends JsonFormatter
{
    public function format(array $record): string
    {
        $request = ServerRequest::fromGlobals();
        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,5);

        return parent::format(
            array_merge(
                $record['extra'],
                isset($debug[3])
                    ? [
                    'sourceLocation' => [
                        'file' => $debug[3]['file'],
                        'line' => $debug[3]['line'],
                        'function' => isset($debug[4]) && isset($debug[4]['class']) && isset($debug[4]['function'])
                            ? sprintf("%s:%s", $debug[4]['class'], $debug[4]['function'])
                            : (
                            isset($debug[4]) && isset($debug[4]['function'])
                                ? $debug[4]['function']
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