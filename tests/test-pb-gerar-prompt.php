<?php
class PbGerarPromptTest extends WP_UnitTestCase
{

    /**
     * Testa a geração de um prompt sem requisitos.
     */
    public function testGerarPromptBasic()
    {
        $request = new WP_REST_Request('POST', '/prompt-builder/v1/gerar');
        $request->set_param('base_prompt', 'Venda de carro');

        $response = pb_gerar_prompt($request);
        $data = $response->get_data();

        $this->assertEquals(200, $response->get_status());
        $this->assertArrayHasKey('prompt', $data);
        $this->assertEquals('Venda de carro', $data['prompt']);
    }

    /**
     * Testa a geração de um prompt com requisitos.
     */
    public function testGerarPromptWithRequirements()
    {
        $request = new WP_REST_Request('POST', '/prompt-builder/v1/gerar');
        $request->set_param('base_prompt', 'Venda de imovel');
        $request->set_param('requisitos', [
            ['chave' => 'Tom', 'valor' => 'corretor de imoveis'],
            ['chave' => 'Objetivo', 'valor' => 'Vender um apartamento de 60m2 com 2 quartos']
        ]);

        $response = pb_gerar_prompt($request);
        $data = $response->get_data();

        $expected_prompt = "Venda de imovel.\nUse Tom como corretor de imoveis.\nUse Vender um apartamento de 60m2 com 2 quartos.\n";

        $this->assertEquals(200, $response->get_status());
        $this->assertArrayHasKey('prompt', $data);
        $this->assertEquals($expected_prompt, $data['prompt']);
    }

    /**
     * Testa a geração de um prompt com requisitos, mas sem a base.
     */
    public function testGerarPromptRequirementsOnly()
    {
        $request = new WP_REST_Request('POST', '/prompt-builder/v1/gerar');
        $request->set_param('base_prompt', ''); // Prompt base vazio
        $request->set_param('requisitos', [
            ['chave' => 'Carro', 'valor' => 'Volvo XC60']
        ]);

        $response = pb_gerar_prompt($request);
        $data = $response->get_data();

        $expected_prompt = "Use Carro como Volvo XC60.\n";

        $this->assertEquals(200, $response->get_status());
        $this->assertArrayHasKey('prompt', $data);
        $this->assertEquals($expected_prompt, $data['prompt']);
    }
}
