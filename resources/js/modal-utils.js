// Função para exibir mensagens do sistema
function showSystemMessage(message, type = 'success') {
    const msgContainer = document.querySelector('.msg') || createMessageContainer();
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-diamond';
    const closeBtn = type === 'success' ? '<div class="close"><i class="bi bi-x"></i></div>' : '';
    
    msgContainer.innerHTML = `
        <div class="${type}">
            ${closeBtn}
            <i class="bi ${icon}"></i> ${message}
        </div>
    `;
    
    // Auto-remove após 5 segundos
    setTimeout(() => {
        msgContainer.innerHTML = '';
    }, 5000);
    
    // Adiciona evento de fechar no X
    if (type === 'success') {
        const closeButton = msgContainer.querySelector('.close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                msgContainer.innerHTML = '';
            });
        }
    }
}

function createMessageContainer() {
    const container = document.createElement('div');
    container.className = 'msg';
    const main = document.querySelector('main.content');
    if (main) {
        main.insertBefore(container, main.firstChild);
    }
    return container;
}

// Inicializa cronograma de ocorrências para formulário de CRIAR evento
function initCronogramaOcorrenciasCriar(tbody) {
    tbody.dataset.initialized = 'true';
    let ocorrenciaCount = 0;
    
    function adicionarOcorrencia() {
        const dataAtual = new Date().toISOString().split('T')[0];
        
        const row = document.createElement('tr');
        row.setAttribute('data-ocorrencia-row', '');
        row.innerHTML = `
            <td data-label="Dia">
                <input type="date" name="ocorrencias[${ocorrenciaCount}][data_ocorrencia]" value="${dataAtual}" required>
            </td>
            <td data-label="Horário">
                <input type="time" name="ocorrencias[${ocorrenciaCount}][horario_inicio]">
            </td>
            <td data-label="Descrição">
                <input type="text" name="ocorrencias[${ocorrenciaCount}][descricao]" placeholder="Descrição (opcional)">
            </td>
            <td data-label="Local">
                <input type="text" name="ocorrencias[${ocorrenciaCount}][local]" placeholder="Local (opcional)">
            </td>
            <td data-label="Ações" class="cronograma-acoes">
                <button type="button" class="btn-icon btn-add-ocorrencia" title="Duplicar ocorrência">
                    <i class="bi bi-plus-circle"></i>
                </button>
                <button type="button" class="btn-icon btn-remove-ocorrencia" title="Remover ocorrência">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        ocorrenciaCount++;
    }
    
    function reindexarOcorrencias() {
        const rows = tbody.querySelectorAll('tr[data-ocorrencia-row]');
        rows.forEach((row, index) => {
            row.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.includes('ocorrencias[')) {
                    const newName = name.replace(/ocorrencias\[\d+\]/, `ocorrencias[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        ocorrenciaCount = rows.length;
    }
    
    // Delegação de eventos para botões
    tbody.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.btn-add-ocorrencia');
        if (addBtn) {
            e.preventDefault();
            adicionarOcorrencia();
            return;
        }
        
        const removeBtn = e.target.closest('.btn-remove-ocorrencia');
        if (removeBtn) {
            e.preventDefault();
            const row = removeBtn.closest('tr[data-ocorrencia-row]');
            if (tbody.querySelectorAll('tr[data-ocorrencia-row]').length > 1) {
                row.remove();
                reindexarOcorrencias();
            } else {
                alert('É necessário manter pelo menos uma ocorrência.');
            }
        }
    });
    
    // Adiciona primeira ocorrência automaticamente se a tabela estiver vazia
    if (tbody.children.length === 0) {
        adicionarOcorrencia();
    }
}

// Inicializa cronograma de ocorrências para formulário de EDITAR evento
function initCronogramaOcorrenciasEditar(tbody) {
    tbody.dataset.initialized = 'true';
    const temOcorrencias = tbody.querySelectorAll('tr[data-ocorrencia-row]').length > 0;
    let ocorrenciaCount = temOcorrencias ? tbody.querySelectorAll('tr[data-ocorrencia-row]').length : 0;
    
    function adicionarOcorrenciaEdit() {
        const dataAtual = new Date().toISOString().split('T')[0];
        
        const row = document.createElement('tr');
        row.setAttribute('data-ocorrencia-row', '');
        row.innerHTML = `
            <td data-label="Dia">
                <input type="date" name="ocorrencias[${ocorrenciaCount}][data_ocorrencia]" value="${dataAtual}" required>
            </td>
            <td data-label="Horário">
                <input type="time" name="ocorrencias[${ocorrenciaCount}][horario_inicio]">
            </td>
            <td data-label="Descrição">
                <input type="text" name="ocorrencias[${ocorrenciaCount}][descricao]" placeholder="Descrição (opcional)">
            </td>
            <td data-label="Local">
                <input type="text" name="ocorrencias[${ocorrenciaCount}][local]" placeholder="Local (opcional)">
            </td>
            <td data-label="Ações" class="cronograma-acoes">
                <button type="button" class="btn-icon btn-add-ocorrencia" title="Duplicar ocorrência">
                    <i class="bi bi-plus-circle"></i>
                </button>
                <button type="button" class="btn-icon btn-remove-ocorrencia" title="Remover ocorrência">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        ocorrenciaCount++;
    }
    
    function reindexarOcorrenciasEdit() {
        const rows = tbody.querySelectorAll('tr[data-ocorrencia-row]');
        rows.forEach((row, index) => {
            row.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.includes('ocorrencias[')) {
                    const newName = name.replace(/ocorrencias\[\d+\]/, `ocorrencias[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        ocorrenciaCount = rows.length;
    }
    
    // Delegação de eventos para botões
    tbody.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.btn-add-ocorrencia');
        if (addBtn) {
            e.preventDefault();
            adicionarOcorrenciaEdit();
            return;
        }
        
        const removeBtn = e.target.closest('.btn-remove-ocorrencia');
        if (removeBtn) {
            e.preventDefault();
            const row = removeBtn.closest('tr[data-ocorrencia-row]');
            if (tbody.querySelectorAll('tr[data-ocorrencia-row]').length > 1) {
                row.remove();
                reindexarOcorrenciasEdit();
            } else {
                alert('É necessário manter pelo menos uma ocorrência.');
            }
        }
    });
    
    // Adiciona primeira ocorrência se não houver nenhuma
    if (!temOcorrencias) {
        adicionarOcorrenciaEdit();
    }
}

