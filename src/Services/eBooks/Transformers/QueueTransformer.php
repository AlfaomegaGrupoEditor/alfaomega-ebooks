<?php

namespace AlfaomegaEbooks\Services\eBooks\Transformers;

class QueueTransformer
{
    public static function transform(array $data): array
    {
        if ($data['in-process'] > 0 || $data['pending'] > 0) {
            $status = 'processing';
        } elseif ($data['failed'] > 0) {
            $status = 'failed';
        } elseif ($data['complete'] > 0) {
            $status = 'completed';
        } else {
            $status = 'idle';
        }

        return [
            'status'    => $status,
            'completed' => $data['complete'],
            'processing'=> $data['in-process'],
            'pending'   => $data['pending'],
            'failed'    => $data['failed'],
        ];
    }
}
