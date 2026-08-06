<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supabase credentials
    |--------------------------------------------------------------------------
    |
    | The web app pushes/pulls data to/from a Supabase Postgres project used by
    | the Flutter mobile app. The service role key bypasses RLS (server-side).
    |
    */

    'url' => env('SUPABASE_URL', ''),

    'anon_key' => env('SUPABASE_ANON_KEY', ''),

    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY', ''),

];
