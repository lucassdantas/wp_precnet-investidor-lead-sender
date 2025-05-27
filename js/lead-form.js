jQuery(document).ready(function ($) {
    $('#precnet-lead-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $msg = $('#precnet-lead-form-msg');
        const $button = $form.find('button[type="submit"]');
        const formData = $form.serialize();

        //$msg.text('Enviando...').css('color', '#333');
        $button.prop('disabled', true).text('Enviando...');

        $.post(precnet_ajax.ajax_url, {
            action: 'precnet_send_lead',
            ...Object.fromEntries(new URLSearchParams(formData)),
        }, function (response) {
            if (response.success) {
                $msg.text(response.data.message).css('color', 'green');
                $button.text('QUERO DESCOBRIR AGORA');
                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 1000);
            } else {
                $msg.text(response.data || 'Erro ao enviar. Verifique os campos ou tente novamente mais tarde').css('color', 'red');
                $button.prop('disabled', false).text('QUERO DESCOBRIR AGORA');
            }
        }).fail(() => {
            $msg.text('Erro ao conectar. Tente novamente mais tarde.').css('color', 'red');
            $button.prop('disabled', false).text('QUERO DESCOBRIR AGORA');
        });
    });
});
