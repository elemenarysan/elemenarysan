<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => getenv('DB_DSN','mysql:host=localhost;dbname=yii2basic'),
    'username' => getenv('DB_USERNAME','user'),
    'password' => getenv('DB_PASSWORD','password'),
    'charset' => getenv('DB_CHARSET','utf8'),

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