export function initModalScripts(container) {
    // Inicializa formulário de criar evento se existir
    const formCriarEvento = container.querySelector('#form-criar-evento');
    if (formCriarEvento && !formCriarEvento.dataset.ajaxInitialized) {
        initFormCriarEvento(formCriarEvento);
    }

    // Controles de preletor (alternar entre membro select2 e campo externo)
    const preletorContainers = container.querySelectorAll('[data-preletor-container]');
    preletorContainers.forEach((scope) => {
        if (scope.dataset.preletorToggleInitialized === 'true') {
            return;
        }
        scope.dataset.preletorToggleInitialized = 'true';
        initPreletorToggle(scope);
    });

    // Controle de geração de cultos quando o evento for marcado como recorrente
    const geracaoCultosField = container.querySelector('.geracao_cultos');
    const eventoRecorrenteRadios = container.querySelectorAll('input[name="evento_recorrente"]');
    if (geracaoCultosField && eventoRecorrenteRadios.length && geracaoCultosField.dataset.recorrenteToggleInitialized !== 'true') {
        geracaoCultosField.dataset.recorrenteToggleInitialized = 'true';
        const manualGeracaoInput = container.querySelector('input[name="geracao_cultos"][value="0"]');
        const cronogramaTab = container.querySelector('.tab-menu li[data-tab="evento-cronograma"]');
        const cronogramaPane = container.querySelector('#evento-cronograma');
        const descricaoTab = container.querySelector('.tab-menu li[data-tab="evento-descricao"]');
        const descricaoPane = container.querySelector('#evento-descricao');
        const detalhesTab = container.querySelector('.tab-menu li[data-tab="evento-detalhes"]');
        const detalhesPane = container.querySelector('#evento-detalhes');

        const toggleGeracaoCultos = () => {
            const selected = container.querySelector('input[name="evento_recorrente"]:checked');
            const isRecorrente = selected && selected.value === '1';

            if (geracaoCultosField instanceof HTMLElement) {
                geracaoCultosField.style.display = isRecorrente ? 'none' : '';
                geracaoCultosField.hidden = isRecorrente;
            }

            if (isRecorrente && manualGeracaoInput) {
                manualGeracaoInput.checked = true;
                manualGeracaoInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Oculta a aba/painel de cronograma quando recorrente
            if (cronogramaTab instanceof HTMLElement) {
                cronogramaTab.style.display = isRecorrente ? 'none' : '';
                cronogramaTab.hidden = isRecorrente;
                cronogramaTab.classList.toggle('active', !isRecorrente && cronogramaTab.classList.contains('active'));
            }
            if (cronogramaPane instanceof HTMLElement) {
                cronogramaPane.style.display = isRecorrente ? 'none' : '';
                cronogramaPane.hidden = isRecorrente;
                cronogramaPane.classList.toggle('active', !isRecorrente && cronogramaPane.classList.contains('active'));
            }

            // Se o cronograma estava ativo, troca para outra aba visível
            if (isRecorrente && cronogramaTab && cronogramaTab.classList.contains('active')) {
                const fallbackTab = detalhesTab || descricaoTab;
                const fallbackPane = detalhesPane || descricaoPane;

                cronogramaTab.classList.remove('active');
                cronogramaPane?.classList.remove('active');
                if (fallbackTab) {
                    fallbackTab.classList.add('active');
                }
                if (fallbackPane) {
                    fallbackPane.hidden = false;
                    fallbackPane.style.display = '';
                    fallbackPane.classList.add('active');
                }
            }
        };

        eventoRecorrenteRadios.forEach((radio) => {
            radio.addEventListener('change', toggleGeracaoCultos);
        });

        toggleGeracaoCultos();
    }
    
    // Inicializa cronograma de ocorrências para criar evento
    const cronogramaBodyCriar = container.querySelector('#cronograma-body-criar');
    if (cronogramaBodyCriar && !cronogramaBodyCriar.dataset.initialized) {
        initCronogramaOcorrenciasCriar(cronogramaBodyCriar);
    }
    
    // Inicializa cronograma de ocorrências para editar evento
    const cronogramaBodyEditar = container.querySelector('#cronograma-body-editar');
    if (cronogramaBodyEditar && !cronogramaBodyEditar.dataset.initialized) {
        initCronogramaOcorrenciasEditar(cronogramaBodyEditar);
    }
    
    // Inicializa formulário de editar culto se existir
    const selectEvento = container.querySelector('#evento_id');
    if (selectEvento && !selectEvento.dataset.eventListenersAdded) {
        initFormEditarCulto(selectEvento);
    }
    
    // Inicializa formulários AJAX
    if (typeof window.initAjaxForms === 'function') {
        window.initAjaxForms(container);
    }
    
    // --- abas legadas (.tabs / .tab-menu) ---
    const legacyTabContainers = container.querySelectorAll('.tabs');
    legacyTabContainers.forEach((tabsContainer) => {
        const menuItems = tabsContainer.querySelectorAll('.tab-menu li[data-tab]');
        const panes = tabsContainer.querySelectorAll('.tab-pane');

        if (!menuItems.length || !panes.length) {
            return;
        }

        const activate = (tabId) => {
            menuItems.forEach((item) => {
                const isActive = item.dataset.tab === tabId;
                item.classList.toggle('active', isActive);
            });

            panes.forEach((pane) => {
                const isActive = pane.id === tabId;
                pane.classList.toggle('active', isActive);
                pane.hidden = !isActive;
                pane.style.display = isActive ? '' : 'none';
            });
        };

        const initialTab =
            tabsContainer.querySelector('.tab-menu li.active[data-tab]') || menuItems[0];

        if (initialTab) {
            activate(initialTab.dataset.tab);
        }
    });
                
    // --- cronograma de eventos (gera linhas a partir do intervalo de datas) ---
    container.querySelectorAll('[data-cronograma-body]').forEach((tbody) => {
        if (tbody.dataset.cronogramaInitialized === 'true') {
            return;
        }
        tbody.dataset.cronogramaInitialized = 'true';

        const startSelector = tbody.dataset.cronogramaStart || '#data_inicio';
        const endSelector = tbody.dataset.cronogramaEnd || '#data_encerramento';
        const prefix = tbody.dataset.cronogramaPrefix || 'ocorrencias';

        const startInput = container.querySelector(startSelector) || document.querySelector(startSelector);
        const endInput = container.querySelector(endSelector) || document.querySelector(endSelector);

        const parseDate = (value) => {
            const date = value ? new Date(value + 'T00:00:00') : null;
            return date instanceof Date && !isNaN(date) ? date : null;
        };

        const buildDateRange = (startDate, endDate) => {
            if (!startDate) return [];
            const end = endDate && endDate >= startDate ? endDate : startDate;
            const days = [];
            for (let dt = new Date(startDate); dt <= end; dt.setDate(dt.getDate() + 1)) {
                days.push(new Date(dt));
            }
            return days;
        };

        const renderCronograma = () => {
            const startDate = parseDate(startInput?.value);
            const endDate = parseDate(endInput?.value);
            const range = buildDateRange(startDate, endDate);
            const rows = range.length ? range : [null];

            tbody.innerHTML = '';

            rows.forEach((dateValue, index) => {
                const dateStr = dateValue ? dateValue.toISOString().slice(0, 10) : '';
                const dateFormatted = dateValue ? dateValue.toLocaleDateString('pt-BR', { 
                    weekday: 'long', 
                    day: '2-digit', 
                    month: 'long', 
                    year: 'numeric' 
                }) : '';
                tbody.insertAdjacentHTML('beforeend', `
                    <tr data-ocorrencia-row>
                        <td data-label="Dia">
                            <span class="cronograma-data">${dateFormatted}</span>
                            <input type="hidden" name="${prefix}[${index}][data_ocorrencia]" value="${dateStr}" data-data-ocorrencia>
                        </td>
                        <td data-label="Horário"><input type="time" name="${prefix}[${index}][horario_inicio]"></td>
                        <td data-label="Descrição"><input type="text" name="${prefix}[${index}][descricao]" placeholder="Descrição (opcional)"></td>
                        <td data-label="Local"><input type="text" name="${prefix}[${index}][local]" placeholder="Local (opcional)"></td>
                        <td data-label="Ações" class="cronograma-acoes">
                            <button type="button" class="btn-icon btn-add-ocorrencia" title="Duplicar ocorrência">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                            <button type="button" class="btn-icon btn-remove-ocorrencia" title="Remover ocorrência">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        };

        startInput?.addEventListener('change', renderCronograma);
        endInput?.addEventListener('change', renderCronograma);
        renderCronograma();

        // Função para reindexar os nomes dos inputs
        const reindexarOcorrencias = () => {
            const rows = tbody.querySelectorAll('tr[data-ocorrencia-row]');
            rows.forEach((row, index) => {
                row.querySelectorAll('input, select, textarea').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name && name.includes(prefix + '[')) {
                        const newName = name.replace(new RegExp(prefix + '\\[\\d+\\]'), `${prefix}[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        };

        // Event listener para adicionar ocorrência
        tbody.addEventListener('click', (e) => {
            const addBtn = e.target.closest('.btn-add-ocorrencia');
            if (addBtn) {
                e.preventDefault();
                const row = addBtn.closest('tr[data-ocorrencia-row]');
                const newRow = row.cloneNode(true);
                
                // Limpar valores dos inputs (exceto data)
                newRow.querySelectorAll('input[type="time"], input[type="text"]').forEach(input => {
                    input.value = '';
                });
                
                // Inserir nova linha após a atual
                row.parentNode.insertBefore(newRow, row.nextSibling);
                
                // Reindexar todos os inputs
                reindexarOcorrencias();
            }
        });

        // Event listener para remover ocorrência
        tbody.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.btn-remove-ocorrencia');
            if (removeBtn) {
                e.preventDefault();
                const row = removeBtn.closest('tr[data-ocorrencia-row]');
                const totalRows = tbody.querySelectorAll('tr[data-ocorrencia-row]').length;
                
                if (totalRows > 1) {
                    if (confirm('Deseja realmente remover esta ocorrência?')) {
                        row.remove();
                        reindexarOcorrencias();
                    }
                } else {
                    alert('Não é possível remover a última ocorrência. O evento deve ter ao menos uma ocorrência.');
                }
            }
        });
    });
                
    // --- controle de abas internas ---
    const tabGroups = container.querySelectorAll('[data-tabs]');
    tabGroups.forEach(group => {
        if (group.dataset.tabsInitialized) {
            return;
        }
        group.dataset.tabsInitialized = 'true';

        const buttons = Array.from(group.querySelectorAll('[data-tab-target]'));
        const panels = Array.from(group.querySelectorAll('.tab-panel'));
        const defaultTab = group.dataset.activeTab || (buttons[0] && buttons[0].dataset.tabTarget);

        const activate = (tabId) => {
            buttons.forEach(btn => {
                const isActive = btn.dataset.tabTarget === tabId;
                btn.classList.toggle('active', isActive);
            });

            panels.forEach(panel => {
                const isActive = panel.id === tabId;
                panel.classList.toggle('active', isActive);
            });
        };

        buttons.forEach(btn => {
            btn.addEventListener('click', () => activate(btn.dataset.tabTarget));
        });

        if (defaultTab) {
            activate(defaultTab);
        }
    });

    // --- controles de upload com preview de texto ---
    const fileInputs = container.querySelectorAll('input[type="file"][data-file-display]');
    fileInputs.forEach((input) => {
        if (input.dataset.fileDisplayInitialized === 'true') {
            return;
        }
        input.dataset.fileDisplayInitialized = 'true';

        const selector = input.dataset.fileDisplay;
        if (!selector) {
            return;
        }

        const findTarget = () => {
            const form = input.closest('form');
            return (form && form.querySelector(selector))
                || container.querySelector(selector)
                || document.querySelector(selector);
        };

        const updateDisplay = () => {
            const target = findTarget();
            if (!target) {
                return;
            }

            if (!target.dataset.fileInitial) {
                target.dataset.fileInitial = target.innerHTML;
            }

            const file = input.files && input.files[0] ? input.files[0] : null;
            if (file) {
                target.textContent = file.name;
            } else {
                target.innerHTML = target.dataset.fileInitial || '';
            }
        };

        input.addEventListener('change', updateDisplay);
    });

    // --- controle de exibição das opções das perguntas ---
    const optionToggles = container.querySelectorAll('[data-toggle-options]');
    optionToggles.forEach(select => {
        if (select.dataset.optionsInitialized) {
            return;
        }
        select.dataset.optionsInitialized = 'true';
        const targetSelector = select.dataset.toggleOptions;
        if (!targetSelector) {
            return;
        }

        const target = container.querySelector(targetSelector);
        if (!target) {
            return;
        }

        const toggleOptions = () => {
            const needsOptions = ['radio', 'checkbox'].includes(select.value);
            target.style.display = needsOptions ? '' : 'none';
        };

        select.addEventListener('change', toggleOptions);
        toggleOptions();
    });

    // --- acordeões das perguntas ---
    const accordions = container.querySelectorAll('[data-accordion]');
    accordions.forEach(accordion => {
        if (accordion.dataset.accordionInitialized) {
            return;
        }
        accordion.dataset.accordionInitialized = 'true';

        const toggle = accordion.querySelector('[data-accordion-toggle]');
        const body = accordion.querySelector('[data-accordion-body]');

        if (!toggle || !body) {
            return;
        }

        const setState = (open) => {
            accordion.classList.toggle('open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        const initialOpen = accordion.dataset.open === 'true';
        setState(initialOpen);

        toggle.addEventListener('click', () => {
            const willOpen = !accordion.classList.contains('open');
            setState(willOpen);
        });
    });

    // --- formulário de nova pergunta em pesquisas ---
    const perguntaForms = container.querySelectorAll('[data-pergunta-form="nova"]');
    perguntaForms.forEach(form => {
        if (form.dataset.ajaxInitialized === 'true') {
            return;
        }
        form.dataset.ajaxInitialized = 'true';

        const context = form.closest('.tab-pane') || container;
        const perguntasList = context.querySelector('[data-perguntas-list]');
        const emptyState = () => context.querySelector('[data-perguntas-empty]');
        const feedback = form.querySelector('[data-pergunta-feedback]');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
        const fields = ['texto', 'tipo', 'options'];

        const toggleLoading = (isLoading) => {
            form.dataset.submitting = isLoading ? 'true' : 'false';
            if (submitBtn) {
                submitBtn.disabled = isLoading;
                submitBtn.innerHTML = isLoading
                    ? '<i class="bi bi-hourglass-split"></i> Salvando...'
                    : originalBtnHtml;
            }
        };

        const setErrors = (errors = {}) => {
            fields.forEach((field) => {
                const target = form.querySelector(`[data-error-target="${field}"]`);
                if (!target) {
                    return;
                }
                const messages = errors[field];
                if (messages && messages.length) {
                    target.textContent = messages[0];
                    target.hidden = false;
                } else {
                    target.textContent = '';
                    target.hidden = true;
                }
            });
        };

        const showFeedback = (message, type = 'success') => {
            if (!feedback) {
                return;
            }

            if (!message) {
                feedback.hidden = true;
                feedback.textContent = '';
                feedback.classList.remove('text-error');
                return;
            }

            feedback.textContent = message;
            feedback.hidden = false;
            feedback.classList.toggle('text-error', type === 'error');
        };

        const resetForm = () => {
            form.reset();
            const tipoSelect = form.querySelector('#tipo-novo');
            if (tipoSelect) {
                tipoSelect.value = 'texto';
                tipoSelect.dispatchEvent(new Event('change'));
            }
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();

            if (form.dataset.submitting === 'true') {
                return;
            }

            toggleLoading(true);
            setErrors();
            showFeedback('');

            const formData = new FormData(form);

            fetch(form.action, {
                method: form.method || 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            setErrors(data.errors);
                            showFeedback('Revise os campos destacados.', 'error');
                            return;
                        }

                        const errorMessage = data.message || 'Não foi possível salvar a pergunta.';
                        showFeedback(errorMessage, 'error');
                        throw new Error(errorMessage);
                    }

                    const emptyEl = emptyState();
                    if (emptyEl) {
                        emptyEl.remove();
                    }

                    if (perguntasList && data.html) {
                        const tempWrapper = document.createElement('div');
                        tempWrapper.innerHTML = data.html.trim();
                        const newCard = tempWrapper.firstElementChild;
                        if (newCard) {
                            perguntasList.insertAdjacentElement('afterbegin', newCard);
                            if (typeof initModalScripts === 'function') {
                                initModalScripts(newCard);
                            }
                        }
                    }

                    resetForm();
                    showFeedback(data.message || 'Pergunta adicionada com sucesso!');
                })
                .catch((error) => {
                    console.error('Falha ao adicionar pergunta:', error);
                })
                .finally(() => {
                    toggleLoading(false);
                });
        });
    });

    // --- controle de destinatários ---
    const selectDest = container.querySelector('#destinatarios');
    const divSelecionados = container.querySelector('#selecionados');

    if (selectDest && divSelecionados) {
        selectDest.addEventListener('change', function () {
            divSelecionados.style.display = (this.value === "0") ? 'block' : 'none';
        });
        selectDest.dispatchEvent(new Event('change'));
    }

    // --- inicialização do Select2 ---
    if (typeof $ !== 'undefined' && $.fn.select2) {
        const $container = $(container);
        const isDocumentContainer = container && container.nodeType === 9;
        const $dropdownParent = isDocumentContainer ? $(container.body || document.body) : $container;
        const $selectScope = isDocumentContainer ? $dropdownParent : $container;
        const $selects = $selectScope.find('select.select2');

        // Força dropdown sempre para baixo
        if (!window.__select2ForceBelowBound) {
            window.__select2ForceBelowBound = true;
            $(document).on('select2:open', () => {
                const $openDropdown = $('.select2-container--open .select2-dropdown');
                $openDropdown.removeClass('select2-dropdown--above').addClass('select2-dropdown--below');
                $openDropdown.css({ top: '100%' });
            });
        }

        $selects.each(function () {
            const $select = $(this);

            if ($select.data('select2')) {
                return;
            }

            const $existingContainer = $select.next('.select2.select2-container');
            if ($existingContainer.length) {
                $existingContainer.remove();
            }

            const placeholder = ($select.data('placeholder') || '').toString().trim();
            const searchPlaceholder = ($select.data('search-placeholder') || '').toString().trim();

            $select.select2({
                placeholder: placeholder || undefined,
                allowClear: Boolean(placeholder),
                width: '100%',
                dropdownParent: $dropdownParent
            });

            if (searchPlaceholder && $select.data('select2')) {
                $select.data('select2').$selection.find('input.select2-search__field')
                    .attr('placeholder', searchPlaceholder);
            }
        });

        // suporte a formulários legados (ex: avisos) que não usam a classe select2
        const $legacyGrupos = $container.find('#grupos').filter(function () {
            return !$(this).data('select2');
        });
        const $legacyMembros = $container.find('#membros').filter(function () {
            return !$(this).data('select2');
        });

        if ($legacyGrupos.length) {
            $legacyGrupos.select2({
                placeholder: 'Selecione os grupos',
                allowClear: true,
                width: '100%',
                dropdownParent: $container
            });

            const instance = $legacyGrupos.data('select2');
            if (instance) {
                instance.$selection.find('input.select2-search__field')
                    .attr('placeholder', 'Selecione os grupos');
            }
        }

        if ($legacyMembros.length) {
            $legacyMembros.select2({
                placeholder: 'Selecione os membros',
                allowClear: true,
                width: '100%',
                dropdownParent: $container
            });

            const instance = $legacyMembros.data('select2');
            if (instance) {
                instance.$selection.find('input.select2-search__field')
                    .attr('placeholder', 'Selecione os membros');
            }
        }
    }

    // --- formulários de escalas ---
    container.querySelectorAll('form[data-escala-form]').forEach(form => {
        if (form.dataset.escalaInitialized) {
            return;
        }
        form.dataset.escalaInitialized = 'true';

        const cultoSelect = form.querySelector('select#culto_id');
        const dataHoraGroup = form.querySelector('[data-escala-datahora]');
        const dataHoraInput = dataHoraGroup ? dataHoraGroup.querySelector('input[name="data_hora"]') : null;

        if (dataHoraInput) {
            dataHoraInput.disabled = false;
        }

        if (cultoSelect && dataHoraGroup) {
            const toggleDataHora = () => {
                const hasCulto = Boolean((cultoSelect.value || '').trim());

                dataHoraGroup.style.display = hasCulto ? 'none' : '';

                if (dataHoraInput) {
                    dataHoraInput.disabled = hasCulto;
                }

                if (dataHoraGroup instanceof HTMLElement) {
                    dataHoraGroup.setAttribute('aria-hidden', hasCulto ? 'true' : 'false');
                }
            };

            toggleDataHora();
            cultoSelect.addEventListener('change', toggleDataHora);
        }

        const itemsContainer = form.querySelector('[data-escala-items]');
        const addButton = form.querySelector('[data-escala-add]');
        const template = form.querySelector('#escala-item-template');

        if (!itemsContainer || !addButton || !template) {
            return;
        }

        const updateSummary = (item) => {
            const funcSummary = item.querySelector('[data-escala-funcao]');
            const responsavelSummary = item.querySelector('[data-escala-responsavel]');

            const funcInput = item.querySelector('input[data-escala-field="funcao"]');
            if (funcSummary && funcInput) {
                const value = funcInput.value.trim();
                funcSummary.textContent = value || 'Definir função';
            }

            if (responsavelSummary) {
                const membroSelect = item.querySelector('select[data-escala-field="membro_id"]');
                const externoInput = item.querySelector('input[data-escala-field="responsavel_externo"]');

                let responsavel = '';
                if (membroSelect && membroSelect.value) {
                    const option = membroSelect.options[membroSelect.selectedIndex];
                    responsavel = option ? option.text.trim() : '';
                } else if (externoInput && externoInput.value.trim()) {
                    responsavel = externoInput.value.trim();
                }

                responsavelSummary.textContent = responsavel || 'Responsável não definido';
            }
        };

        const updateTitles = () => {
            const items = itemsContainer.querySelectorAll('.escala-item');
            items.forEach((item, index) => {
                const orderLabel = item.querySelector('[data-escala-order]');
                if (orderLabel) {
                    orderLabel.textContent = `Item ${index + 1}`;
                }
                updateSummary(item);
            });
        };

        const getNextIndex = () => {
            const items = Array.from(itemsContainer.querySelectorAll('.escala-item'));
            if (!items.length) {
                return 0;
            }

            const maxIndex = items.reduce((acc, item) => {
                const value = Number(item.dataset.index);
                return Number.isFinite(value) ? Math.max(acc, value) : acc;
            }, -1);

            return maxIndex + 1;
        };

        const applySelect2 = (scope) => {
            if (typeof $ === 'undefined' || !$.fn.select2) {
                return;
            }

            $(scope).find('select.select2').each(function () {
                const $select = $(this);

                if ($select.data('select2')) {
                    return;
                }

                const $existingContainer = $select.next('.select2.select2-container');
                if ($existingContainer.length) {
                    $existingContainer.remove();
                }

                const placeholder = ($select.data('placeholder') || '').toString().trim();
                const searchPlaceholder = ($select.data('search-placeholder') || '').toString().trim();

                $select.select2({
                    placeholder: placeholder || undefined,
                    allowClear: Boolean(placeholder),
                    width: '100%',
                    dropdownParent: $(form)
                });

                if (searchPlaceholder && $select.data('select2')) {
                    $select.data('select2').$selection.find('input.select2-search__field')
                        .attr('placeholder', searchPlaceholder);
                }
            });
        };

        const attachSummaryHandler = (root) => {
            root.querySelectorAll('.escala-item').forEach(item => {
                const funcInput = item.querySelector('input[data-escala-field="funcao"]');
                if (funcInput && !funcInput.dataset.summaryHandlerAttached) {
                    const handler = () => updateSummary(item);
                    funcInput.dataset.summaryHandlerAttached = 'true';
                    funcInput.addEventListener('input', handler);
                    funcInput.addEventListener('change', handler);
                }

                const memberSelect = item.querySelector('select[data-escala-field="membro_id"]');
                if (memberSelect && !memberSelect.dataset.summaryHandlerAttached) {
                    const handler = () => updateSummary(item);
                    memberSelect.dataset.summaryHandlerAttached = 'true';
                    memberSelect.addEventListener('change', handler);
                }

                const externoInput = item.querySelector('input[data-escala-field="responsavel_externo"]');
                if (externoInput && !externoInput.dataset.summaryHandlerAttached) {
                    const handler = () => updateSummary(item);
                    externoInput.dataset.summaryHandlerAttached = 'true';
                    externoInput.addEventListener('input', handler);
                    externoInput.addEventListener('change', handler);
                }

                updateSummary(item);
            });
        };

        const attachRemoveHandler = (root) => {
            root.querySelectorAll('.btn-remover-item').forEach(button => {
                if (button.dataset.removeHandlerAttached) {
                    return;
                }
                button.dataset.removeHandlerAttached = 'true';
                button.addEventListener('click', () => {
                    const item = button.closest('.escala-item');
                    if (item) {
                        item.remove();
                        updateTitles();
                    }
                });
            });
        };

        applySelect2(itemsContainer);
        attachRemoveHandler(itemsContainer);
        attachSummaryHandler(itemsContainer);
        updateTitles();

        addButton.addEventListener('click', () => {
            const nextIndex = getNextIndex();
            const html = template.innerHTML.replace(/__INDEX__/g, nextIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const element = wrapper.firstElementChild;
            itemsContainer.appendChild(element);

            applySelect2(element);
            attachRemoveHandler(element);
            attachSummaryHandler(element);
            updateTitles();
        });

        const modalStackRef = (typeof modalStack !== 'undefined') ? modalStack : window.modalStack;
        const shouldHandleInlineModal = Array.isArray(modalStackRef) && modalStackRef.length > 0;
        if (shouldHandleInlineModal) {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalLabel = submitBtn ? submitBtn.innerHTML : '';

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
                }

                const formData = new FormData(form);
                const method = (form.getAttribute('method') || 'POST').toUpperCase();

                fetch(form.action, {
                    method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(async (response) => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok || data.success === false) {
                            const errors = data.errors ? Object.values(data.errors).flat() : null;
                            const message = errors && errors.length
                                ? errors.join(' ')
                                : (data.message || 'Erro ao salvar escala.');
                            throw new Error(message);
                        }
                        return data;
                    })
                    .then((data) => {
                        const detail = {
                            escalaId: data?.escala?.id,
                            cultoId: data?.escala?.culto_id || form.querySelector('input[name="culto_id"]')?.value || null,
                            escalasHtml: data?.escalasHtml || null,
                            escala: data?.escala || data?.data,
                            message: data?.message || 'Escala registrada com sucesso!',
                        };

                        if (typeof showSystemMessage === 'function' && detail.message) {
                            showSystemMessage(detail.message, 'success');
                        }

                        if (typeof voltarModalAnterior === 'function') {
                            voltarModalAnterior();

                            setTimeout(() => {
                                window.dispatchEvent(new CustomEvent('escalaUpdated', {
                                    detail
                                }));
                            }, 300);
                        }
                    })
                    .catch((error) => {
                        console.error('Falha ao salvar escala:', error);
                        if (typeof showSystemMessage === 'function') {
                            showSystemMessage(error.message || 'Erro ao salvar escala.', 'error');
                        }
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalLabel || '<i class="bi bi-check-circle"></i> Salvar Escala';
                        }
                    });
            });

            const deleteButton = form.querySelector('[data-escala-delete]');
            if (deleteButton && !deleteButton.dataset.deleteHandlerAttached) {
                deleteButton.dataset.deleteHandlerAttached = 'true';
                deleteButton.addEventListener('click', () => {
                    const deleteUrl = deleteButton.getAttribute('data-delete-url');
                    const cultoId = deleteButton.getAttribute('data-culto-id') || null;
                    const confirmMessage = deleteButton.getAttribute('data-confirm-message') || 'Deseja realmente excluir esta escala?';

                    const confirmPromise = typeof confirmarAcao === 'function'
                        ? confirmarAcao(confirmMessage)
                        : Promise.resolve(window.confirm(confirmMessage));

                    confirmPromise.then((confirmed) => {
                        if (!confirmed) {
                            return;
                        }

                        const originalDeleteLabel = deleteButton.innerHTML;
                        deleteButton.disabled = true;
                        deleteButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Excluindo...';

                        const token = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';

                        fetch(deleteUrl, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-HTTP-Method-Override': 'DELETE',
                            },
                        })
                            .then(async (response) => {
                                const data = await response.json().catch(() => ({}));
                                if (!response.ok || data.success === false) {
                                    const errors = data.errors ? Object.values(data.errors).flat() : null;
                                    const message = errors && errors.length
                                        ? errors.join(' ')
                                        : (data.message || 'Erro ao excluir escala.');
                                    throw new Error(message);
                                }
                                return data;
                            })
                            .then((data) => {
                                const detail = {
                                    cultoId: data?.culto_id || cultoId,
                                    escalasHtml: data?.escalasHtml || null,
                                    message: data?.message || 'Escala excluída com sucesso!',
                                };

                                if (typeof showSystemMessage === 'function' && detail.message) {
                                    showSystemMessage(detail.message, 'success');
                                }

                                if (typeof voltarModalAnterior === 'function') {
                                    voltarModalAnterior();

                                    setTimeout(() => {
                                        window.dispatchEvent(new CustomEvent('escalaDeleted', {
                                            detail
                                        }));
                                    }, 300);
                                }
                            })
                            .catch((error) => {
                                console.error('Falha ao excluir escala:', error);
                                if (typeof showSystemMessage === 'function') {
                                    showSystemMessage(error.message || 'Erro ao excluir escala.', 'error');
                                }
                            })
                            .finally(() => {
                                deleteButton.disabled = false;
                                deleteButton.innerHTML = originalDeleteLabel;
                            });
                    });
                });
            }
        }
    });

    // --- gerenciador de participantes das células---
    const participanteTab = container.querySelector('#participantes[data-participantes]');
    if (participanteTab && !participanteTab.dataset.managerInitialized) {
        participanteTab.dataset.managerInitialized = 'true';

        const perPage = Number(participanteTab.dataset.pageSize || 6);
        const hiddenSelect = participanteTab.querySelector('#participantes-hidden');
        const select = participanteTab.querySelector('#participante-select');
        const addButton = participanteTab.querySelector('#btn-add-participante');
        const listContent = participanteTab.querySelector('.participantes-content');
        const pagination = participanteTab.querySelector('.participantes-pagination');
        const fallbackAvatar = participanteTab.dataset.fallbackAvatar || '';

        let currentPage = 1;
        let initialDataRaw = [];

        try {
            initialDataRaw = participanteTab.dataset.participantes ? JSON.parse(participanteTab.dataset.participantes) : [];
        } catch (error) {
            console.error('Falha ao interpretar os participantes iniciais:', error);
            initialDataRaw = [];
        }

        const state = new Map(initialDataRaw.map(item => [String(item.id), item]));

        function syncHiddenOptions() {
            if (!hiddenSelect) return;

            const stateIds = new Set(state.keys());

            Array.from(hiddenSelect.options).forEach(option => {
                if (!stateIds.has(option.value)) {
                    option.remove();
                } else {
                    option.selected = true;
                    stateIds.delete(option.value);
                }
            });

            stateIds.forEach(id => {
                const opt = document.createElement('option');
                opt.value = id;
                opt.selected = true;
                hiddenSelect.appendChild(opt);
            });
        }

        function setOptionAvailability() {
            if (!select) return;

            Array.from(select.options).forEach(option => {
                if (!option.value) {
                    return;
                }
                option.disabled = state.has(option.value);
            });
        }

        function renderPagination(totalItems) {
            if (!pagination) return;

            pagination.innerHTML = '';
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            if (totalPages <= 1) {
                return;
            }

            for (let page = 1; page <= totalPages; page++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'page-btn' + (page === currentPage ? ' active' : '');
                button.dataset.page = String(page);
                button.textContent = page;
                pagination.appendChild(button);
            }
        }

        function renderList() {
            if (!listContent) return;

            const items = Array.from(state.values()).sort((a, b) => a.nome.localeCompare(b.nome));
            const totalItems = items.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

            const start = (currentPage - 1) * perPage;
            const pageItems = items.slice(start, start + perPage);

            listContent.innerHTML = '';

            if (pageItems.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'card empty-state';
                empty.textContent = 'Nenhum participante adicionado até o momento.';
                listContent.appendChild(empty);
            } else {
                pageItems.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'list-item participante-row';
                    row.dataset.id = item.id;
                    row.innerHTML = `
                        <div class="item item-2">
                            <p style="display:flex; align-items:center; gap:.5em">
                                <img src="${item.foto || fallbackAvatar}" class="avatar" alt="Avatar">
                                ${item.nome}
                            </p>
                        </div>
                        <div class="item item-1"><p>${item.telefone}</p></div>
                        <div class="item item-2"><p>${item.endereco}</p></div>
                        <div class="item item-1"><p>${item.ministerio}</p></div>
                        <div class="item item-1 participante-actions">
                            <button type="button" class="btn participante-remove" data-id="${item.id}"><i class="bi bi-person-dash"></i> Remover</button>
                        </div>
                    `;
                    listContent.appendChild(row);
                });
            }

            renderPagination(totalItems);
            syncHiddenOptions();
            setOptionAvailability();
        }

        if (addButton && select) {
            addButton.addEventListener('click', () => {
                const option = select.options[select.selectedIndex];
                if (!option || !option.value || option.disabled) {
                    return;
                }

                const id = option.value;
                if (state.has(id)) {
                    select.value = '';
                    return;
                }

                const item = {
                    id,
                    nome: option.dataset.nome || option.textContent.trim(),
                    telefone: option.dataset.telefone || 'Não informado',
                    endereco: option.dataset.endereco || 'Não informado',
                    ministerio: option.dataset.ministerio || 'Não informado',
                    foto: option.dataset.foto || fallbackAvatar,
                };

                state.set(id, item);
                select.value = '';
                currentPage = 1;
                renderList();
            });
        }

        if (listContent) {
            listContent.addEventListener('click', (event) => {
                const button = event.target.closest('.participante-remove');
                if (!button) {
                    return;
                }

                const id = button.dataset.id;
                if (!id) {
                    return;
                }

                state.delete(id);
                renderList();
            });
        }

        if (pagination) {
            pagination.addEventListener('click', (event) => {
                const button = event.target.closest('.page-btn');
                if (!button) {
                    return;
                }

                const page = Number(button.dataset.page) || 1;
                if (page === currentPage) {
                    return;
                }

                currentPage = page;
                renderList();
            });
        }

        renderList();
    }

    // --- Controle de motivo de desligamento em formulários de membros ---
    const statusSelect = container.querySelector('#ativo');
    const motivoDiv = container.querySelector('#motivo_desligamento_div');

    if (statusSelect && motivoDiv) {
        function toggleMotivoDiv() {
            if (statusSelect.value === '0') {
                motivoDiv.style.display = 'flex';
            } else {
                motivoDiv.style.display = 'none';
            }
        }

        statusSelect.addEventListener('change', toggleMotivoDiv);
        toggleMotivoDiv(); // Executa ao carregar para definir o estado inicial
    }

    // Inicializa scripts para botões de menu nos paineis 
    if (typeof window !== 'undefined' && typeof window.initOptionsMenus === 'function') {
        window.initOptionsMenus(container || document);
    }

    // --- Atualiza lista de escalas do culto ao criar/excluir em modal aninhado ---
    if (!window._escalaEventosRegistrados) {
        window._escalaEventosRegistrados = true;

        const atualizarListaEscalas = (detail) => {
            const info = detail || {};
            const { cultoId, escalasHtml, message } = info;

            if (escalasHtml) {
                document.querySelectorAll('[data-escalas-lista]').forEach((lista) => {
                    const listaCultoId = lista.getAttribute('data-culto-id');
                    if (cultoId && listaCultoId && Number(listaCultoId) !== Number(cultoId)) {
                        return;
                    }
                    lista.innerHTML = escalasHtml;
                    if (typeof initModalScripts === 'function') {
                        initModalScripts(lista);
                    }
                });
            }

            if (typeof showSystemMessage === 'function' && message) {
                showSystemMessage(message, 'success');
            }
        };

        const eventosEscala = ['escalaCreated', 'escalaDeleted', 'escalaUpdated'];
        eventosEscala.forEach(evt => {
            window.addEventListener(evt, (e) => atualizarListaEscalas(e.detail));
        });
    }

    // --- Gestor de Imagens ---
    const gestorImagens = container.querySelector('#form-gestor-imagens');
    if (gestorImagens && !gestorImagens.dataset.gestorInitialized) {
        gestorImagens.dataset.gestorInitialized = 'true';

        let selectedCard = null;
        const btnSelecionar = gestorImagens.querySelector('#btn-selecionar-imagem');
        const acervo = gestorImagens.querySelector('#acervo-imagens');

        if (acervo && btnSelecionar) {
            // Adiciona evento de clique nos cards
            acervo.addEventListener('click', (e) => {
                const card = e.target.closest('.card-arquivo');
                if (!card) return;

                // Evita selecionar ao clicar no botão de delete
                if (e.target.closest('.delete-img')) {
                    return;
                }

                // Remove seleção anterior
                if (selectedCard) {
                    selectedCard.classList.remove('selected');
                }

                // Se clicar no mesmo card, desseleciona
                if (selectedCard === card) {
                    selectedCard = null;
                    btnSelecionar.classList.add('inactive');
                    btnSelecionar.disabled = true;
                } else {
                    // Seleciona o novo card
                    card.classList.add('selected');
                    selectedCard = card;
                    btnSelecionar.classList.remove('inactive');
                    btnSelecionar.disabled = false;
                }
            });

            // Evento do botão selecionar
            btnSelecionar.addEventListener('click', () => {
                if (selectedCard) {
                    const arquivoId = selectedCard.dataset.arquivoId;
                    const arquivoUrl = selectedCard.dataset.arquivoUrl;

                    console.log('Arquivo selecionado:', { id: arquivoId, url: arquivoUrl });

                    // Dispara evento na janela principal
                    window.top.postMessage({
                        type: 'imagemSelecionada',
                        arquivoId: arquivoId,
                        arquivoUrl: arquivoUrl
                    }, '*');

                    // Fecha o modal
                    if (typeof window.fecharJanelaModal === 'function') {
                        window.fecharJanelaModal();
                    } else if (typeof window.top.fecharJanelaModal === 'function') {
                        window.top.fecharJanelaModal();
                    }
                }
            });
        }
    }

    // --- Inicialização do Livewire em componentes carregados dinamicamente ---
    const livewireComponents = container.querySelectorAll('[wire\\:id]');
    if (livewireComponents.length > 0 && typeof window.Livewire !== 'undefined') {
        // Recarrega os componentes Livewire no container
        livewireComponents.forEach(component => {
            try {
                if (window.Livewire && window.Livewire.rescan) {
                    window.Livewire.rescan();
                }
            } catch (error) {
                console.error('Erro ao inicializar componente Livewire:', error);
            }
        });
    }
}

