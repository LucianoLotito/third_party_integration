<?php

$version = explode('.', env('API_VERSION'));

return [
    'major' => $version[0],
    'minor' => $version[1],
    'patch' => $version[2],
];
