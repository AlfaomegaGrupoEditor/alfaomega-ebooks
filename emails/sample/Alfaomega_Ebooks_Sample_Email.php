<?php

namespace emails\sample;

use AlfaomegaEbooks\Services\eBooks\Service;
use WC_Email;

if ( ! class_exists( 'Alfaomega_Ebooks_Sample_Email' ) ) {

    /**
     * Class Alfaomega_Ebooks_Sample_Email
     *
     * @package Alfaomega_Ebooks
     */
    class Alfaomega_Ebooks_Sample_Email extends WC_Email {
        public function __construct() {
            $this->id = 'sample_email';
            $this->title = __('Sample Code Email', 'text-domain');
            $this->description = __('Sample code email.', 'text-domain');
            $this->heading = __('Sample code email', 'text-domain');
            $this->subject = __('Sample code email', 'text-domain');

            $this->template_html = 'html-sample-email.php';
            $this->template_plain = 'plain-sample-email.php';

            // Call parent constructor
            parent::__construct();
        }

        /**
         * Send the email to the recipient.
         * @param array $sample
         *
         * @return void
         * @throws \Exception
         */
        public function trigger(array $sample) {
            if ( ! $sample ) {
                return;
            }

            $this->object = $sample;
            $this->recipient = $this->object['destination'];

            if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
                return;
            }

            $this->send(
                $this->get_recipient(),
                $this->get_subject(),
                $this->get_content(),
                $this->get_headers(),
                $this->get_attachments()
            );
        }

        /**
         * Get email subject.
         *
         * @return string
         */
        public function get_content_html() {
            return wc_get_template_html($this->template_html, array(
                'sample' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $this,
            ));
        }

        /**
         * Get email plain content.
         *
         * @return string
         */
        public function get_content_plain() {
            return wc_get_template_html($this->template_plain, array(
                'order' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email' => $this,
            ));
        }
    }
}
