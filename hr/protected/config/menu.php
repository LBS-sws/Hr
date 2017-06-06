<?php

return array(
	'Data Entry'=>array(
		'access'=>'ZA',
		'items'=>array(
			'Staff Info'=>array(
				'access'=>'ZA01',
				'url'=>'/staff/index',
			),
		),
	),
	'Report'=>array(
		'access'=>'ZB',
		'items'=>array(
			'Report Manager'=>array(
				'access'=>'ZB01',
				'url'=>'/queue/index',
			),
		),
	),
	'System Setting'=>array(
		'access'=>'ZC',
		'items'=>array(
			'Employer'=>array(
				'access'=>'ZC01',
				'url'=>'/employer/index',
			),
			'Department'=>array(
				'access'=>'ZC02',
				'url'=>'/dept/index',
			),
		),
	),
);
