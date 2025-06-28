<?php
/*
Plugin Name: Prompt Builder
Description: Um plugin WordPress que ajuda a gerar prompts estruturados para inteligências artificiais.
Version: 1.0.0
Author: Arthur Felizdoro
Text Domain: prompt-builder
*/

if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', function () {
    load_plugin_textdomain('prompt-builder', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

add_action('admin_menu', function () {
    add_submenu_page(
        'tools.php',
        __('Prompt Builder', 'prompt-builder'),
        __('Prompt Builder', 'prompt-builder'),
        'manage_options',
        'prompt-builder',
        'pb_render_admin_page'
    );
});

add_action('admin_init', function () {
    register_setting(
        'prompt_builder_settings_group',
        'pb_ia_api_key',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );

    add_settings_section(
        'pb_ia_api_section',
        __('Configurações da API para a IA', 'prompt-builder'),
        function () {
            echo '<p>' . esc_html__('Coloque sua API Key', 'prompt-builder') . '</p>';
        },
        'prompt-builder'
    );

    add_settings_field(
        'pb_ia_api_key_field',
        __('Chave da API da IA', 'prompt-builder'),
        function () {
            $api_key = get_option('pb_ia_api_key');
            echo '<input type="password" name="pb_ia_api_key" value="' . esc_attr($api_key) . '" class="regular-text" placeholder="' . esc_attr__('API Key', 'prompt-builder') . '" />';
            echo '<p class="description">' . esc_html__('Solicite a API key ao desenvolvedor', 'prompt-builder') . '</p>';
        },
        'prompt-builder',
        'pb_ia_api_section'
    );
});


function pb_render_admin_page()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html__('Prompt Builder', 'prompt-builder'); ?></h1>
        <div id="pb-messages" class="notice" style="display: none;"></div>

        <div class="postbox">
            <h2 class="hndle"><span><?php echo esc_html__('Configurações', 'prompt-builder'); ?></span></h2>
            <div class="inside">
                <form method="post" action="options.php">
                    <?php settings_fields('prompt_builder_settings_group'); ?>
                    <?php do_settings_sections('prompt-builder'); ?>
                    <?php submit_button(__('Salvar', 'prompt-builder')); ?>
                </form>
            </div>
        </div>

        <div class="postbox">
            <h2 class="hndle"><span><?php echo esc_html__('Gerador de Prompts', 'prompt-builder'); ?></span></h2>
            <div class="inside">
                <form id="pb-form">
                    <h3><?php echo esc_html__('Briefing', 'prompt-builder'); ?></h3>
                    <textarea name="base_prompt" rows="4" cols="60" placeholder="<?php echo esc_attr__('Escreva seu briefing aqui...', 'prompt-builder'); ?>"></textarea>

                    <h3><?php echo esc_html__('Requisitos', 'prompt-builder'); ?></h3>
                    <div id="pb-requisitos">
                        <div class="req-row">
                            <input type="text" name="requisitos[0][chave]" placeholder="<?php echo esc_attr__('Chave (ex: Tom)', 'prompt-builder'); ?>">
                            <input type="text" name="requisitos[0][valor]" placeholder="<?php echo esc_attr__('Valor (ex: Formal)', 'prompt-builder'); ?>">
                            <button type="button" class="button button-secondary pb-remove-req"><?php echo esc_html__('Remover', 'prompt-builder'); ?></button>
                        </div>
                    </div>
                    <button type="button" id="pb-add-req" class="button button-secondary">
                        <?php echo esc_html__('+ Requisito', 'prompt-builder'); ?>
                    </button>

                    <div class="pb-form-actions">
                        <button type="submit" class="button button-primary">
                            <?php echo esc_html__('Gerar Prompt', 'prompt-builder'); ?>
                        </button>
                    </div>
                </form>

                <h3><?php echo esc_html__('Prompt Gerado', 'prompt-builder'); ?></h3>
                <textarea id="pb-prompt-output" readonly rows="8" cols="80" placeholder="<?php echo esc_attr__('Seu prompt gerado aparece aqui...', 'prompt-builder'); ?>"></textarea>

                <div id="pb-criar-post-container" class="pb-form-actions">
                    <button type="button" id="pb-criar-post" class="button button-secondary">
                        <?php echo esc_html__('Criar Rascunho de Post', 'prompt-builder'); ?>
                    </button>
                </div>

                <div class="pb-ai-actions">
                    <button type="button" id="pb-get-ai-response" class="button button-primary">
                        <?php echo esc_html__('Pedir pra IA', 'prompt-builder'); ?>
                    </button>
                </div>

                <h3><?php echo esc_html__('Resposta da IA', 'prompt-builder'); ?></h3>
                <textarea id="pb-ai-response-output" readonly rows="10" cols="80" placeholder="<?php echo esc_attr__('A resposta da IA aparece aqui...', 'prompt-builder'); ?>"></textarea>
            </div>
        </div>
    </div>
<?php
}

