<?php

return [
    'types' => [
        'initial' => 'initial',
        'handover' => 'handover',
        'routine' => 'routine',
        'return' => 'return',
        'fleet_exit' => 'fleet_exit',
    ],

    'type_labels' => [
        'initial' => 'Inicial',
        'handover' => 'Entrega',
        'routine' => 'Rotina',
        'return' => 'Recolha do motorista',
        'fleet_exit' => 'Saida da frota',
    ],

    'statuses' => [
        'draft' => 'draft',
        'in_progress' => 'in_progress',
        'ready_to_sign' => 'ready_to_sign',
        'signed' => 'signed',
        'closed' => 'closed',
    ],

    'status_labels' => [
        'draft' => 'Rascunho',
        'in_progress' => 'Em progresso',
        'ready_to_sign' => 'Pronta para assinar',
        'signed' => 'Assinada',
        'closed' => 'Fechada',
    ],

    'step_labels' => [
        1 => 'Identificacao da viatura',
        2 => 'Identificacao do condutor',
        3 => 'Documentacao',
        4 => 'Estado operacional',
        5 => 'Acessorios e extras',
        6 => 'Fotografias exteriores',
        7 => 'Fotografias interiores',
        8 => 'Danos exteriores',
        9 => 'Danos interiores',
        10 => 'Extras e observacoes',
        11 => 'Assinaturas',
        12 => 'Fecho e PDF',
    ],

    'required_slots' => [
        'exterior' => [
            'front',
            'front_left_45',
            'left',
            'rear_left_45',
            'rear',
            'rear_right_45',
            'right',
            'front_right_45',
        ],
        'interior' => [
            'dashboard',
            'front_seats',
            'rear_seats',
            'trunk',
            'odometer',
            'center_console',
        ],
    ],

    'slot_labels' => [
        'exterior' => [
            'front' => 'Frente',
            'front_left_45' => 'Frente esquerda 45 graus',
            'left' => 'Lado esquerdo',
            'rear_left_45' => 'Traseira esquerda 45 graus',
            'rear' => 'Traseira',
            'rear_right_45' => 'Traseira direita 45 graus',
            'right' => 'Lado direito',
            'front_right_45' => 'Frente direita 45 graus',
        ],
        'interior' => [
            'dashboard' => 'Tablier',
            'front_seats' => 'Bancos dianteiros',
            'rear_seats' => 'Bancos traseiros',
            'trunk' => 'Bagageira',
            'odometer' => 'Odometro',
            'center_console' => 'Consola central',
        ],
    ],

    'damage_locations' => [
        'body' => 'Carrocaria',
        'interior' => 'Interior',
        'tires_rims' => 'Pneus/Jantes',
        'glass' => 'Vidros',
        'other' => 'Outros',
    ],

    'damage_types' => [
        'scratch' => 'Risco',
        'dent' => 'Amolgadela',
        'crack' => 'Racha',
        'broken' => 'Partido',
        'stain' => 'Mancha',
        'tear' => 'Rasgao',
        'missing' => 'Em falta',
        'other' => 'Outro',
    ],
];
