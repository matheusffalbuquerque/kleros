export function initModalScripts(container) {
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

        $selects.each(function () {
            const $select = $(this);

            if ($select.data('select2')) {
                return;
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

    // Inicializa scripts para botões de menu nos paineis 
    if (typeof window !== 'undefined' && typeof window.initOptionsMenus === 'function') {
        window.initOptionsMenus(container || document);
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

