<?php

namespace AlfaomegaEbooks\Services\eBooks\Managers;

use ActionScheduler_QueueRunner;
use AlfaomegaEbooks\Services\Alfaomega\Api;
use AlfaomegaEbooks\Services\eBooks\Service;
use AlfaomegaEbooks\Services\eBooks\Transformers\ActionLogTransformer;
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
    protected string $logsTable = 'actionscheduler_logs';

    /**
     * QueueManager constructor.
     */
    public function __construct(Api $api,
                                array $settings)
    {
        global $table_prefix;

        parent::__construct($api, $settings);
        $this->table = $table_prefix . $this->table;
        $this->logsTable = $table_prefix . $this->logsTable;
    }

    /**
     * Executes the job queue runner to process the scheduled actions.
     *
     * @return void
     */
    public function run(): void
    {
        $queue_runner = new ActionScheduler_QueueRunner();
        $queue_runner->run();
    }

    /**
     * Retrieves the status of a queue.
     * This method retrieves the status of a queue by counting the number of actions with the specified queue name and
     * status.
     *
     * @param string $queue   The queue name.
     * @param bool $transform Whether to transform the result.
     *
     * @return array The status of the queue.
     * @throws \Exception
     */
    public function status(string $queue, bool $transform = false): array
    {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT status, count(*) as 'count'
            FROM {$this->table} a
            WHERE a.hook = %s
            GROUP BY a.status
        ", $queue);
        $results = $wpdb->get_results($query);

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

        if ($queue === 'alfaomega_ebooks_queue_import') {
            // ONLY show the import failed items if the queue
            // 'alfaomega_ebooks_queue_import' is empty
            if ($data['failed'] === 0) {
                $failedImport = Service::make()
                    ->ebooks()
                    ->ebookPost()
                    ->getImportList('failed');
                $data['failed'] += $failedImport['meta']['total'];
            }

            $excludedImport = Service::make()
                ->ebooks()
                ->ebookPost()
                ->getImportList('excluded');
            $data['excluded'] = $excludedImport['meta']['total'];
        }

        return $transform ? QueueTransformer::transform($data) : $data;
    }

    /**
     * Clears actions from the specified queue that are not in 'in-process' status.
     *
     * @param string $queue The specific queue from which to clear actions.
     *
     * @return array Returns an array containing the updated status of the queue.
     * @throws \Exception
     */
    public function clear(string $queue): array
    {
        global $wpdb;

        $query = $wpdb->prepare("
            DELETE FROM {$this->table}
                WHERE hook = %s
                    AND status not in ('in-process')
        ", $queue);
        $wpdb->get_results($query);

        Service::make()->ebooks()->ebookPost()
            ->updateImported([], 'delete');

        return $this->status($queue, true);
    }

    /**
     * Retrieves a list of actions with pagination and status filtering.
     *
     * @param string $queue The specific queue to filter actions by.
     * @param array $status An array of statuses to filter actions by.
     * @param int $page     The current page number for pagination.
     * @param int $perPage  The number of items per page.
     *
     * @return array Returns an array containing the data of the actions and meta information for pagination.
     * @throws \Exception
     */
    public function actions(string $queue, array $status, int $page = 1, int $perPage = 10): array
    {
        global $wpdb;

        if (!in_array('excluded', $status)) {
            $offset = ($page - 1) * $perPage;
            $query = $wpdb->prepare("
                SELECT *
                FROM {$this->table} a
                WHERE a.hook = %s
                    AND a.status in (" . join(",", array_fill(0, count($status), '%s')) . ")
                ORDER BY a.status, a.scheduled_date_gmt DESC
                LIMIT %d OFFSET %d;
            ", array_merge([$queue], $status, [$perPage, $offset]));
            $results = $wpdb->get_results($query);

            $data = [];
            foreach ($results as $result) {
                $data[] = array_merge(
                    ActionTransformer::transform($result), [
                        'logs' => $this->logs($result->action_id)
                    ]);
            }

            // ONLY show the import failed items if the queue
            // 'alfaomega_ebooks_queue_import' is empty
            $importFailedTotal = 0;
            if ($queue === 'alfaomega_ebooks_queue_import'
                && $status === ['failed']
                && count($results) === 0) {
                $failedImport = Service::make()
                    ->ebooks()
                    ->ebookPost()
                    ->getImportList('failed', $page, $perPage);

                foreach ($failedImport['data'] as $import) {
                    $import['type'] = 'import';
                    $import['logs'] = $import['logs']['data'];
                    $data[] = $import;
                }
                $importFailedTotal = $failedImport['meta']['total'];
            }

            $query = $wpdb->prepare("
                SELECT count(*) as 'count'
                FROM {$this->table} a
                WHERE a.hook = %s
                    AND a.status in (" . join(",", array_fill(0, count($status), '%s')) . ")
                ", array_merge([$queue], $status));
            $pages = $wpdb->get_results($query);
            $pagesCount = intval($pages[0]->count) + $importFailedTotal;
        } else {
            $data = [];
            $excludedImport = Service::make()
                ->ebooks()
                ->ebookPost()
                ->getImportList('excluded', $page, $perPage);

            foreach ($excludedImport['data'] as $import) {
                $import['type'] = 'import';
                $import['logs'] = $import['logs']['data'];
                $data[] = $import;
            }
            $pagesCount = $excludedImport['meta']['total'];
        }

        return [
            'data' => $data,
            'meta' => [
                'total'        => $pagesCount,
                'current_page' => $page,
                'pages'        => ceil($pagesCount / $perPage),
            ],
        ];
    }

    /**
     * Retrieves the log of an action.
     * This method retrieves the log of an action by selecting all log entries with the specified action ID.
     *
     * @param int $actionId The action ID.
     *
     * @return array The log of the action.
     */
    public function logs(int $actionId): array
    {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT *
            FROM {$this->logsTable} a
            WHERE a.action_id = %s
            ORDER BY a.log_date_gmt DESC;", $actionId);
        $results = $wpdb->get_results($query);

        $data = [];
        foreach ($results as $result) {
            $data[] = ActionLogTransformer::transform($result);
        }

        return $data;
    }

    /**
     * Deletes actions from the specified queue and updates the status of imported ebooks if applicable.
     *
     * @param string $queue   The specific queue from which actions are to be deleted.
     * @param array $actionId An array of action IDs to be deleted.
     * @param string $type    The type of action to delete, default is 'action'.
     *
     * @return array Returns an array with the status of the deletion process.
     * @throws \Exception If the deletion of actions fails.
     */
    public function delete(string $queue, array $actionId, string $type = 'action'): array
    {
        global $wpdb;

        if ($type === 'action') {
            if ($queue === 'alfaomega_ebooks_queue_import') {
                $actions = $this->getActionsById($queue, $actionId);
                $isbns = array_column($actions, 'isbn');
                Service::make()->ebooks()->ebookPost()
                    ->updateImported($isbns, 'failed', errorCode: 'failed_action_deleted');
            }

            $query = $wpdb->prepare("
                    DELETE
                    FROM {$this->table}
                    WHERE hook = %s
                        AND action_id in (" . join(",", array_fill(0, count($actionId), '%s')) . ");
                ", array_merge([$queue], $actionId));
            $wpdb->get_results($query);

            if ($wpdb->rows_affected === 0) {
                throw new \Exception(esc_html__('Failed to delete the action.', 'alfaomega-ebooks'), 500);
            }
        } else {
            Service::make()->ebooks()->ebookPost()
                ->updateImported($actionId, 'delete');
        }

        return $this->status($queue, true);
    }

    /**
     * Retries the specified actions by setting their status to 'pending' and rescheduling them.
     *
     * @param string $queue   The specific queue to filter actions by.
     * @param array $actionId An array of action IDs to retry.
     * @param string $type    The type of action, default is 'action'.
     *
     * @return array Returns an array with the status of the actions after retrying.
     * @throws \Exception If the actions could not be added to the pending queue.
     */
    public function retry(string $queue, array $actionId, string $type = 'action'): array
    {
        global $wpdb;

        if ($type === 'action') {
            $payload = $this->refreshActionsPayload($queue, $actionId);
            if (empty($payload)) {
                $query = $wpdb->prepare("
                        UPDATE {$this->table}
                        SET status = 'pending',
                            scheduled_date_gmt = DATE_ADD(NOW(), INTERVAL 1 minute),
                            scheduled_date_local = DATE_ADD(NOW(), INTERVAL 1 minute)
                        WHERE hook = %s
                            AND action_id in (" . join(",", array_fill(0, count($actionId), '%s')) . ");
                    ", array_merge([$queue], $actionId));
                $wpdb->get_results($query);

                if ($wpdb->rows_affected === 0) {
                    throw new \Exception(esc_html__('Failed adding actions to the pending queue.', 'alfaomega-ebooks'), 500);
                }
            } else {
                $errors = 0;
                foreach ($payload as $actionId => $args) {
                    $query = $wpdb->prepare("
                        UPDATE {$this->table}
                        SET status = 'pending',
                            extended_args = %s,
                            scheduled_date_gmt = DATE_ADD(NOW(), INTERVAL 1 minute),
                            scheduled_date_local = DATE_ADD(NOW(), INTERVAL 1 minute)
                        WHERE hook = %s
                            AND action_id = %d;
                    ", [$args, $queue, $actionId]);
                    $wpdb->get_results($query);

                    if ($wpdb->rows_affected === 0) {
                        $errors++;
                    }
                }

                if ($errors > 0) {
                    throw new \Exception(esc_html__('Failed adding actions to the pending queue.', 'alfaomega-ebooks'), 500);
                }
            }

            $this->run();
        } else {
            Service::make()->ebooks()->ebookPost()
                ->updateImported($actionId, 'delete');

            // NOTE: In the frontend, importNewEbooks will be called, but no only the items in the
            //      list will be retried, there is no guaranty either that all items will be retried
            //      in the first attempt.
        }

        return $this->status($queue, true);
    }

    /**
     * Excludes specific actions from a given queue by updating their status to 'excluded'.
     *
     * @param string $queue   The specific queue to filter actions by.
     * @param array $actionId An array of action IDs to be excluded.
     *
     * @return array Returns an array containing the status of actions in the queue after exclusion.
     * @throws \Exception
     */
    public function exclude(string $queue, array $actionId): array
    {
        Service::make()->ebooks()->ebookPost()
                ->updateImported($actionId, 'excluded', errorCode: 'manually_excluded');

        return $this->status($queue, true);
    }

    /**
     * Retrieves actions based on their IDs.
     * This method retrieves actions by selecting all actions with the specified queue name and action IDs.
     *
     * @param string $queue   The queue name.
     * @param array $actionId The IDs of the actions.
     *
     * @return array The actions associated with the given IDs.
     */
    public function getActionsById(string $queue, array $actionId): array
    {
        global $wpdb;
        $query = $wpdb->prepare("
            SELECT *
            FROM {$this->table} a
            WHERE a.hook = %s
                AND a.action_id in (" . join(",", array_fill(0, count($actionId), '%s')) . ")
            ORDER BY a.status, a.scheduled_date_gmt DESC;
        ", array_merge([$queue], $actionId));
        $results = $wpdb->get_results($query);
        
        $data = [];
        foreach ($results as $result) {
            $data[] = array_merge(
                ActionTransformer::transform($result), [
                    'logs' => $this->logs($result->action_id)
                ]);
        }
        
        return $data;
    }

    /**
     * Refreshes the payload of the actions.
     * This method refreshes the payload of the actions by selecting all actions with the specified queue name and action IDs.
     *
     * @param string $queue   The queue name.
     * @param array $actionId The IDs of the actions.
     *
     * @return array|null The refreshed payload of the actions.
     */
    protected function refreshActionsPayload(string $queue, array $actionId): ?array
    {
        global $wpdb;

        // check if enabled for the current queue
        $service = match ($queue) {
            'alfaomega_ebooks_queue_setup_price' => Service::make()->wooCommerce()->updatePrice(),
            default => null,
        };
        if (empty($service)) {
            return null;
        }

        $query = $wpdb->prepare("
            SELECT action_id, extended_args
            FROM {$this->table}
            WHERE hook = %s
               AND action_id in (" . join(",", array_fill(0, count($actionId), '%s')) . ");
        ", array_merge([$queue], $actionId));
        $results = $wpdb->get_results($query);

        $payload = [];
        foreach ($results as $result) {
            $extendedArgs = json_decode($result->extended_args, true);
            [$eBook, $throwError, $postId] = $extendedArgs;

            $service->setFactor(
                $eBook['factor'] ?? 'price_update',
                floatval($eBook['value']) ?? 1
            );
            $payload[$result->action_id] = !empty($postId)
                ? json_encode([ $service->getPayload(intval($postId)), $throwError, $postId ])
                : $result->extended_args;
        }

        return $payload;
    }
}
