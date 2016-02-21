<?php
return [
    'roles' => [
        'super_administrator' => ['name' => 'Super Administrator', 'level' => 0],
        'hub_administrator' => ['name' => 'Hub Administrator', 'level' => 1],
        'self' => ['name' => 'Self', 'level' => 2000, 'virtual' => true], // for accessing your own user record
        'guest' => ['name' => 'Guest', 'level' => 9999, 'virtual' => true]
    ],
    'globalRules' => [
        ['allow', 'roles' => 'super_administrator', 'privileges' => null],
    ],
    'modelRules' => [
        'Users' => ['model-everyone'],
    ],
    'ruleSets' => [
        // note: for route rules, no context is pulled first. For example, any rule that references 'instructor' would allow ANY instructor
        'model-everyone' => [
            ['allow', 'roles' => null]
        ],
        'route-everyone' => [
            ['allow', 'roles' => null]
        ],
        'route-admins' => [
            ['allow', 'roles' => 'hub_administrator']
        ],
        'route-super' => [] // caught by global rule for super admin
    ],
    'routeRules' => [
        'publicRoute' => ['route-everyone'],
        'getUsers' => ['route-admins'],
    ]
];
?>
