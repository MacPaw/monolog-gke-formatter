<?php

namespace MacPaw\MonologGkeFormatter;

use GuzzleHttp\Psr7\ServerRequest;
use Monolog\Formatter\JsonFormatter;

class GkeFormatter extends JsonFormatter
{
    public function format(array $record): string
    {
        $request = ServerRequest::fromGlobals();

        return parent::format(
            array_merge(
                $record['extra'],
                isset($record['context']) && isset($record['context']['exception'])
                    ? [
                        'sourceLocation' => [
                            'file' => str_split(':', $record['context']['exception']['file'])[0],
                            'line' => str_split(':', $record['context']['exception']['file'])[1]
                        ]
                    ]
                    : [],
                [
                    'message' => $record['message'],
                    'thread' => $record['channel'],
                    'severity' => $record['level_name'],
                    'serviceContext' => $record['context'],
                    'timestamp' => $record['datetime']->getTimestamp(),
                    'httpRequest' => [
                        'requestMethod' => $request->getMethod(),
                        'requestUrl' => $request->getUri()->__toString(),
                        'requestSize' => $request->getBody()->getSize(),
                        'protocol' => $request->getProtocolVersion(),
                        'referer' => $request->getHeader('Referer'),
                        'userAgent' => $request->getHeader('User-Agent'),
                        'remoteIp' => $request->getHeader('X-Forwarded-For'),
                    ],
                ]
            )
        );
    }
}