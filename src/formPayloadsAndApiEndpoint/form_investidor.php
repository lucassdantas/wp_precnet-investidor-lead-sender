<?php 
if (!defined( 'ABSPATH')) exit;

//values of fields coming from parent send-lead-to-api.php 
$apiEndpoint = 'forms/investidor';

$payload = [
    'nome' => sanitize_text_field($_POST['name']),
    'telefone' => sanitize_text_field($_POST['phone']),
    'email' => sanitize_email($_POST['email']),
];