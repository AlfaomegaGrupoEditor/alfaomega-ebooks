<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use AlfaomegaEbooks\Services\Alfaomega\Api;
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
     * QueueManager constructor.
     */
    public function __construct(Api $api,
                                array $settings)
    {
        global $table_prefix;

        parent::__construct($api, $settings);
        $this->table = $table_prefix . $this->table;
    }

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

        $results = $wpdb->get_results("
                SELECT status, count(*) as 'count'
                FROM {$this->table} a
                WHERE a.hook = '$queue'
                GROUP BY a.status
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
     * Clear the queue data
     * @param string $queue
     *
     * @return array
     */
    public function clear(string $queue): array
    {
        global $wpdb;

        $wpdb->get_results("
                DELETE
                FROM {$this->table} a
                WHERE hook = '$queue'
                    AND status not in ('in-process');
            ");

        return $this->status($queue, true);
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

        $offset = ($page - 1) * $perPage;
        $results = $wpdb->get_results("
            SELECT *
            FROM {$this->table} a
            WHERE a.hook = '$queue'
                AND a.status in ('" . join("','", $status) . "')
            ORDER BY a.status, a.scheduled_date_gmt DESC
            LIMIT $perPage OFFSET $offset;
        ");

        // TODO: Implement this.
        $data = [];
        foreach ($results as $result) {
            $data[] = ActionTransformer::transform($result);
        }

        $pages = $wpdb->get_results("
            SELECT count(*) as 'count'
            FROM {$this->table} a
            WHERE a.hook = '$queue'
                AND a.status in ('" . join("','", $status) . "')
        ");

        return [
            'data' => $data,
            'meta' => [
                'total'        => intval($pages[0]->count),
                'current_page' => $page,
                'pages'        => ceil(intval($pages[0]->count) / $perPage),
            ],
        ];
    }

    /**
     * Deletes an action from the queue.
     * This method deletes an action from the queue by removing the action with the specified queue name and action ID.
     *
     * @param string $queue   The queue name.
     * @param array $actionId The action IDs.
     *
     * @return array The status of the queue.
     * @throws \Exception
     */
    public function delete(string $queue, array $actionId): array
    {
        global $wpdb;

        $result = $wpdb->query("
                DELETE
                FROM {$this->table}
                WHERE hook = '$queue'
                    AND action_id in (" . join(",", $actionId) . ");
            ");
        if (empty($result)) {
            throw new \Exception(esc_html__('Failed to delete the action.', 'alfaomega-ebooks'), 500);
        }

        return $this->status($queue, true);
    }

    /**
     * Retries an action in the queue.
     * This method retries an action in the queue by updating the status of the action with the specified queue name
     * and action ID to 'pending'.
     *
     * @param string $queue   The queue name.
     * @param array $actionId The action ID.
     *
     * @return array The status of the queue.
     * @throws \Exception
     */
    public function retry(string $queue, array $actionId): array
    {
        global $wpdb;

        $result = $wpdb->query("
                UPDATE {$this->table}
                SET status = 'pending'
                WHERE hook = '$queue'
                    AND action_id in (" . join(",", $actionId) . ");
            ");
        if (empty($result)) {
            throw new \Exception(esc_html__('Failed adding actions to the pending queue.', 'alfaomega-ebooks'), 500);
        }

        return $this->status($queue, true);
    }
}
