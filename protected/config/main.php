<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Places',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.models.graphics.*',
		'application.components.*',
	),

	'modules'=>array(	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
  			'showScriptName'=>false,
			'rules'=>array(
				'<id:\d+>'=>'site/view',
				'<action:\w+>/<id:\d+>'=>'site/<action>',
				'<action:\w+>'=>'site/<action>',
			),
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=places',
			'emulatePrepare' => true,
			'username' => 'places',
			'password' => '***REMOVED***',
			'charset' => 'utf8',
      		'tablePrefix' => '',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
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
		'googleAnalytics' => array(
			'class' => 'ext.GoogleAnalytics.TPGoogleAnalytics',
			'account' => 'UA-7054410-2',
			'autoRender' => true,
		),
	),
	

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        'LOCALAPP_JQUERY_VER'       => '1.10.2',
        'LOCALAPP_JQUERYUI_VER'     => '1.10.3',
        'LOCALAPP_SERVER'           => ($_SERVER["SERVER_NAME"]=="assettdev.colorado.edu")?"assettdev.colorado.edu":"compass.colorado.edu",
    ),
);