<?php 
if (!defined( 'ABSPATH')) exit;

//values of fields coming from parent send-lead-to-api.php 
$apiEndpoint = 'forms/investidor';
$payload = [
  "nome" => $fields['nome'] ?? '',
  "telefone" => $fields['telefone'] ?? '',
  "email" => $fields['email'] ?? '',
];