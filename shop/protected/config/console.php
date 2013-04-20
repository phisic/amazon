<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),
    'modules'=>array(
            #...
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
            #...
        ),
	// application components
	'components'=>array(
		'amazon' => array(
            'class'=>'ext.AmazonECS',
            'accessKey'=>'AKIAIDT5J2U4KPAIAARA',
            'secretKey'=>'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', 
            'country'=>'COM', 
            'associateTag'=>'3445-3149-2207',
        ),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=amazon',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
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
			),
		),
	),
    
    'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
        //Laptops node Id
        'node'=>565108,
	),
);