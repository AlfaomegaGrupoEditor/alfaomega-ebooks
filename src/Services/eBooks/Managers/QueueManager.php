<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

class QueueManager extends AbstractManager
{
    /**
     * Retrieves the status of a queue.
     * This method retrieves the status of a queue by querying the WordPress actionscheduler_actions table.
     * It retrieves the number of actions with the specified queue name and status.
     *
     * @param string $queue The queue name to query.
     *
     * @return array Returns an associative array containing the queue name and the number of actions with the
     *               specified status.
     */
    public function status(string $queue): array
    {
        global $wpdb;
        global $table_prefix;

        $table = $table_prefix . "actionscheduler_actions";
        $results = $wpdb->get_results("
                SELECT status, count(*) as 'count'
                FROM $table a
                WHERE (a.hook like '$queue%' OR
                       (a.extended_args IS NULL AND a.args like '$queue%') OR
                       a.extended_args like '$queue%')
                GROUP BY status
            ");

        $data = [
            'queue'    => $queue,
            'complete' => 0,
            'failed'   => 0,
            'pending'  => 0,
        ];
        foreach ($results as $result) {
            $data[$result->status] = intval($result->count);
        }

        return $data;
    }

    /**
     * Clears a queue.
     * This method clears a queue by deleting all actions with the specified queue name and status.
     *
     * @return array Returns an empty array.
     */
    public function clear(): array
    {
        global $wpdb;
        $wpdb->get_results("
                DELETE
                FROM wp_actionscheduler_actions
                WHERE hook like '%alfaomega_ebooks_queue%'
                    AND status not in ('pending', 'in-process');
            ");

        return [];
    }
}
