<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Amazon Laptops',
    'theme' => 'bootstrap',
	// preloading 'log' component
	'preload'=>array('log','bootstrap'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.modules.user.models.*',
        'application.modules.user.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'111',
            'generatorPaths' => array(
                'bootstrap.gii'
            ),
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
        'user'=>array(
                # encrypting method (php hash function)
                'hash' => 'md5',

                # send activation email
                'sendActivationMail' => true,

                # allow access for non-activated users
                'loginNotActiv' => false,

                # activate user on registration (only sendActivationMail = false)
                'activeAfterRegister' => false,

                # automatically login from registration
                'autoLogin' => true,

                # registration path
                'registrationUrl' => array('/user/registration'),

                # recovery password path
                'recoveryUrl' => array('/user/recovery'),

                # login form path
                'loginUrl' => array('/user/login'),

                # page after login
                'returnUrl' => array('/user/profile'),

                # page after logout
                'returnLogoutUrl' => array('/user/login'),
            ),
	),

	// application components
	'components'=>array(
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => true,
        ),
        'amazon' => array(
            'class'=>'ext.AmazonECS',
            'accessKey'=>'AKIAIDT5J2U4KPAIAARA',
            'secretKey'=>'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', 
            'country'=>'COM', 
            'associateTag'=>'3445-3149-2207',
        ),
		'user'=>array(
                // enable cookie-based authentication
                'class' => 'WebUser',
                'allowAutoLogin'=>true,
                'loginUrl' => array('/user/login'),
        ),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName' => 0,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=amazon',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
            'tablePrefix' => 'tbl_',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
        'cache'=>array(
            'class'=>  'CFileCache',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
        //Laptops node Id
        'node'=>565108,
	),
    
);