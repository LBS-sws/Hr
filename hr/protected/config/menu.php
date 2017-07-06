<?php

return array(
	'Data Entry'=>array(
		'access'=>'ZA',
		'items'=>array(
		    //員工資料
            'Employee Info'=>array(
                'access'=>'ZA03',
                'url'=>'/employee/index',
            ),
/* 員工資料（旧版本）
 * 			'Staff Info'=>array(
				'access'=>'ZA01',
				'url'=>'/staff/index',
			),*/
            'Company Info'=>array(
                'access'=>'ZA02',
                'url'=>'/company/index',
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
    //合同模塊
	'Contract'=>array(
		'access'=>'ZD',
		'items'=>array(
			'Contract Word'=>array(
				'access'=>'ZD01',
				'url'=>'/word/index',
			),
			'Contract List'=>array(
				'access'=>'ZD02',
				'url'=>'/contract/index',
			)
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
