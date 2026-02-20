<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'labvm',
    'name' => 'LabVM',
    'language' => 'it',
    'defaultRoute' => 'account/security/login',
    'homeUrl'=>array('site/index'),
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'account' => ['class' => 'app\modules\account\Module'],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'VowLR1kHJeaMWds2tuhCKpN6S1uxzQFl',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\account\models\User',
            'loginUrl' => ['account/security/login'],
            'identityCookie' => ['name' => '_identity-labvm', 'httpOnly' => true],
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            // uncomment if you want to cache RBAC items hierarchy
            // 'cache' => 'cache',
        ],
        'errorHandler' => [
            'errorAction' => 'error/view',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'formatter' => [
            'dateFormat' => 'dd/MM/yyyy',
            'datetimeFormat' => 'dd/MM/yyyy HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'EUR',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'assetManager' =>[
            'forceCopy' => true,
        ]

        /**
     * Usato per fare l'override del CSS di Bootstrap5
     */
        /*
        'assetManager' => [
            'forceCopy' => true,
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'sourcePath' => '@app/web/css/',
                    'css' => ['bootstrap.min.css'],
                ]
            ],
        ]
        */

    ],
    'params' => $params,
    /** Serve anche dopo l'aggiornamento? 
     * Fa l'override della classe legata al paginatore. 
     * Nella versione precedente di yii, il widget gridview non usava quello 
     * di bootstrap 5 ma il default del framework. 
     * Bisogna verificare se il problema è stato risolto.
     */
    'container' => [
        'definitions' => [
            \yii\widgets\LinkPager::class => \yii\bootstrap5\LinkPager::class,
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
