<?php

$settings = array(
    /**
     * General Configuration
     */
    array(
        'name' => __('General Configuration', 'alfaomega-ebooks'),
        'type' => 'title',
        'id'   => $prefix . 'general_config_settings',
        'desc' => __('General settings to setup the service', 'alfaomega-ebooks'),
    ),
    array(
        'id'      => $prefix . 'active',
        'name'    => __('Active', 'alfaomega-ebooks'),
        'type'    => 'checkbox',
        'desc'    => __(' Disable/Enable the plugin.', 'alfaomega-ebooks'),
        'default' => 'yes',
    ),
    array(
        'id'   => $prefix . 'username',
        'name' => __('Username', 'alfaomega-ebooks'),
        'type' => 'text',
        'desc' => __(' User\'s email authorized to access Alfaomega Ebooks Platform. ', 'alfaomega-ebooks'),
    ),
    array(
        'id'   => $prefix . 'password',
        'name' => __('Password', 'alfaomega-ebooks'),
        'type' => 'password',
        'desc' => __(' User\'s password authorized to access Alfaomega Ebooks Platform. ', 'alfaomega-ebooks'),
    ),
    array(
        'id'   => $prefix . 'notify_to',
        'name' => __('Notify to', 'alfaomega-ebooks'),
        'type' => 'text',
        'desc' => __(' Email address to send a copy of every download code set to clients. ', 'alfaomega-ebooks'),
    ),
    array(
        'name' => __('General Configuration', 'alfaomega-ebooks'),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'general_config_settings',
    ),

    /**
     * Platform Settings
     */
    array(
        'name' => __( 'Platform Settings', 'alfaomega-ebooks' ),
        'type' => 'title',
        'id'   => $prefix . 'platform_settings',
        'desc' => __('Alfaomega external services to use by the Client Digital Library', 'alfaomega-ebooks'),
    ),
    array(
        'id'      => $prefix . 'reader',
        'name'    => __('Reader', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => __(' Reader app URL. ', 'alfaomega-ebooks'),
        'default' => 'https://reader.readonline.com.mx',
    ),
    array(
        'id'      => $prefix . 'panel',
        'name'    => __('Panel', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => __(' Publisher Panel URL. ', 'alfaomega-ebooks'),
        'default' => 'https://panel.bibliotecasdigitales.com',
    ),
    array(
        'name' => __('Platform Settings', 'alfaomega-ebooks'),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'platform_settings',
    ),

    /**
     * API Settings
     */
    array(
        'name' => __('API Settings', 'alfaomega-ebooksy'),
        'type' => 'title',
        'id'   => $prefix . 'api_settings',
        'desc'    => __(' Alfaomega API configuration. ', 'alfaomega-ebooks'),
    ),
    array(
        'id'      => $prefix . 'token',
        'name'    => __('Token URL', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => __(' Endpoint to renovate the access token. ', 'alfaomega-ebooks'),
        'default' => 'https://panel.bibliotecasdigitales.com/oauth/token',
    ),
    array(
        'id'      => $prefix . 'server',
        'name'    => __('API Server', 'alfaomega-ebooks'),
        'type'    => 'text',
        'desc'    => __(' API Server URL. ', 'alfaomega-ebooks'),
        'default' => 'https://panel.bibliotecasdigitales.com/api/',
    ),
    array(
        'id'   => $prefix . 'client_id',
        'name' => __('Client Id', 'alfaomega-ebooks'),
        'type' => 'text',
    ),
    array(
        'id'   => $prefix . 'client_secret',
        'name' => __('Client Secret', 'alfaomega-ebooks'),
        'type' => 'password',
    ),
    array(
        'name' => __('API Settings', 'alfaomega-ebooks'),
        'type' => 'sectionend',
        'desc' => '',
        'id'   => $prefix . 'api_settings',
    ),
);
?>
