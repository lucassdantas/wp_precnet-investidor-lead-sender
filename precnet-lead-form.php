<?php 
/**
 * Plugin Name: Precnet Lead Form
 * Description: Envia os leads do formulário para a API
 * Plugin URI:  https://github.com/lucassdantas/wp_precnet-investidor-lead-sender
 * Version:     1.0.0
 * Author:      RD Exclusive
 * Author URI:  https://www.rdexclusive.com.br
 */

if (!defined('ABSPATH')) exit;
if (!function_exists('add_action')) die;


// Shortcode para renderizar o formulário
add_shortcode('precnet_lead_form', 'precnet_render_lead_form');

function precnet_render_lead_form() {
    ob_start(); ?>
    <form id="precnet-lead-form" class="precnet-lead-form">
        <div class="form-group">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" placeholder="NOME" required>
        </div>

        <div class="form-group">
            <label for="phone">Telefone (com DDD)</label>
            <input type="tel" id="phone" name="phone" placeholder="TELEFONE (COM DDD)" required>
        </div>

        <div class="form-group full-width">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="EMAIL" required>
        </div>

        <label class="terms-label">
            <input type="checkbox" name="terms" required>
            ACEITO OS <a href="https://investidor.precnet.com.br/politica-de-privacidade/" target="_blank">TERMOS</a> PARA USO DE DADOS.
        </label>

        <button type="submit">QUERO DESCOBRIR AGORA</button>

        <div id="precnet-lead-form-msg"></div>
    </form>
    <?php
    return ob_get_clean();
}


// Scripts
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('precnet-lead-form-css', plugin_dir_url(__FILE__) . 'css/lead-form.css');
    wp_enqueue_script('precnet-lead-form-js', plugin_dir_url(__FILE__) . 'js/lead-form.js', ['jquery'], null, true);
    wp_localize_script('precnet-lead-form-js', 'precnet_ajax', [
      'ajax_url' => admin_url('admin-ajax.php'),
    ]);
    wp_enqueue_script('precnet-lead-form-mask-js', plugin_dir_url(__FILE__) . 'js/inputs-mask.js', null, true);
}, 99);

// Handler AJAX (logged-in and non-logged-in users)
add_action('wp_ajax_precnet_send_lead', 'precnet_send_lead');
add_action('wp_ajax_nopriv_precnet_send_lead', 'precnet_send_lead');

function precnet_send_lead() {
    if (
        empty($_POST['name']) ||
        empty($_POST['phone']) ||
        empty($_POST['email']) ||
        empty($_POST['terms'])
    ) {
        wp_send_json_error('Todos os campos são obrigatórios.');
    }

    require_once plugin_dir_path(__FILE__) . 'src/apiCredentials.php';
    require_once plugin_dir_path(__FILE__) . 'src/formPayloadsAndApiEndpoint/form_investidor.php';

    $headers = [
        'Content-Type' => 'application/json',
        'Authorization' => $token,
    ];

    $response = wp_remote_post($apiBase . $apiEndpoint, [
        'headers' => $headers,
        'body' => json_encode($payload),
    ]);

    // 1. Se a API estiver indisponível (timeout, DNS, etc)
    if (is_wp_error($response)) {
        wp_send_json_error('Serviço indisponível, tente novamente mais tarde.');
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    // 2. Erro 500 da API
    if ($status_code === 500) {
        wp_send_json_error('Serviço indisponível, tente novamente mais tarde.');
    }

    // 3. Sucesso
    if ($status_code === 201) {

        // Envia e-mail com os dados do lead
        $to = 'investidor@precnet.com.br';
        $subject = 'Novo Lead do Formulário do Site';
        $message = "Nome: {$_POST['name']}\n";
        $message .= "Telefone: {$_POST['phone']}\n";
        $message .= "Email: {$_POST['email']}\n";
        $message .= "Aceitou os termos: Sim\n";

        $headers = ['Content-Type: text/plain; charset=UTF-8', 'Cc: lucasdantas.rdmarketingdigital@gmail.com'];

        $email_sent = wp_mail($to, $subject, $message, $headers);

        // Se o e-mail falhar
        if (!$email_sent) {
            wp_send_json_error('E-mail não enviado, tente novamente mais tarde.');
        }

        // Se tudo der certo
        wp_send_json_success([
            'message' =>  'Sucesso!',
            'redirect' => 'https://investidor.precnet.com.br/cadastro-realizado/',
        ]);
    }

    // 4. Outros erros (ex: 400)
    wp_send_json_error('Os campos e/ou valores enviados são inválidos. Verifique e tente novamente.');
}

