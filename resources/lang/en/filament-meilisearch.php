<?php

return [
    'navigation' => [
        'group' => 'Meilisearch',
    ],
    'pages' => [
        'dashboard' => [
            'title' => 'Meilisearch Dashboard',
        ],
        'indexes' => [
            'title' => 'Indexes',
        ],
        'documents' => [
            'title' => 'Documents',
        ],
        'keys' => [
            'title' => 'API Keys',
        ],
        'tasks' => [
            'title' => 'Tasks',
        ],
        'dumps' => [
            'title' => 'Dumps',
        ],
        'snapshots' => [
            'title' => 'Snapshots',
        ],
        'settings' => [
            'title' => 'Settings',
        ],
    ],
    'actions' => [
        'create' => 'Create',
        'delete' => 'Delete',
        'view' => 'View',
        'edit' => 'Edit',
        'search' => 'Search',
    ],
    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'primary_key' => 'Primary Key',
        'host' => 'Host',
        'api_key' => 'API Key',
    ],
    'messages' => [
        'created' => ':resource created successfully.',
        'updated' => ':resource updated successfully.',
        'deleted' => ':resource deleted successfully.',
        'error' => 'An error occurred: :message',
    ],
];
