<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jitsi Meet Domain & Service Settings
    |--------------------------------------------------------------------------
    |
    | Domain of the Jitsi Meet server (default: 'meet.jit.si' for public demo).
    | For on-premise or JaaS deployments, specify your custom domain.
    |
    */
    'domain' => env('JITSI_DOMAIN', 'meet.jit.si'),

    /*
    |--------------------------------------------------------------------------
    | Jitsi App ID / Prefix (for JaaS or tenant setups)
    |--------------------------------------------------------------------------
    */
    'app_id' => env('JITSI_APP_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Room Prefix
    |--------------------------------------------------------------------------
    | Prefix to prevent room collision on public shared Jitsi instances.
    */
    'room_prefix' => env('JITSI_ROOM_PREFIX', 'pkflow_'),

    /*
    |--------------------------------------------------------------------------
    | Demo Mode Warning
    |--------------------------------------------------------------------------
    | When true, displays explicit warnings that public Jitsi instance is
    | for demonstration and testing only with synthetic patient data.
    */
    'is_demo' => env('JITSI_IS_DEMO', true),

    /*
    |--------------------------------------------------------------------------
    | JWT Secret / Signing Key (for production authenticated rooms)
    |--------------------------------------------------------------------------
    | Handled exclusively on backend. Never exposed to frontend.
    */
    'secret' => env('JITSI_SECRET', null),
];