// Função para inicializar formulário de criar evento com AJAX
function initFormCriarEvento(form) {
    if (!form) return;
    if (form.dataset.ajaxInitialized === 'true') return;
    
    form.dataset.ajaxInitialized = 'true';
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Validação
        const titulo = form.querySelector('[name="titulo"]');
        
        if (!titulo || !titulo.value.trim()) {
            showSystemMessage('Por favor, preencha o título do evento.', 'error');
            return;
        }
        
        // Valida se há ao menos uma ocorrência
        const ocorrencias = form.querySelectorAll('input[name*="[data_ocorrencia]"]');
        if (ocorrencias.length === 0) {
            showSystemMessage('Por favor, adicione ao menos uma ocorrência ao evento.', 'error');
            return;
        }
        
        // Valida se todas as ocorrências têm data preenchida
        let todasPreenchidas = true;
        ocorrencias.forEach(input => {
            if (!input.value) {
                todasPreenchidas = false;
            }
        });
        
        if (!todasPreenchidas) {
            showSystemMessage('Por favor, preencha a data de todas as ocorrências.', 'error');
            return;
        }
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
        }
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Adicionar Evento';
            }
            
            if (!data.success) {
                showSystemMessage(data.message || 'Erro ao criar evento.', 'error');
                return;
            }
            
            const evento = data.evento || data.data;
            if (!evento || !evento.id) {
                showSystemMessage('Evento criado mas dados incompletos.', 'error');
                return;
            }
            
            // Mostra mensagem de sucesso
            if (data.message) {
                showSystemMessage(data.message, 'success');
            }
            
            window.eventoRecenteCriado = {
                id: evento.id,
                titulo: evento.titulo,
                evento: evento
            };
            
            // Marca que precisa recarregar o modal anterior
            window.recarregarModalAnterior = true;
            
            if (typeof voltarModalAnterior === 'function') {
                voltarModalAnterior();
                
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('eventoCreated', {
                        detail: {
                            eventoId: evento.id,
                            eventoTitulo: evento.titulo,
                            evento: evento
                        }
                    }));
                }, 300);
            }
        })
        .catch(error => {
            console.error('Erro ao criar evento:', error);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Adicionar Evento';
            }
            showSystemMessage('Erro ao criar evento.', 'error');
        });
    });
}

