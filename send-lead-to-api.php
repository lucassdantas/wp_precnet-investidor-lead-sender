<?php 
/**
 * Plugin Name: Send Lead To API
 * Description: Plugin to send elementor form to API
 * Plugin URI:  https://github.com/lucassdantas/wp_precnet-investidor-lead-sender
 * Version:     1.0.0
 * Author:      RD Exclusive
 * Author URI:  https://www.rdexclusive.com.br
 */

if (!defined('ABSPATH')) exit;
if (!function_exists('add_action')) die;

add_action('elementor_pro/forms/new_record', function ($record, $ajax_handler) {
    require_once plugin_dir_path(__FILE__) . 'src/apiCredentials.php';
   
    $headers = [
      'Content-Type' => 'application/json',
      "Authorization" => $token,
    ];
    $raw_fields = $record->get('fields');
    $fields = [];
    
    //nest values of field[id][value] directly on field[id]. Eg: fields['name'] = name, not fields['name']['value'] = name 
    foreach ($raw_fields as $id => $field) {$fields[$id] = $field['value'];}

    //put new fields values on the paylod or make them empty
    require_once plugin_dir_path(__FILE__) . 'src/formPayloadsAndApiEndpoint/form_investidor.php';
    
    $response = wp_remote_post($apiBase.$apiEndpoint, [
        'headers' => $headers,
        'body' => json_encode($payload),
    ]);

    if (is_wp_error($response)) $ajax_handler->data['output'] = $response->get_error_message(); 
    else $ajax_handler->data['output'] = wp_remote_retrieve_body($response);
}, 10, 2);
