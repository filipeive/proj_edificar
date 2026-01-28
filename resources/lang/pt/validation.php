<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'Informe um e-mail válido.',
    'string' => 'O campo :attribute deve ser um texto.',
    'numeric' => 'O campo :attribute deve ser numérico.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'array' => 'O campo :attribute deve ser uma lista.',
    'in' => 'O :attribute selecionado é inválido.',
    'exists' => 'O :attribute selecionado é inválido.',
    'unique' => 'Este :attribute já está em uso.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'min' => [
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'array' => 'O campo :attribute deve ter no mínimo :min itens.',
    ],
    'max' => [
        'string' => 'O campo :attribute deve ter no máximo :max caracteres.',
        'numeric' => 'O campo :attribute deve ser no máximo :max.',
        'array' => 'O campo :attribute deve ter no máximo :max itens.',
    ],
    'attributes' => [
        'email' => 'e-mail',
        'password' => 'senha',
        'name' => 'nome',
    ],
];