function initPreletorToggle(scope) {
    const selectEl = scope.querySelector('[data-preletor-select]');
    const toggleBtn = scope.querySelector('[data-preletor-toggle]');
    const externalInput = scope.querySelector('[data-preletor-external-input]');

    if (!selectEl || !toggleBtn || !externalInput) {
        return;
    }

    const toggleSelect2Visibility = (hide) => {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            const $select = $(selectEl);
            const $container = $select.next('.select2');
            if ($container.length) {
                $container.toggle(!hide);
            }
            // se o select2 ainda não foi inicializado, tenta novamente em seguida
            if (!$container.length) {
                setTimeout(() => {
                    const $cont2 = $select.next('.select2');
                    if ($cont2.length) {
                        $cont2.toggle(!hide);
                    }
                }, 50);
            }
        } else {
            selectEl.style.display = hide ? 'none' : '';
        }
    };

    const setMode = (mode) => {
        const isExternal = mode === 'external';
        scope.dataset.mode = mode;

        selectEl.disabled = isExternal;
        toggleSelect2Visibility(isExternal);

        selectEl.style.display = isExternal ? 'none' : '';
        externalInput.style.display = isExternal ? '' : 'none';
        externalInput.disabled = !isExternal;

        toggleBtn.textContent = isExternal ? 'Inserir membro' : 'Inserir externo';
    };

    const initialExternal = !selectEl.value && externalInput.value;
    setMode(initialExternal ? 'external' : 'member');

    // Reaplica após a inicialização do select2
    setTimeout(() => {
        setMode(scope.dataset.mode || 'member');
    }, 400);

    toggleBtn.addEventListener('click', () => {
        const isExternal = scope.dataset.mode === 'external';
        setMode(isExternal ? 'member' : 'external');
    });
}

