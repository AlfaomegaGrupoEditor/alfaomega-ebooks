<?php

namespace AlfaomegaEbooks\Services\eBooks\Transformers;

class ActionLogTransformer
{
    public static function transform(object $data): array
    {
        return [
            'id'      => $data->log_id,
            'message' => $data->message,
            'date'    => $data->log_date_gmt,
        ];
    }
}
