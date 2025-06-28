# 🧠 Prompt Builder

**Um plugin WordPress intuitivo para gerar prompts estruturados para Inteligências Artificiais.**

---

## 📄 Descrição

**Prompt Builder** é um plugin WordPress criado para facilitar a geração de prompts para ferramentas de Inteligência Artificial.  

Desenvolvido como parte do **Desafio Técnico – Desenvolvedor(a) Full Stack WordPress**, o plugin foi inteiramente elaborado com o auxílio de técnicas de IA (Gemini), incluindo a correção ortográfica e geração deste arquivo.

---

## ⚙️ Funcionalidades

✅ **Geração de Prompts Estruturados**  
Crie prompts detalhados a partir de um briefing base, combinando com múltiplos requisitos definidos pelo usuário.

✅ **Adição Dinâmica de Requisitos**  
Adicione ou remova campos de requisitos (em formato chave:valor) em tempo real.

✅ **Criação de Rascunhos de Post**  
Transforme o prompt gerado (ou a resposta da IA) em um rascunho de post WordPress com apenas um clique.

✅ **Integração com API de IA**  
Conecte o plugin à sua chave de API de IA (como Gemini) e envie prompts diretamente do WordPress.

🌍 **Internacionalização**  
Pronto para tradução com suporte ao `text-domain`.

---

## 🧩 Instalação

### 📦 Instalação Manual

1. Baixe o arquivo `.zip` do plugin. (https://drive.google.com/file/d/1k7vm4Gzck1agaK9ayMjvRefiBTnoHoKk/view?usp=sharing)  
2. No painel do WordPress, vá em **Plugins > Adicionar Novo > Carregar Plugin**.  
3. Envie o `.zip` e clique em **Instalar Agora**.  
4. Após instalado, clique em **Ativar Plugin**.

---

## 🔑 Configuração da API

1. No painel do WordPress, acesse **Ferramentas > Prompt Builder**.  
2. Na seção **"Configurações da API da IA"**, insira sua chave de API.  
   > A chave deve ser solicitada ao desenvolvedor.  
3. Clique em **Salvar**.

---

## ✨ Como Usar

1. Acesse **Ferramentas > Prompt Builder**.  
2. Em **Briefing**, insira a ideia central do prompt.  
3. Em **Requisitos**, adicione pares de chave:valor (ex: `Tom: Formal`, `Público: Desenvolvedores`).  
4. Clique em **Gerar Prompt** para visualizar o resultado.  

Após isso, você pode:

- ✅ Clicar em **Criar Rascunho de Post** para salvar como post.  
- 🤖 Clicar em **Pedir pra IA** para enviar à API e receber a resposta diretamente na interface.

---

## 🧪 Desenvolvimento e Testes

Este plugin inclui **testes unitários com PHPUnit** para garantir a estabilidade e funcionalidade da geração de prompts.

### 🔧 Pré-requisitos

- Ambiente de teste WordPress configurado.
- Recomenda-se o uso de [`wp-env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) ou configuração manual com `wp-cli` e `install-wp-tests.sh`.

### 🚀 Usando `wp-env`

1. Instale o Docker Desktop e o pacote global `@wordpress/env`:  
   ```bash
   npm install -g @wordpress/env
   ```

2. Crie um arquivo `.wp-env.json` na raiz do projeto (caso não exista):  
   ```json
   {
     "plugins": [
       "."
     ],
     "tests": {
       "php": "latest"
     }
   }
   ```

3. Inicie o ambiente de testes:  
   ```bash
   wp-env start
   ```

4. Execute os testes:  
   ```bash
   wp-env run cli phpunit tests/
   ```
   Ou um teste específico:
   ```bash
   wp-env run cli phpunit tests/test-pb-gerar-prompt.php
   ```

### 📁 Arquivos de Teste

- Os testes estão localizados em:  
  `tests/test-pb-gerar-prompt.php`

- Os cenários testam a função `pb_gerar_prompt` com diferentes combinações de briefing e requisitos.

---

## 📜 Licença

Todos os direitos reservados a **Arthur Felizdoro**.  
**Proibida** a modificação, redistribuição ou uso comercial **sem autorização expressa** do autor.
