/**
 * prompt-builder-admin.js
 *
 * Este ficheiro contém toda a lógica JavaScript
 */

//Verifica se o DOM esta carregado antes de iniciar
document.addEventListener('DOMContentLoaded', () => {
    const pbWpNonce = pb_localized_strings.nonce;

    //Utilizado para exibir msg
    function pbShowMessage(message, type = "info") {
        const msgDiv = document.getElementById("pb-messages");

        if (msgDiv) {
            msgDiv.textContent = message;
            msgDiv.className = "notice notice-" + type + " is-dismissible";
            msgDiv.style.display = "block";
            console.log(message);
            setTimeout(() => {
                msgDiv.style.display = "none";
            }, 5000);
        }
    }

    function addRemoveButtonListeners() {
        document.querySelectorAll('.pb-remove-req').forEach(button => {
            button.onclick = (event) => {
                event.target.closest('.req-row').remove();
                reindexRequirements();
            };
        });
    }
    function reindexRequirements() {
        const requirementRows = document.querySelectorAll("#pb-requisitos .req-row");
        requirementRows.forEach((row, index) => {
            const keyInput = row.querySelector('input[name^="requisitos"][name$="[chave]"]');
            const valueInput = row.querySelector('input[name^="requisitos"][name$="[valor]"]');
            if (keyInput) {
                keyInput.name = `requisitos[${index}][chave]`;
            }
            if (valueInput) {
                valueInput.name = `requisitos[${index}][valor]`;
            }
        });
    }


    const pbAddReqButton = document.getElementById("pb-add-req");
    if (pbAddReqButton) {
        pbAddReqButton.addEventListener("click", () => {
            const requirementsContainer = document.getElementById("pb-requisitos");
            if (requirementsContainer) {
                const index = requirementsContainer.querySelectorAll(".req-row").length;
                const div = document.createElement("div");
                div.classList.add("req-row");

                div.innerHTML = `<input type="text" name="requisitos[${index}][chave]" placeholder="${pb_localized_strings.key_placeholder_text}">
                                 <input type="text" name="requisitos[${index}][valor]" placeholder="${pb_localized_strings.value_placeholder_text}">
                                 <button type="button" class="button button-secondary pb-remove-req">${pb_localized_strings.remove_button_text}</button>`;
                requirementsContainer.appendChild(div);
                addRemoveButtonListeners();
            }
        });
    }

    const pbForm = document.getElementById("pb-form");
    if (pbForm) {
        pbForm.addEventListener("submit", async (e) => {
            console.log("Evento gerar Prompt acionado.");
            e.preventDefault();
            const form = new FormData(e.target);
            const promptOutput = document.getElementById("pb-prompt-output");
            const iaResponseOutput = document.getElementById("pb-ai-response-output");
            if (iaResponseOutput) iaResponseOutput.value = "";

            try {
                const res = await fetch(pb_localized_strings.rest_url_gerar, {
                    method: "POST",
                    headers: {
                        "X-WP-Nonce": pbWpNonce
                    },
                    body: form
                });

                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || pb_localized_strings.network_response_not_ok);
                }

                const data = await res.json();
                if (promptOutput) {
                    promptOutput.value = data.prompt || "";
                }
                pbShowMessage(pb_localized_strings.prompt_generated_success, "success");
            } catch (error) {
                console.error("Erro ao gerar prompt:", error);
                pbShowMessage(`${pb_localized_strings.error_generating_prompt}: ${error.message}`, "error");
            }
        });
    }

    const pbCriarPostButton = document.getElementById("pb-criar-post");
    if (pbCriarPostButton) {
        pbCriarPostButton.addEventListener("click", async () => {
            const content = document.getElementById("pb-prompt-output").value;
            if (!content.trim()) {
                pbShowMessage(pb_localized_strings.generate_prompt_first_post, "warning");
                return;
            }

            try {
                const res = await fetch(pb_localized_strings.rest_url_criar_post, {
                    method: "POST",
                    headers: {
                        "X-WP-Nonce": pbWpNonce,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ content })
                });

                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || pb_localized_strings.network_response_not_ok);
                }

                const data = await res.json();
                if (data.success) {
                    pbShowMessage(`${pb_localized_strings.draft_created_success} ID: ${data.post_id}`, "success");
                } else {
                    pbShowMessage(`${pb_localized_strings.error_creating_post}: ${data.message || ""}`, "error");
                }
            } catch (error) {
                console.error("Erro ao gerar post", error);
                pbShowMessage(`${pb_localized_strings.error_creating_post_full}: ${error.message}`, "error");
            }
        });
    }

    const pbGetAiResponseButton = document.getElementById("pb-get-ai-response");
    if (pbGetAiResponseButton) {
        pbGetAiResponseButton.addEventListener("click", async () => {
            const promptContent = document.getElementById("pb-prompt-output").value;
            const aiResponseOutput = document.getElementById("pb-ai-response-output");

            if (!promptContent.trim()) {
                pbShowMessage(pb_localized_strings.generate_prompt_first_ia, "warning");
                return;
            }

            pbShowMessage(pb_localized_strings.generating_ia_response_info, "info");
            if (aiResponseOutput) aiResponseOutput.value = pb_localized_strings.generating_response_text;

            try {
                const res = await fetch(pb_localized_strings.rest_url_gerar_resposta_ia, {
                    method: "POST",
                    headers: {
                        "X-WP-Nonce": pbWpNonce,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ prompt: promptContent })
                });

                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || pb_localized_strings.network_response_not_ok_ia);
                }

                const data = await res.json();
                if (aiResponseOutput) {
                    aiResponseOutput.value = data.ai_response || pb_localized_strings.no_ia_response_received;
                }
                pbShowMessage(pb_localized_strings.ia_response_success, "success");
            } catch (error) {
                console.error("Erro ao obter resposta da IA:", error);
                if (aiResponseOutput) aiResponseOutput.value = pb_localized_strings.error_ia_response_console_details;
                pbShowMessage(`${pb_localized_strings.error_getting_ia_response}: ${error.message}`, "error");
            }
        });
    }
    addRemoveButtonListeners();
});
