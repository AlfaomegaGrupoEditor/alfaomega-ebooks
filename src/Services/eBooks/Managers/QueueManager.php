<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\eBooks\Transformers\ActionTransformer;
use AlfaomegaEbooks\Services\eBooks\Transformers\QueueTransformer;

class QueueManager extends AbstractManager
{
    /**
     * The table name.
     *
     * @var string
     */
    protected string $table = 'actionscheduler_actions';

    /**
     * Retrieves the status of a queue.
     * This method retrieves the status of a queue by counting the number of actions with the specified queue name and status.
     *
     * @param string $queue The queue name.
     * @param bool $transform Whether to transform the result.
     *
     * @return array The status of the queue.
     */
    public function status(string $queue, bool $transform = false): array
    {
        global $wpdb;

        $table = $this->getTable();
        $results = $wpdb->get_results("
                SELECT status, count(*) as 'count'
                FROM $table a
                WHERE (a.hook like '$queue%' OR
                       (a.extended_args IS NULL AND a.args like '$queue%') OR
                       a.extended_args like '$queue%')
                GROUP BY status
            ");

        $data = [
            'queue'      => $queue,
            'complete'   => 0,
            'failed'     => 0,
            'pending'    => 0,
            'in-process' => 0,
        ];
        foreach ($results as $result) {
            $data[$result->status] = intval($result->count);
        }

        return $transform ? QueueTransformer::transform($data) : $data;
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

        $table = $this->getTable();
        $wpdb->get_results("
                DELETE
                FROM $table
                WHERE hook like '%alfaomega_ebooks_queue%'
                    AND status not in ('pending', 'in-process');
            ");

        return [];
    }

    /**
     * Retrieves the table name.
     *
     * @return string The table name.
     */
    public function getTable(): string
    {
        global $table_prefix;
        $this->table = $table_prefix . $this->table;

        return $this->table;
    }

    /**
     * Retrieves the actions of a queue.
     * This method retrieves the actions of a queue by selecting all actions with the specified queue name and status.
     *
     * @param string $queue The queue name.
     * @param array $status The status of the actions.
     * @param int $page The page number.
     * @param int $perPage The number of actions per page.
     *
     * @return array The actions of the queue.
     */
    public function actions(string $queue, array $status, int $page = 1, int $perPage = 10): array
    {
        global $wpdb;

        $table = $this->getTable();
        $offset = ($page - 1) * $perPage;
        $results = $wpdb->get_results("
            SELECT *
            FROM $table a
            WHERE (a.hook like '$queue%' OR
                   (a.extended_args IS NULL AND a.args like '$queue%') OR
                   a.extended_args like '$queue%')
                AND a.status in ('" . join("','", $status) . "')
            ORDER BY a.status, a.scheduled_date_gmt DESC
            LIMIT $perPage OFFSET $offset;
        ");

        // TODO: Implement this.
        $data = [];
        foreach ($results as $result) {
            $data[] = ActionTransformer::transform($result);
        }

        return [
            'data' => $data,
            'meta' => [
                'page'    => $page,
                'perPage' => $perPage,
                'pages'   => ceil(count($data) / $perPage),
            ],
        ];
    }
}
