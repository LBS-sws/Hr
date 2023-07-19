<?php

class JsonController extends Controller
{
    /**
     * type 		0部门1职位
     * city 		城市
     * dept_id 		部门id
     * dept_class 	职位类别
     */
    public function actionIndex()
    {
        if($_GET['user']!='admin'){
			exit;
		}
		if($_GET['ac']=='1')
		{
			// 部门
			$items = Yii::app()->db->createCommand('SELECT id,name FROM hr_dept WHERE type = 0 ORDER BY id ASC ')->queryAll();
	
			// 职位
			$list = Yii::app()->db->createCommand('SELECT id,name,dept_id FROM hr_dept WHERE type = 1 ORDER BY id ASC ')->queryAll();
	
			$data['items'] = $items;
			$data['list'] = $list;
	
			echo json_encode($data);
		}
        if($_GET['ac']=='2')
		{
			// entry_time入職時間
			$list = Yii::app()->db->createCommand('SELECT id,name,code,sex,phone,entry_time FROM hr_employee WHERE 1=1 ORDER BY id ASC ')->queryAll();
			echo "<pre>";
	        print_r($list[0]);
			// echo count($list);
			//echo json_encode($list);
		}
    }
	
}
