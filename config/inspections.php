<?php

return [
    'types' => [
        'initial' => 'initial',
        'handover' => 'handover',
        'routine' => 'routine',
        'return' => 'return',
    ],

    'statuses' => [
        'draft' => 'draft',
        'in_progress' => 'in_progress',
        'ready_to_sign' => 'ready_to_sign',
        'signed' => 'signed',
        'closed' => 'closed',
    ],

    'step_labels' => [
        1 => 'Identificação da viatura',
        2 => 'Identificação do condutor',
        3 => 'Documentação e estado operacional',
        4 => 'Fotografias exteriores',
        5 => 'Fotografias interiores',
        6 => 'Danos exteriores',
        7 => 'Danos interiores',
        8 => 'Extras e observações',
        9 => 'Assinaturas',
        10 => 'Fecho e PDF',
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
        'body' => 'Carroçaria',
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
        'tear' => 'Rasgão',
        'missing' => 'Em falta',
        'other' => 'Outro',
    ],
];
