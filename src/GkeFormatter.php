<?php

namespace MacPaw\MonologGkeFormatter;

use GuzzleHttp\Psr7\ServerRequest;
use Monolog\Formatter\JsonFormatter;

class GkeFormatter extends JsonFormatter
{
    const BACKTRACE_DEFAULT_CALL = 6;

    protected $deepToBacktrace;
    protected $httpRequestContext;
    protected $sourceLocationContext;

    public function __construct(
        int $batchMode = self::BATCH_MODE_JSON,
        bool $appendNewline = true,
        bool $ignoreEmptyContextAndExtra = false,
        bool $httpRequestContext = false,
        bool $sourceLocationContext = false,
        int $deepToBacktrace = self::BACKTRACE_DEFAULT_CALL
    ) {
        parent::__construct($batchMode, $appendNewline, $ignoreEmptyContextAndExtra);
        $this->httpRequestContext = $httpRequestContext;
        $this->sourceLocationContext = $sourceLocationContext;
        $this->deepToBacktrace = $deepToBacktrace;
    }

    public function format(array $record): string
    {
        $request = ServerRequest::fromGlobals();
        $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,$this->deepToBacktrace);

        return parent::format(
            array_merge(
                $record['extra'],
                $this->sourceLocationContext && isset($debug[$this->deepToBacktrace-2])
                    ? [
                        'sourceLocation' => [
                            'file' => $debug[$this->deepToBacktrace-2]['file'],
                            'line' => $debug[$this->deepToBacktrace-2]['line'],
                            'function' => isset($debug[$this->deepToBacktrace-1]) && isset($debug[$this->deepToBacktrace-1]['class']) && isset($debug[$this->deepToBacktrace-1]['function'])
                                ? $debug[$this->deepToBacktrace-1]['class'] . $debug[$this->deepToBacktrace-1]['type'] . $debug[$this->deepToBacktrace-1]['function']
                                : (
                                isset($debug[$this->deepToBacktrace-1]) && isset($debug[$this->deepToBacktrace-1]['function'])
                                    ? $debug[$this->deepToBacktrace-1]['function']
                                    : ''
                                ),
                        ]
                    ]
                    : [],
                $this->httpRequestContext && preg_match('/cgi/', php_sapi_name())
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