<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
            'db' => 
            array (
              'connectionString' => 'mysql:host=olympic.colorado.edu;dbname=places_staging',
              'emulatePrepare' => true,
              'username' => 'places',
              'password' => 'equalizations democratizations agrarianize remoralizing',
              'charset' => 'utf8',
              'tablePrefix' => '',
            ),
		),
	)
);
