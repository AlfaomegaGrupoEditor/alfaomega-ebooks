<?php

namespace AlfaomegaEbooks\Services\eBooks\Transformers;

class ActionTransformer
{
    public static function transform(array $data): array
    {
        return [
            'id'               => $data['id'],
            'isbn'             => $data['args'],
            'title'            => $data['title'],
            'status'           => $data['status'],
            'schedule_date'    => $data['scheduled_date'],
            'last_attend_date' => $data['last_attempt_date'],
            'data'             => $data['data'],
        ];
    }
}
