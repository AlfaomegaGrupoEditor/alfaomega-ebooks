<?php

$settings = array(
    /**
     * General Configuration
     */
    array(
        'name' => esc_html__('General Configuration', 'alfaomega-ebooks'),
        'type' => 'title',
        'id'   => $prefix . 'general_config_settings',
        'desc' => esc_html__('General settings to setup the service', 'alfaomega-ebooks'),
    ),
    array(
        'id'      => $prefix . 'active',
        'name'    => esc_html__('Active', 'alfaomega-ebooks'),
        'type'    => 'checkbox',
        'desc'    => esc_html__('Disable/Enable the plugin.', 'alfaomega-ebooks'),
        'default' => 'yes',
    ),
    array(
        'id'   => $prefix . 'username',
        'name' => esc_html__('Username', 'alfaomega-ebooks'),
        'type' => 'text',
        'desc' => esc_html__('User\'s email authorized to access Alfaomega Ebooks Platform. ', 'alfaomega-ebooks'),
    ),
    array(
        'id'   => $prefix . 'password',
        'name' => esc_html__('Password', 'alfaomega-ebooks'),
        'type' => 'password',
        'desc' => esc_html__('User\'s password authorized to access Alfaomega Ebooks Platform. ', 'alfaomega-ebooks'),
    ),
    array(
        'id'   => $prefix . 'notify_to',
        'name' => esc_html__('Notify to', 'alfaomega-ebooks'),
        'type' => 'text',
        'desc' => esc_html__('Email address to send a copy of every download code set to clients. ', 'alfaomega-ebooks'),
    ),
    array(
        'name' => esc_html__('General Configuration', 'alfaomega-ebooks'),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'general_config_settings',
    ),

    /**
     * Platform Settings
     */
    array(
        'name' => esc_html__( 'Platform Settings', 'alfaomega-ebooks' ),
        'type' => 'title',
        'id'   => $prefix . 'platform_settings',
        'desc' => esc_html__('Alfaomega external services to use by the Client Digital Library', 'alfaomega-ebooks'),
    ),
    array(
        'id'      => $prefix . 'reader',
        'name'    => esc_html__('Reader', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => esc_html__(' Reader app URL. ', 'alfaomega-ebooks'),
        'default' => 'https://reader.readonline.com.mx',
    ),
    array(
        'id'      => $prefix . 'panel',
        'name'    => esc_html__('Panel', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => esc_html__(' Publisher Panel URL. ', 'alfaomega-ebooks'),
        'default' => 'https://panel.bibliotecasdigitales.com',
    ),
    array(
        'name' => esc_html__('Platform Settings', 'alfaomega-ebooks'),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'platform_settings',
    ),

    /**
     * API Settings
     */
    array(
        'name' => esc_html__('API Settings', 'alfaomega-ebooksy'),
        'type' => 'title',
        'id'   => $prefix . 'api_settings',
        'desc'    => esc_html__(' Alfaomega API configuration. ', 'alfaomega-ebooks'),
    ),
    array(
        'id'      => $prefix . 'token',
        'name'    => esc_html__('Token URL', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => esc_html__(' Endpoint to renovate the access token. ', 'alfaomega-ebooks'),
        'default' => 'https://panel.bibliotecasdigitales.com/oauth/token',
    ),
    array(
        'id'      => $prefix . 'server',
        'name'    => esc_html__('API Server', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => esc_html__(' API Server URL. ', 'alfaomega-ebooks'),
        'default' => 'https://panel.bibliotecasdigitales.com/api/',
    ),
    array(
        'id'   => $prefix . 'client_id',
        'name' => esc_html__('Client Id', 'alfaomega-ebooks'),
        'type' => 'text',
    ),
    array(
        'id'   => $prefix . 'client_secret',
        'name' => esc_html__('Client Secret', 'alfaomega-ebooks'),
        'type' => 'password',
    ),
    array(
        'name' => esc_html__('API Settings', 'alfaomega-ebooks'),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'api_settings',
    ),
);
?>
