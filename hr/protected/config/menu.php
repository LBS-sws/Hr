<?php

return array(
	'Data Entry'=>array(
		'access'=>'ZA',
		'items'=>array(
            'Company Info'=>array(
                'access'=>'ZA02',
                'url'=>'/company/index',
            ),
            'Wages Config'=>array(
                'access'=>'ZA03',
                'url'=>'/wages/index',
            ),
            'Wages Make'=>array(
                'access'=>'ZA04',
                'url'=>'/makeWages/index',
            ),
            'Only Holiday List'=>array(
                'access'=>'ZA05',
                'url'=>'/holiday/index',
            ),
            'Only Work List'=>array(
                'access'=>'ZA06',
                'url'=>'/holiday/index?type=1',
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
            //在職員工列表
			'Job Employee List'=>array(
				'access'=>'ZE03',
				'url'=>'/employee/index',
			),
            //離職員工列表
            'Departure Employee List'=>array(
                'access'=>'ZE02',
                'url'=>'/departure/index',
            ),
            //員工變更列表
			'Employee Update List'=>array(
				'access'=>'ZE04',
				'url'=>'/history/index',
			),
            'All Holiday List'=>array(
                'access'=>'ZE05',
                'url'=>'/holiday/index?only=1',
            ),
            'All Work List'=>array(
                'access'=>'ZE06',
                'url'=>'/holiday/index?only=1&type=1',
            ),
		),
	),
    //審核模塊
	'Audit'=>array(
		'access'=>'ZG',
		'items'=>array(
            //入職審核
            'Employee Audit'=>array(
                'access'=>'ZG01',
                'url'=>'/audit/index',
            ),
            //變更審核
			'Employee Update Audit'=>array(
				'access'=>'ZG02',
				'url'=>'/auditHistory/index',
			),
            //工資單審核
			'Wages Audit'=>array(
				'access'=>'ZG03',
				'url'=>'/auditWages/index',
			),
            'Holiday Audit'=>array(
                'access'=>'ZG04',
                //'url'=>'/employer/index',
                'url'=>'/auditHoliday/index',
            ),
            'Work Audit'=>array(
                'access'=>'ZG05',
                'url'=>'/auditHoliday/index?type=1',
            ),
		),
	),
	'System Setting'=>array(
		'access'=>'ZC',
		'items'=>array(
			'Department'=>array(
				'access'=>'ZC01',
				//'url'=>'/employer/index',
				'url'=>'/dept/index',
			),
			'Leader'=>array(
				'access'=>'ZC02',
				'url'=>'/dept/index?type=1',
			),
			'Holiday Config'=>array(
				'access'=>'ZC03',
				//'url'=>'/employer/index',
				'url'=>'/holidayCon/index',
			),
			'Work Config'=>array(
				'access'=>'ZC04',
				'url'=>'/holidayCon/index?type=1',
			),
			'employee binding account'=>array(
				'access'=>'ZC05',
				'url'=>'/binding/index',
			),
		),
	),
);
