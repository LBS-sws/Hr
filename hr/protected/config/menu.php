<?php

return array(
	'Data Entry'=>array(
		'access'=>'ZA',
		'items'=>array(
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
    //員工模塊
	'Employee'=>array(
		'access'=>'ZE',
		'items'=>array(
            //員工錄入
            'Employee Info'=>array(
                'access'=>'ZE01',
                'url'=>'/employ/index',
            ),
            //審核資料
			'Employee Audit'=>array(
				'access'=>'ZE02',
				'url'=>'/audit/index',
			),
            //員工列表
			'Employee List'=>array(
				'access'=>'ZE03',
				'url'=>'/employee/index',
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
