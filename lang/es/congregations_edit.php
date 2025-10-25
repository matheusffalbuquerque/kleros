<?php

return [
    'title' => 'Configuraciones',
    'tabs' => [
        'general' => 'Datos generales',
        'visual' => 'Personalización',
        'administrative' => 'Administración',
    ],
    'sections' => [
        'institutional' => [
            'title' => 'Institucional',
            'fields' => [
                'identification' => 'Identificación',
                'short_name' => 'Nombre reducido',
                'cnpj' => 'Registro fiscal',
                'email' => 'Correo electrónico',
                'phone' => 'Teléfono',
            ],
        ],
        'location' => [
            'title' => 'Ubicación',
            'fields' => [
                'address' => 'Dirección',
                'number' => 'Número',
                'complement' => 'Complemento',
                'district' => 'Barrio',
                'country' => 'País',
                'state' => 'Estado/Región',
                'city' => 'Ciudad',
            ],
            'placeholders' => [
                'country' => 'Selecciona un país',
                'state' => 'Selecciona un estado/región',
                'city' => 'Selecciona una ciudad',
            ],
        ],
        'visual' => [
            'title' => 'Características visuales',
            'files' => [
                'title' => 'Archivos e imágenes',
                'logo' => 'Logo de la congregación',
                'logo_hint' => 'Selecciona un archivo PNG o SVG',
                'banner' => 'Banner para la pantalla de inicio de sesión',
                'banner_hint' => 'Imagen horizontal (JPG o PNG)',
                'upload' => 'Subir',
                'current_logo' => 'Logo actual',
                'current_banner' => 'Banner actual',
            ],
            'colors' => [
                'title' => 'Colores y fuentes',
                'description' => 'Elige la paleta y la tipografía',
                'primary' => 'Color primario',
                'secondary' => 'Color secundario',
                'accent' => 'Color de destaque',
                'font' => 'Fuente de texto',
                'preview_label' => 'Ejemplo de fuente elegida:',
                'preview_text' => 'Todo lo puedo en Cristo que me fortalece.',
            ],
            'themes' => [
                'title' => 'Tema visual',
                'classic' => 'Clásico',
                'modern' => 'Moderno',
                'vintage' => 'Vintage',
            ],
        ],
        'administrative' => [
            'title' => 'Preferencias',
            'grouping' => 'Organización de grupos',
            'grouping_options' => [
                'grupo' => 'Solo grupos',
                'departamento' => 'Grupos y Departamentos',
                'setor' => 'Grupos, Departamentos y Sectores',
            ],
            'cells' => [
                'label' => 'Células/Pequeños grupos',
                'active' => 'Activo',
                'inactive' => 'Inactivo',
            ],
            'language' => [
                'label' => 'Lenguaje del sistema',
                'language_options' => [
                    'Inglés' => 'en',
                    'Portugués' => 'pt',
                    'Espanhol' => 'es',
                ],
            ],
        ],
    ],
    'buttons' => [
        'update' => 'Actualizar',
        'restore' => 'Restaurar',
        'back' => 'Volver',
    ],
    'placeholders' => [
        'email' => 'miemail@dominio.com',
        'phone' => '(00) 00000-0000',
        'cnpj' => '00.000.000/0000-00',
    ],
    'scripts' => [
        'no_file' => 'Ningún archivo seleccionado',
        'file_deleted' => 'Archivo eliminado.',
        'error_delete' => 'Error al eliminar el archivo.',
        'loading' => 'Cargando...',
        'select_state' => 'Selecciona un estado',
        'select_city' => 'Selecciona una ciudad',
    ],
];
