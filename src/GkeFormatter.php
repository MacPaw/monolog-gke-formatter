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
                isset($record['file'])
                    ? [
                        'sourceLocation' => [
                            'file' => str_split(':', $record['file'])[0],
                            'line' => str_split(':', $record['file'])[1]
                        ]
                    ]
                    : [],
                [
                    'message' => $record['message'],
                    'thread' => $record['channel'],
                    'severity' => $record['level_name'],
                    'serviceContext' => $record['context'],
                    'timestamp' => $record['datatime']->getTimestamp(),
                ]
            )
        );
    }
}