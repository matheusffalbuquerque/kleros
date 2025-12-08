/**
 * Utilitário para submissão de formulários via AJAX
 * Mantém o modal aberto e permite navegação entre modais
 */

/**
 * Envia formulário via AJAX
 * @param {HTMLFormElement|string} form - Elemento do formulário ou seletor
 * @param {Object} options - Opções de configuração
 * @returns {Promise}
 */
export function submitFormAjax(form, options = {}) {
    const formElement = typeof form === 'string' ? document.querySelector(form) : form;
    
    if (!formElement) {
        console.error('Formulário não encontrado');
        return Promise.reject(new Error('Formulário não encontrado'));
    }

    const {
        onSuccess = null,
        onError = null,
        onComplete = null,
        showLoading = true,
        validateBefore = null,
        dataTransform = null,
        returnToModal = false, // Se true, volta ao modal anterior após sucesso
        updateSelect = null // { selectId: 'evento_id', labelKey: 'titulo', valueKey: 'id' }
    } = options;

    return new Promise((resolve, reject) => {
        // Validação customizada antes de enviar
        if (validateBefore && typeof validateBefore === 'function') {
            const validationResult = validateBefore(formElement);
            if (!validationResult.valid) {
                mostrarMensagem(validationResult.message || 'Por favor, corrija os erros do formulário.', 'error');
                reject(new Error('Validação falhou'));
                return;
            }
        }

        // Captura dados do formulário
        const formData = new FormData(formElement);
        
        // Transformação de dados se necessário
        if (dataTransform && typeof dataTransform === 'function') {
            const transformed = dataTransform(formData);
            if (transformed instanceof FormData) {
                formData = transformed;
            }
        }

        // URL e método
        const url = formElement.action;
        const method = formElement.method.toUpperCase() || 'POST';

        // Mostra loading
        if (showLoading) {
            mostrarLoading(formElement);
        }

        // Envia requisição
        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('[form-ajax-utils] Resposta recebida:', data);
            
            // Remove loading
            if (showLoading) {
                removerLoading(formElement);
            }

            // Callback de sucesso customizado
            let preventDefaultAction = false;
            if (onSuccess && typeof onSuccess === 'function') {
                console.log('[form-ajax-utils] Executando callback onSuccess...');
                const result = onSuccess(data, formElement);
                console.log('[form-ajax-utils] Resultado do callback:', result);
                // Se o callback retornar explicitamente false, não executa ações padrão
                if (result === false) {
                    preventDefaultAction = true;
                    console.log('[form-ajax-utils] ✅ Ação padrão foi impedida pelo callback');
                }
            }

            // Atualiza select no modal anterior se especificado
            if (updateSelect && data.data) {
                atualizarSelectModalAnterior(updateSelect, data.data);
            }

            // Mostra mensagem de sucesso
            if (data.message) {
                mostrarMensagem(data.message, 'success');
            }

            // Volta ao modal anterior se configurado (somente se não foi impedido pelo callback)
            if (!preventDefaultAction) {
                console.log('[form-ajax-utils] preventDefaultAction =', preventDefaultAction);
                if (returnToModal && typeof voltarModalAnterior === 'function') {
                    console.log('[form-ajax-utils] Voltando ao modal anterior em 800ms...');
                    setTimeout(() => {
                        voltarModalAnterior();
                    }, 800);
                } else if (typeof fecharJanelaModal === 'function') {
                    console.log('[form-ajax-utils] ⚠️ Fechando TODOS os modais em 800ms...');
                    setTimeout(() => {
                        fecharJanelaModal();
                    }, 800);
                }
            } else {
                console.log('[form-ajax-utils] ✅ Ação padrão impedida, não vai fechar/voltar modal');
            }

            resolve(data);
        })
        .catch(error => {
            // Remove loading
            if (showLoading) {
                removerLoading(formElement);
            }

            // Callback de erro customizado
            if (onError && typeof onError === 'function') {
                onError(error, formElement);
            }

            // Mostra erros de validação
            if (error.data && error.data.errors) {
                mostrarErrosValidacao(error.data.errors, formElement);
            } else if (error.data && error.data.message) {
                mostrarMensagem(error.data.message, 'error');
            } else {
                mostrarMensagem('Erro ao processar requisição. Tente novamente.', 'error');
            }

            reject(error);
        })
        .finally(() => {
            if (onComplete && typeof onComplete === 'function') {
                onComplete(formElement);
            }
        });
    });
}

