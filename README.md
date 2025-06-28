iPrompt Builder
Um plugin WordPress intuitivo para gerar prompts estruturados para Inteligências Artificiais.

Descrição
O Propmpt Bulder e um plugin wordpress para criação de prompt para  Inteligências Artificiais
foi criado com base em Desafio Técnico – Desenvolvedor(a) Full Stack WordPress.
O plugin foi criado com ajuda de tecnicas de inteligencia artificial (Gemini) para desenvolver a interface e corrigir as ortografias incluindo esse arquivo.

Funcionalidades
1- Geração de Prompts Estruturados: Crie prompts detalhados combinando um briefing base com múltiplos requisitos definidos pelo utilizador.
2- Adição Dinâmica de Requisitos: Adicione e remova campos de requisitos (chave: valor) em tempo real na interface do utilizador.
3- Criação de Rascunhos de Posts: Converta instantaneamente o prompt gerado ou a resposta da IA num rascunho de post no WordPress.
4- Integração com IA (API): Conecte o plugin à sua chave de API de IA para enviar o prompt gerado e receber respostas diretamente no painel do WordPress.

Internacionalização: Pronto para traduções com suporte a text-domain.

Instalação
Instalação Manual
Descarregue o ficheiro .zip do plugin.

No seu painel de administração do WordPress, vá para Plugins > Adicionar Novo > Carregar Plugin.

Carregue o ficheiro .zip que descarregou.

Ative o plugin.

Configuração da API da IA
Após ativar o plugin, vá para Ferramentas > Prompt Builder no seu painel de administração do WordPress.

Na secção "Configurações da API da IA", insira a sua chave de API da IA chave deve ser solicitada ao desenvolvedor.

Clique em "Salvar".

Utilização
No painel de administração do WordPress, navegue até Ferramentas > Prompt Builder.

Seção "Gerador de Prompts":

No campo "Briefing", insira a sua ideia principal para o prompt.

Na secção "Requisitos", adicione pares de chave-valor que detalham o seu prompt (ex: "Tom: Formal", "Público: Desenvolvedores"). Use o botão "+ Requisito" para adicionar mais campos.

Clique em "Gerar Prompt" para construir o prompt final.

O prompt gerado aparecerá na área "Prompt Gerado".

Após gerar o prompt:

Pode clicar em "Criar Rascunho de Post" para salvar o conteúdo do prompt gerado como um novo rascunho de post no WordPress.

Ou, clique em "Pedir pra IA" para enviar o prompt gerado para a IA configurada e ver a resposta na área "Resposta da IA".

Desenvolvimento e Testes
Para desenvolvedores que desejam contribuir ou testar o plugin, o Prompt Builder inclui testes unitários baseados em PHPUnit.

Pré-requisitos para Testes
É necessário um ambiente de teste WordPress configurado. Recomenda-se o uso de wp-env (Docker-based) ou uma configuração manual via wp-cli e install-wp-tests.sh.

Usando wp-env
Certifique-se de que tem o Docker Desktop e o @wordpress/env (npm install -g @wordpress/env) instalados.

Na raiz do seu plugin, configure o ficheiro .wp-env.json (se não existir):

{
    "plugins": [
        "."
    ],
    "tests": {
        "php": "latest"
    }
}

Inicie o ambiente de teste:

wp-env start

Corra os testes PHPUnit a partir da raiz do seu plugin:

wp-env run cli phpunit tests/

(Ou wp-env run cli phpunit tests/test-pb-gerar-prompt.php para o teste específico.)

Testes PHPUnit
Os testes unitários para a função de geração de prompts estão localizados em tests/test-pb-gerar-prompt.php. Estes testes simulam diferentes cenários de entrada para a função pb_gerar_prompt para garantir que o prompt é formatado corretamente.

Licença
Todos os direitos reservados a Arthur Felizdoro.
Nenhuma modificação, redistribuição ou uso comercial é permitido sem autorização expressa do autor.