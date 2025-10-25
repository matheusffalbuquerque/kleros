<?php

return [
    'title' => 'Configurações',
    'tabs' => [
        'general' => 'Dados Gerais',
        'visual' => 'Personalização',
        'administrative' => 'Administração',
    ],
    'sections' => [
        'institutional' => [
            'title' => 'Institucional',
            'fields' => [
                'identification' => 'Identificação',
                'short_name' => 'Nome reduzido',
                'cnpj' => 'CNPJ',
                'email' => 'Email',
                'phone' => 'Telefone',
            ],
        ],
        'location' => [
            'title' => 'Localização',
            'fields' => [
                'address' => 'Endereço',
                'number' => 'Número',
                'complement' => 'Complemento',
                'district' => 'Bairro',
                'country' => 'País',
                'state' => 'Estado',
                'city' => 'Cidade',
            ],
            'placeholders' => [
                'country' => 'Selecione um país',
                'state' => 'Selecione um estado/região',
                'city' => 'Selecione uma cidade',
            ],
        ],
        'visual' => [
            'title' => 'Características Visuais',
            'files' => [
                'title' => 'Arquivos e imagens',
                'logo' => 'Logo da congregação',
                'logo_hint' => 'Selecione um arquivo PNG ou SVG',
                'banner' => 'Banner para tela de login',
                'banner_hint' => 'Imagem horizontal (JPG ou PNG)',
                'upload' => 'Upload',
                'current_logo' => 'Logo atual',
                'current_banner' => 'Banner atual',
            ],
            'colors' => [
                'title' => 'Cores e fontes',
                'description' => 'Escolha a paleta e tipografia',
                'primary' => 'Cor primária',
                'secondary' => 'Cor secundária',
                'accent' => 'Cor de destaque',
                'font' => 'Fonte de texto',
                'preview_label' => 'Exemplo fonte escolhida:',
                'preview_text' => 'Tudo posso naquele que me fortalece.',
            ],
            'themes' => [
                'title' => 'Tema visual',
                'classic' => 'Clássico',
                'modern' => 'Moderno',
                'vintage' => 'Vintage',
            ],
        ],
        'administrative' => [
            'title' => 'Preferências',
            'grouping' => 'Organização de grupos',
            'grouping_options' => [
                'grupo' => 'Apenas grupos',
                'departamento' => 'Grupos e Departamentos',
                'setor' => 'Grupos, Departamentos, Setores',
            ],
            'cells' => [
                'label' => 'Células/Pequenos Grupos',
                'active' => 'Ativo',
                'inactive' => 'Inativo',
            ],
            'language' => [
                'label' => 'Idioma do sistema',
                'language_options' => [
                    'Inglês' => 'en',
                    'Português' => 'pt',
                    'Espanhol' => 'es',
                ],
            ],
        ],
    ],
    'buttons' => [
        'update' => 'Atualizar',
        'restore' => 'Restaurar',
        'back' => 'Voltar',
    ],
    'placeholders' => [
        'email' => 'myemail@domain.com',
        'phone' => '(00) 00000-0000',
        'cnpj' => '00.000.000/0000-00',
    ],
    'scripts' => [
        'no_file' => 'Nenhum arquivo selecionado',
        'file_deleted' => 'Arquivo excluído.',
        'error_delete' => 'Erro ao excluir o arquivo.',
        'loading' => 'Carregando...',
        'select_state' => 'Selecione um estado',
        'select_city' => 'Selecione uma cidade',
    ],
];
