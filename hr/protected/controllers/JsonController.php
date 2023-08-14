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
			exit;
		}
        if($_GET['ac']=='2')
		{
			// entry_time入 職時間
			// staff_status 員工狀態：0（已經入職）
			// position	职位
			$list = Yii::app()->db->createCommand('SELECT id, name, code, sex, city, phone, entry_time, staff_status, position FROM hr_employee WHERE 1=1 ORDER BY id ASC ')->queryAll();
			
			//echo count($list);
			echo json_encode($list);
			exit;
		}
        if($_GET['ac']=='3'){
            echo "<pre>";
            $from =  'security'.Yii::app()->params['envSuffix'].'.sec_user_access';
            $rows = Yii::app()->db->createCommand()->select("*")->from($from)->where(array('like', 'username', "Grace"))->queryAll();
            print_r($rows);

            $fromuser =  'security'.Yii::app()->params['envSuffix'].'.sec_user';
            $row = Yii::app()->db->createCommand()->select("*")->from($fromuser)->where(array('like', 'username', "Grace"))->queryAll();
            print_r($row);
        }
		if($_GET['ac']=='company'){
			echo "<pre>";
			$from =  'docmandev'.Yii::app()->params['envSuffix'].'.dm_doc_type';
            $rows = Yii::app()->db->createCommand()->select("*")->from($from)->queryAll();
            print_r($rows);
			
			$from1 =  'docmandev'.Yii::app()->params['envSuffix'].'.dm_file';
            $rows1 = Yii::app()->db->createCommand()->select("*")->from($from1)->queryAll();
            print_r($rows1);
			
			$from2 =  'docmandev'.Yii::app()->params['envSuffix'].'.dm_master';
            $rows2 = Yii::app()->db->createCommand()->select("*")->from($from2)->queryAll();
            print_r($rows2);
		}
    }
	
}
