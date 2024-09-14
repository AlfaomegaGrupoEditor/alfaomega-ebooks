<?php

namespace AlfaomegaEbooks\Services\eBooks\Emails;

use WC_Email;

/**
 * Class Alfaomega_Ebooks_Sample_Email
 *
 * @package Alfaomega_Ebooks
 */
class AlfaomegaEbooksSampleEmail extends WC_Email {
    public function __construct() {
        $this->id = 'sample_email';
        $this->title = __('Access code email', 'alfaomega-ebook');
        $this->description = __('An email with the access code to activate the sample.', 'alfaomega-ebook');
        $this->heading = __('Ebook Sample Access Codes', 'alfaomega-ebook');
        $this->subject = __('Alfaomega eBook access code', 'alfaomega-ebook');
        $this->customer_email = true;

        // Set the templates for HTML and plain email versions
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
    public function trigger(array $sample): bool {
        if ( ! $sample ) {
            return false;
        }

        $this->object = $sample;
        $this->recipient = $this->object['destination'];

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return false;
        }

        return $this->send(
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
    public function get_content_html()
    {
        return wc_get_template_html(
            $this->template_html,
            [
                'sample'             => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'sent_to_admin'      => false,
                'plain_text'         => false,
                'email'              => $this,
            ],
            '',
            ALFAOMEGA_EBOOKS_PATH . 'views/emails/'
        );
    }

    /**
     * Get email plain content.
     *
     * @return string
     */
    public function get_content_plain()
    {
        return wc_get_template_html(
            $this->template_plain,
            [
                'sample'             => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'sent_to_admin'      => false,
                'plain_text'         => false,
                'email'              => $this,
            ],
            '',
            ALFAOMEGA_EBOOKS_PATH . 'views/emails/'
        );
    }

    /**
     * Default content to show below main email content.
     *
     * @since 3.7.0
     * @return string
     */
    public function get_default_additional_content() {
        return __( 'We hope you enjoy your reading experience. Thanks for your confidence.', 'alfaomega-ebook' );
    }
}