/**
 * Mostra loading no formulário
 */
function mostrarLoading(formElement) {
    const submitButton = formElement.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.dataset.originalHtml = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processando...';
    }

    // Adiciona overlay de loading
    const overlay = document.createElement('div');
    overlay.className = 'form-loading-overlay';
    overlay.innerHTML = '<div class="spinner"></div>';
    formElement.style.position = 'relative';
    formElement.appendChild(overlay);
}

/**
 * Remove loading do formulário
 */
function removerLoading(formElement) {
    const submitButton = formElement.querySelector('button[type="submit"]');
    if (submitButton && submitButton.dataset.originalHtml) {
        submitButton.disabled = false;
        submitButton.innerHTML = submitButton.dataset.originalHtml;
        delete submitButton.dataset.originalHtml;
    }

    const overlay = formElement.querySelector('.form-loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Mostra mensagem ao usuário
 */
function mostrarMensagem(mensagem, tipo = 'info') {
    // Usa o sistema de mensagens existente do Kleros
    const msgContainer = document.createElement('div');
    msgContainer.className = 'msg';
    
    const msgContent = document.createElement('div');
    msgContent.className = tipo === 'success' ? 'success' : 'error';
    
    const icon = tipo === 'success' ? 'bi-check-circle' : 'bi-exclamation-diamond';
    msgContent.innerHTML = `<i class="bi ${icon}"></i> ${mensagem}`;
    
    msgContainer.appendChild(msgContent);
    document.body.appendChild(msgContainer);
    
    // Remove após 5 segundos
    setTimeout(() => {
        msgContainer.remove();
    }, 5000);
}

/**
 * Mostra erros de validação nos campos
 */
function mostrarErrosValidacao(errors, formElement) {
    // Remove erros anteriores
    formElement.querySelectorAll('.error-message').forEach(el => el.remove());
    formElement.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

    // Adiciona novos erros
    Object.keys(errors).forEach(fieldName => {
        const field = formElement.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('input-error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = errors[fieldName][0];
            
            field.parentNode.appendChild(errorDiv);
        }
    });

    // Mostra mensagem geral
    const firstError = Object.values(errors)[0][0];
    mostrarMensagem(firstError, 'error');
}

/**
 * Atualiza select no modal anterior com o novo item criado
 */
function atualizarSelectModalAnterior(config, data) {
    const { selectId, labelKey = 'titulo', valueKey = 'id' } = config;
    
    // Agenda atualização para depois de voltar ao modal
    setTimeout(() => {
        const select = document.getElementById(selectId);
        if (select && data[valueKey] && data[labelKey]) {
            const option = new Option(data[labelKey], data[valueKey], true, true);
            select.add(option);
            select.value = data[valueKey];
            
            // Dispara evento change
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }, 100);
}

/**
 * Inicializa formulários AJAX na página/modal
 */
export function initAjaxForms(container = document) {
    container.querySelectorAll('[data-ajax-form]').forEach(form => {
        // Remove listener anterior se existir
        if (form.dataset.ajaxInitialized === 'true') {
            return;
        }
        form.dataset.ajaxInitialized = 'true';

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Pega configurações do data-attribute
            const config = JSON.parse(form.dataset.ajaxForm || '{}');
            
            submitFormAjax(form, config);
        });
    });
}

// Exporta para uso global
window.submitFormAjax = submitFormAjax;
window.initAjaxForms = initAjaxForms;
