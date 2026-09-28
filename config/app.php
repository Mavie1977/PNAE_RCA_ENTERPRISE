<?php
return [
 'name'=>env('APP_NAME','PNAE-RCA V10 Enterprise'),
 'env'=>env('APP_ENV','production'),
 'debug'=>(bool) env('APP_DEBUG', false),
 'url'=>env('APP_URL','http://localhost'),
 'timezone'=>env('APP_TIMEZONE','UTC'),
 'locale'=>'fr',
 'fallback_locale'=>'fr',
 'faker_locale'=>'fr_FR',
 'cipher'=>'AES-256-CBC',
 'key'=>env('APP_KEY'),
 'previous_keys'=>array_filter(explode(',', env('APP_PREVIOUS_KEYS',''))),
];
