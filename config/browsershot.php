<?php

return [
    'node_binary' => env('BROWSERSHOT_NODE_BINARY'),
    'npm_binary' => env('BROWSERSHOT_NPM_BINARY'),
    'node_module_path' => env('BROWSERSHOT_NODE_MODULE_PATH', base_path('node_modules')),
    'chrome_path' => env('BROWSERSHOT_CHROME_PATH'),
    'no_sandbox' => (bool) env('BROWSERSHOT_NO_SANDBOX', false),
    'timeout' => (int) env('BROWSERSHOT_TIMEOUT', 120),
];
