<?php

namespace AlfaomegaEbooks\Services\eBooks\Transformers;

class ActionTransformer
{
    public static function transform(object $data): array
    {
        $extendedArgs = json_decode($data->extended_args, true);
        $details = !empty($extendedArgs) ? $extendedArgs[0] : null;
        if (!empty($details)) {
            $details = array_map(function ($value) {
                return str_replace(['<p>'], '', html_entity_decode($value));
            }, $details);
        }

        return [
            'id'               => $data->action_id,
            'isbn'             => !empty($details['isbn']) ? $details['isbn'] : null,
            'title'            => !empty($details['title']) ? $details['title'] : null,
            'status'           => $data->status,
            'schedule_date'    => $data->scheduled_date_gmt,
            'last_attend_date' => $data->last_attempt_gmt,
            'data'             => $details,
        ];
    }
}