function initFormEditarCulto(selectEvento) {
    if (!selectEvento || selectEvento.dataset.eventListenersAdded === 'true') {
        return;
    }
    
    selectEvento.dataset.eventListenersAdded = 'true';
    
    // Função para adicionar evento ao select
    function adicionarEventoAoSelect(eventoId, eventoTitulo) {
        if (!selectEvento) return false;
        
        const optionExists = Array.from(selectEvento.options).some(opt => opt.value == eventoId);
        
        if (!optionExists) {
            const newOption = new Option(eventoTitulo, eventoId, true, true);
            selectEvento.add(newOption);
            
            // Força re-renderização completa do select
            const parent = selectEvento.parentNode;
            const clone = selectEvento.cloneNode(true);
            parent.replaceChild(clone, selectEvento);
            selectEvento = clone;
            
            // Feedback visual
            setTimeout(() => {
                clone.focus();
                clone.scrollTop = clone.scrollHeight;
                clone.style.transition = 'background-color 0.3s ease';
                clone.style.backgroundColor = '#22c55e';
                setTimeout(() => {
                    clone.style.backgroundColor = 'color-mix(in srgb, var(--success-color, #22c55e) 20%, transparent)';
                    setTimeout(() => {
                        clone.style.backgroundColor = '';
                    }, 1500);
                }, 300);
            }, 100);
            
            return true;
        } else {
            selectEvento.value = eventoId;
            selectEvento.focus();
            selectEvento.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Feedback visual
            selectEvento.style.transition = 'background-color 0.3s ease';
            selectEvento.style.backgroundColor = '#3b82f6';
            setTimeout(() => {
                selectEvento.style.backgroundColor = '';
            }, 800);
            
            return true;
        }
    }
    
    // Verifica se há um evento recente criado ao carregar
    if (window.eventoRecenteCriado) {
        setTimeout(() => {
            adicionarEventoAoSelect(
                window.eventoRecenteCriado.id, 
                window.eventoRecenteCriado.titulo
            );
            window.eventoRecenteCriado = null;
        }, 200);
    }
    
    // Escuta quando o modal é restaurado
    window.addEventListener('modalRestaurado', function(e) {
        if (window.eventoRecenteCriado) {
            adicionarEventoAoSelect(
                window.eventoRecenteCriado.id, 
                window.eventoRecenteCriado.titulo
            );
            window.eventoRecenteCriado = null;
        }
    });
    
    // Escuta o evento de criação de evento
    window.addEventListener('eventoCreated', function(e) {
        const { eventoId, eventoTitulo } = e.detail;
        
        if (!eventoId || !eventoTitulo) return;
        
        setTimeout(() => {
            adicionarEventoAoSelect(eventoId, eventoTitulo);
        }, 300);
    });
}
