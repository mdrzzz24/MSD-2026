<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Client Recommendation Reasons
    |--------------------------------------------------------------------------
    |
    | Reasons shown to client users when they reject a registrant.
    | The selected value is stored in the registrant's `client_remark` field.
    |
    */

    'reject' => [
        'WAITING FOR METRODATA APPROVAL',
        'DECLINED (INTERN)',
        'WAITING LIST',
        'DECLINE (COMPETITOR)',
        'DECLINE (PRINCIPAL NON SPONSOR)',
        'DECLINE (DISTRIBUTOR)',
        'DATA NOT VALID (EMAIL, TITLE, NAMA, NO TELP)',
        'COOKIE MONSTER',
        'STUDENT',
        'NOT REPRESENTING ANY COMPANY',
    ],

];