add_action('rest_api_init', function () {
    register_rest_route('prompt-builder/v1', '/gerar/', [
        'methods'             => 'POST',
        'callback'            => 'pb_gerar_prompt',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'args'                => [
            'base_prompt' => [
                'sanitize_callback' => 'sanitize_textarea_field',
                'required'          => false,
            ],
            'requisitos' => [
                'validate_callback' => function ($value, $request, $param) {
                    if (!is_array($value)) {
                        return false;
                    }
                    foreach ((array) $value as $req) {
                        if (!is_array($req) || !isset($req['chave']) || !isset($req['valor'])) {
                            return false;
                        }
                    }
                    return true;
                },
                'sanitize_callback' => function ($value, $request, $param) {
                    $sanitized_reqs = [];
                    if (is_array($value)) {
                        foreach ($value as $req) {
                            $sanitized_reqs[] = [
                                'chave' => sanitize_text_field($req['chave']),
                                'valor' => sanitize_text_field($req['valor']),
                            ];
                        }
                    }
                    return $sanitized_reqs;
                },
                'required'          => false,
            ],
        ],
    ]);

    register_rest_route('prompt-builder/v1', '/criar-post/', [
        'methods'             => 'POST',
        'callback'            => 'pb_criar_post',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'args'                => [
            'content' => [
                'sanitize_callback' => 'sanitize_textarea_field',
                'required'          => true,
            ],
        ],
    ]);

    register_rest_route('prompt-builder/v1', '/gerar-resposta-ia/', [
        'methods'             => 'POST',
        'callback'            => 'pb_gerar_resposta_ia',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'args'                => [
            'prompt' => [
                'sanitize_callback' => 'sanitize_textarea_field',
                'required'          => true,
            ],
        ],
    ]);
});

function pb_gerar_prompt(WP_REST_Request $request)
{
    $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response(['message' => 'Falha na verificação do nonce.'], 403);
    }

    //pega a base utilizado a função de sanitize
    $base = sanitize_textarea_field($request->get_param('base_prompt'));

    //pega os requisitos
    $requisitos = $request->get_param('requisitos');

    //verifica se há requisitos e base para iniciar o processo, pega a base e pula uma linha
    $prompt = $base;
    if (!empty($base) && !empty($requisitos)) {
        $prompt .= "\n";
    }

    //Aqui faz a conctenação dos requisitos, pula linha
    if (is_array($requisitos)) {
        foreach ($requisitos as $req) {
            if (isset($req['chave']) && isset($req['valor'])) {
                $prompt .= sprintf(esc_html__('Use %1$s como %2$s.', 'prompt-builder') . "\n", $req['chave'], $req['valor']);
            }
        }
    }

    //Retorna texto concatenado
    return rest_ensure_response(['prompt' => $prompt]);
}

function pb_criar_post(WP_REST_Request $request)
{
    $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response(['message' => 'Falha na verificação do nonce.'], 403);
    }

    $content = sanitize_textarea_field($request->get_param('content'));

    if (empty($content)) {
        return new WP_REST_Response(
            array('success' => false, 'message' => esc_html__('O conteúdo não pode estar vazio.', 'prompt-builder')),
            400
        );
    }

    $post_id = wp_insert_post([
        'post_title'   => esc_html__('Prompt Gerado em ', 'prompt-builder') . current_time('mysql'),
        'post_content' => $content,
        'post_status'  => 'draft',
        'post_type'    => 'post'
    ], true);

    if (is_wp_error($post_id)) {
        return new WP_REST_Response(
            array('success' => false, 'message' => $post_id->get_error_message()),
            500
        );
    }

    return rest_ensure_response(['success' => true, 'post_id' => $post_id]);
}

