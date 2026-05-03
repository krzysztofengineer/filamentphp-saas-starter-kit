<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account deletion grace period
    |--------------------------------------------------------------------------
    |
    | Number of days between scheduling an account for deletion and the user
    | being permanently pruned. During this window the user can sign back in
    | and cancel the deletion via the dashboard banner.
    */

    'deletion_grace_days' => (int) env('ACCOUNT_DELETION_GRACE_DAYS', 30),
];
