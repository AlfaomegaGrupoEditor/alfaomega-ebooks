<?php

namespace AlfaomegaEbooks\Services\eBooks;

/**
 * Based on Action Scheduler High Volume Plugin
 * URI: https://github.com/prospress/action-scheduler-high-volume
 * Description: Increase Action Scheduler batch size, concurrency and timeout period to process large queues of actions
 * more quickly on servers with more server resources.
 * Author: Prospress Inc.
 * Author URI: http://prospress.com/
 * Version: 1.1.0
 * Copyright 2018 Prospress, Inc.  (email : freedoms@prospress.com)
 */
class ActionSchedulerSetup
{
    protected const ACTION_SCHEDULER_ENABLED = true;
    protected const ACTION_SCHEDULER_BATCH_SIZE = 20;
    protected const ACTION_SCHEDULER_CONCURRENT_BATCHES = 5;
    protected const ACTION_SCHEDULER_TIMEOUT = 3;
    protected const ACTION_SCHEDULER_TIME_LIMIT = 120;

    /**
     * Action scheduler claims a batch of actions to process in each request. It keeps the batch
     * fairly small (by default, 25) in order to prevent errors, like memory exhaustion.
     *
     * This method increases it so that more actions are processed in each queue, which speeds up the
     * overall queue processing time due to latency in requests and the minimum 1 minute between each
     * queue being processed.
     *
     * For more details, see: https://actionscheduler.org/perf/#increasing-batch-size
     */
    public function ashp_increase_queue_batch_size( $batch_size ) {
        return $batch_size * self::ACTION_SCHEDULER_BATCH_SIZE;
    }

    /**
     * Action scheduler processes queues of actions in parallel to speed up the processing of large numbers
     * If each queue takes a long time, this will result in multiple PHP processes being used to process actions,
     * which can prevent PHP processes being available to serve requests from visitors. This is why it defaults to
     * only 5. However, on high volume sites, this can be increased to speed up the processing time for actions.
     *
     * This method hextuples the default so that more queues can be processed concurrently. Use with caution as doing
     * this can take down your site completely depending on your PHP configuration.
     *
     * For more details, see: https://actionscheduler.org/perf/#increasing-concurrent-batches
     */
    public function ashp_increase_concurrent_batches( $concurrent_batches ) {
        return $concurrent_batches * self::ACTION_SCHEDULER_CONCURRENT_BATCHES;
    }

    /**
     * Action scheduler reset actions claimed for more than 5 minutes. Because we're increasing the batch size, we
     * also want to increase the amount of time given to queues before reseting claimed actions.
     */
    public function ashp_increase_timeout( $timeout ) {
        return $timeout * self::ACTION_SCHEDULER_TIMEOUT;
    }

    /**
     * Action scheduler initiates one queue runner every time the 'action_scheduler_run_queue' action is triggered.
     *
     * Because this action is only triggered at most once every minute, that means it would take 30 minutes to spin
     * up 30 queues. To handle high volume sites with powerful servers, we want to initiate additional queue runners
     * whenever the 'action_scheduler_run_queue' is run, so we'll kick off secure requests to our server to do that.
     */
    public function ashp_request_additional_runners() {

        // allow self-signed SSL certificates
        add_filter( 'https_local_ssl_verify', '__return_false', 100 );

        for ( $i = 0; $i < 5; $i++ ) {
            $response = wp_remote_post( admin_url( 'admin-ajax.php' ), array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => false,
                'headers'     => array(),
                'body'        => array(
                    'action'     => 'ashp_create_additional_runners',
                    'instance'   => $i,
                    'ashp_nonce' => wp_create_nonce( 'ashp_additional_runner_' . $i ),
                ),
                'cookies'     => array(),
            ) );
        }
    }

    /**
     * Handle requests initiated by ashp_request_additional_runners() and start a queue runner if the request is valid.
     */
    public function ashp_create_additional_runners() {
        if ( isset( $_POST['ashp_nonce'] ) && isset( $_POST['instance'] ) && wp_verify_nonce( $_POST['ashp_nonce'], 'ashp_additional_runner_' . $_POST['instance'] ) ) {
            ActionScheduler_QueueRunner::instance()->run();
        }

        wp_die();
    }

    /**
     * Action Scheduler provides a default maximum of 30 seconds in which to process actions. Increase this to 120
     * seconds for hosts like Pantheon which support such a long time limit, or if you know your PHP and Apache, Nginx
     * or other web server configs support a longer time limit.
     *
     * Note, WP Engine only supports a maximum of 60 seconds - if using WP Engine, this will need to be decreased to 60.
     */
    public function ashp_increase_time_limit() {
        return self::ACTION_SCHEDULER_TIME_LIMIT;
    }

    /**
     * Check if Action Scheduler is enabled.
     *
     * @return bool
     */
    public function enabled(): bool
    {
        return self::ACTION_SCHEDULER_ENABLED;
    }

    /**
     * Retry past due tasks.
     * @return void
     */
    function retry_past_due_tasks() {
        $past_due_actions = as_get_scheduled_actions([
            'status' => 'pending',
            'per_page' => -1,
        ]);

        foreach ($past_due_actions as $actionId => $action) {
            as_enqueue_async_action($action->get_hook(), $action->get_args(), $action->get_group());
            as_unschedule_action($action->get_hook(), $action->get_args(), $action->get_group());
        }
    }
}