function pb_gerar_resposta_ia(WP_REST_Request $request)
{
    $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response(['message' => 'Falha na verificação do nonce.'], 403);
    }

    $prompt_para_ia = sanitize_textarea_field($request->get_param('prompt'));

    if (empty($prompt_para_ia)) {
        return new WP_REST_Response(
            array('success' => false, 'message' => esc_html__('O prompt pra IA tá vazio!', 'prompt-builder')),
            400
        );
    }

    $ia_api_key = get_option('pb_ia_api_key');

    if (empty($ia_api_key)) {
        return new WP_REST_Response(
            array('success' => false, 'message' => esc_html__('A API Key da IA não foi configurada. Coloca lá nas configurações do plugin, por favor.', 'prompt-builder')),
            400
        );
    }

    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $ia_api_key;

    $headers = array(
        'Content-Type' => 'application/json',
    );
    $body = json_encode(array(
        'contents' => array(
            array(
                'role' => 'user',
                'parts' => array(
                    array('text' => $prompt_para_ia),
                ),
            ),
        ),
    ));

    $response = wp_remote_post($api_url, array(
        'headers'     => $headers,
        'body'        => $body,
        'timeout'     => 120,
        'sslverify'   => false,
    ));

    if (is_wp_error($response)) {
        return new WP_REST_Response(
            array('success' => false, 'message' => esc_html__('Erro ao falar com a IA: ', 'prompt-builder') . $response->get_error_message()),
            500
        );
    }

    $body_response = wp_remote_retrieve_body($response);
    $data = json_decode($body_response, true);

    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $ai_response_text = $data['candidates'][0]['content']['parts'][0]['text'];
        return rest_ensure_response(['success' => true, 'ai_response' => $ai_response_text]);
    } else {
        error_log('Error: Resposta inesperada da IA: ' . print_r($data, true));
        return new WP_REST_Response(
            array('success' => false, 'message' => esc_html__('Não obteve resposta válida.', 'prompt-builder')),
            500
        );
    }
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ('tools_page_prompt-builder' !== $hook) {
        return;
    }

    wp_enqueue_script('wp-api');

    wp_enqueue_script(
        'pb-admin-script',
        plugin_dir_url(__FILE__) . 'js/prompt-builder-admin.js',
        array('jquery', 'wp-api'),
        filemtime(plugin_dir_path(__FILE__) . 'js/prompt-builder-admin.js'),
        true
    );

    wp_enqueue_style(
        'pb-admin-style',
        plugin_dir_url(__FILE__) . 'css/admin-styles.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'css/admin-styles.css')
    );

    wp_localize_script(
        'pb-admin-script',
        'pb_localized_strings',
        array(
            'nonce' => wp_create_nonce('wp_rest'),
            'rest_url_gerar' => esc_url_raw(rest_url('prompt-builder/v1/gerar')),
            'rest_url_criar_post' => esc_url_raw(rest_url('prompt-builder/v1/criar-post')),
            'rest_url_gerar_resposta_ia' => esc_url_raw(rest_url('prompt-builder/v1/gerar-resposta-ia')),
            'key_placeholder_text' => esc_attr__('Chave (Cor)', 'prompt-builder'),
            'value_placeholder_text' => esc_attr__('Valor (ex: Branco)', 'prompt-builder'),
            'remove_button_text' => esc_html__('X', 'prompt-builder'),
            'network_response_not_ok' => esc_html__('Deu ruim...', 'prompt-builder'),
            'prompt_generated_success' => esc_html__('Prompt criado', 'prompt-builder'),
            'error_generating_prompt' => esc_html__('Erro ao gerar prompt', 'prompt-builder'),
            'generate_prompt_first_post' => esc_html__('Necessario gerar um prompt antes', 'prompt-builder'),
            'draft_created_success' => esc_html__('Rascunho criado!', 'prompt-builder'),
            'error_creating_post' => esc_html__('Erro ao criar rascunho', 'prompt-builder'),
            'error_creating_post_full' => esc_html__('Erro ao criar rascunho, vê o console.', 'prompt-builder'),
            'generate_prompt_first_ia' => esc_html__('Gera um prompt primeiro, se não, não funciona', 'prompt-builder'),
            'generating_ia_response_info' => esc_html__('Aguarde...', 'prompt-builder'),
            'generating_response_text' => esc_html__('Aguarde...', 'prompt-builder'),
            'network_response_not_ok_ia' => esc_html__('ERRO na IA', 'prompt-builder'),
            'no_ia_response_received' => esc_html__('Nenhuma resposta da IA.', 'prompt-builder'),
            'ia_response_success' => esc_html__('Resultado da IA', 'prompt-builder'),
            'error_ia_response_console_details' => esc_html__('Erro na IA. Vê o console.', 'prompt-builder'),
            'error_getting_ia_response' => esc_html__('Erro ao pegar resposta da IA lascou... verifica a API KEY', 'prompt-builder'),
        )
    );
});
