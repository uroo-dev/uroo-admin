<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mobile sync tables
    |--------------------------------------------------------------------------
    |
    | Tables synced with the Flutter mobile app (parents first so FK
    | remapping works when the phone pushes newly created rows).
    |
    | 'user' => 'user_id'                            : direct owner column
    | 'user' => [parent, fk, parent_user_column]     : owner via parent table
    |
    */

    'tables' => [
        'clients' => ['user' => 'user_id'],
        'projects' => ['user' => 'user_id'],
        'invoices' => ['user' => 'user_id'],
        'invoice_payments' => ['user' => ['invoices', 'invoice_id', 'user_id']],
        'app_ideas' => ['user' => 'user_id'],
        'brain_dumps' => ['user' => 'user_id'],
        'notes' => ['user' => 'user_id'],
        'savings_goals' => ['user' => 'user_id'],
        'savings_transactions' => ['user' => ['savings_goals', 'goal_id', 'user_id']],
    ],

];
