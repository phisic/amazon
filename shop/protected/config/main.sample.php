<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
            'name' => 'Laptop Top7',
            'theme' => 'bootstrap',
            // preloading 'log' component
            'preload' => array('log', 'bootstrap'),
            'import' => array(
                'application.models.*',
                'application.components.*',
                'application.modules.user.models.*',
                'application.modules.user.components.*',
                'ext.eoauth.*',
                'ext.eoauth.lib.*',
                'ext.lightopenid.*',
                'ext.eauth.services.*',
            ),
            'components' => array(
                'bootstrap' => array(
                    'class' => 'ext.bootstrap.components.Bootstrap',
                    'responsiveCss' => true,
                ),
                'db' => array(
                    'password' => '',
                ),
                'user' => array(
                    // enable cookie-based authentication
                    'class' => 'WebUser',
                    'allowAutoLogin' => true,
                    'loginUrl' => array('/user/login'),
                ),
                'urlManager' => array(
                    'urlFormat' => 'path',
                    'showScriptName' => 0,
                    'rules' => array(
                        '<controller:\w+>/<id:\d+>' => '<controller>/view',
                        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                        '<controller:search>/<action:detail>/<asin:\w+>' => '<controller>/<action>',
                        '<controller:watch>/<action:remove>/<asin:[\w\-]+>' => '<controller>/<action>',
                    ),
                ),
                'errorHandler' => array(
                    // use 'site/error' action to display errors
                    'errorAction' => 'site/error',
                ),
                'loid' => array(
                    'class' => 'ext.lightopenid.loid',
                ),
                'eauth' => array(
                    'class' => 'ext.eauth.EAuth',
                    'popup' => false, // Use the popup window instead of redirecting.
                    'services' => array(// You can change the providers and their classes.
                        'google' => array(
                            'class' => 'GoogleOpenIDService',
                        ),
                        'facebook' => array(
                            'class' => 'FacebookOAuthService',
                            'client_id' => '366086316833196',
                            'client_secret' => '92dd47a74620ae3328e6f43f1b46c958'
                        ),
                        'twitter' => array(
                            'class' => 'TwitterOAuthService',
                            'key' => 'Ow9g3Vl0eCDH9ajAu4WkA',
                            'secret' => 'sRv12o8inFpDn9WgcxQYRb4ID74kpsnI7phKgUotr4',
                        ),
                    ),
                ),
            ),
        ));