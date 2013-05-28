<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	// preloading 'log' component
	'preload'=>array('log'),

	'modules'=>array(
		
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
        'urlManager' => array(
                    'urlFormat' => 'path',
                    'showScriptName' => 0,
            ),
        'stat'=>array('class'=>  'Statistics'),
        'part'=>array('class'=>  'Part'),
        'search' => array(
            'class' => 'application.components.DGSphinxSearch.DGSphinxSearch',
            'server' => '127.0.0.1',
            'port' => 3313,
            'maxQueryTime' => 3000,
            'enableProfiling'=>0,
            'enableResultTrace'=>0,
            'fieldWeights' => array(
                'name' => 10000,
                'keywords' => 100,
            ),
        ),
        'amazon' => array(
            'class'=>'ext.AmazonECS',
            'accessKey'=>'AKIAIDT5J2U4KPAIAARA',
            'secretKey'=>'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', 
            'country'=>'COM', 
            'associateTag'=>'laptoptop7com-20',
        ),
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=amazon',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '1',
			'charset' => 'utf8',
            'tablePrefix' => 'tbl_',
            'enableProfiling'=>true,
            'schemaCachingDuration' => 3600,
            'enableParamLogging'=>true,
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

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'murod9@gmail.com',
        'robotEmail'=>'laptoptop7@laptoptop7.com',
        //Laptops node Id
        'node'=>565108,
        'secret' => '99limon01962a7d10fd4a20156f8d02adea93d1',
        'domain'=>'laptoptop7.com',
        'category'=>'laptop',
        'searchPlace'=>'search for laptops, example: Macbook, Ultrabook',
        'GACode' => "<script type=\"text/javascript\">

                        var _gaq = _gaq || [];
                        _gaq.push(['_setAccount', 'UA-40874781-1']);
                        _gaq.push(['_setDomainName', 'laptoptop7.com']);
                        _gaq.push(['_trackPageview']);

                        (function() {
                          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                        })();

                      </script>",
        'menu'=> array(
                        'search/toppricedrops'=>'Top Price Drops Today',
                        'search/bestsellers'=>'Best Sellers',
                        'search/topreviewed'=>'Top Reviewed',
                        'search/newreleases'=>'New Releases',
                        'search/toppowerful'=>'Top Powerful and Gaming',
                        'search/all'=>'All Laptops',
                        'site/contact'=>'Contact us',
                    )
	),
    
);