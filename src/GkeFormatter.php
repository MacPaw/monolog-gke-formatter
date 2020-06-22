<?php

namespace MacPaw\MonologGkeFormatter;

use Monolog\Formatter\JsonFormatter;

class GkeFormatter extends JsonFormatter
{
    public function format(array $record): string
    {
        return parent::format(
            array_merge(
                $record['extra'],
                isset($record['exception'])
                    ? [
                        'sourceLocation' => [
                            'file' => str_split(':', $record['exception']['file'])[0],
                            'line' => str_split(':', $record['exception']['file'])[1]
                        ]
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