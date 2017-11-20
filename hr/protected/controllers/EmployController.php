<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class EmployController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new EmployList;
        if (isset($_POST['EmployList'])) {
            $model->attributes = $_POST['EmployList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['employ_01']) && !empty($session['employ_01'])) {
                $criteria = $session['employ_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new EmployForm('new');
        $model->entry_time = $model->test_start_time = date("Y/m/d");
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new EmployForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new EmployForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['EmployForm'])) {
            $model = new EmployForm($_POST['EmployForm']['scenario']);
            $model->attributes = $_POST['EmployForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('employ/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['EmployForm'])) {
            $model = new EmployForm('audit');
            $model->attributes = $_POST['EmployForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('employ/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    public function actionFinish()
    {
        if (isset($_POST['EmployForm'])) {
            $data = $_POST['EmployForm'];
            $uid = Yii::app()->user->id;
            Yii::app()->db->createCommand()->update('hr_employee', array(
                'jj_card'=>$data['jj_card'],
                'sb_card'=>$data['sb_card'],
                'ld_card'=>$data['ld_card'],
                'staff_status'=>0,
                'staff_old_status'=>0,
            ), 'id=:id and staff_status=4', array(':id'=>$data['id']));
            //記錄
            Yii::app()->db->createCommand()->insert('hr_employee_history', array(
                "employee_id"=>$data['id'],
                "status"=>"finish",
                "lcu"=>$uid,
                "lcd"=>date('Y-m-d H:i:s'),
            ));
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            $this->redirect(Yii::app()->createUrl('employ/index'));
        }
    }

    //生成合同
    public function actionGenerate($index=0){
        if (empty($index) || !is_numeric($index)){
            $this->redirect(Yii::app()->createUrl('employ/index'));
        }else{
            $bool = EmployeeForm::updateEmployeeWord($index);
            if (!$bool){
                $this->redirect(Yii::app()->createUrl('employ/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Contract formation success'));
                $this->redirect(Yii::app()->createUrl('employ/edit',array('index'=>$index)));
            }
        }
    }

    //刪除草稿
    public function actionDelete(){
        $model = new EmployForm('delete');
        if (isset($_POST['EmployForm'])) {
            $model->attributes = $_POST['EmployForm'];
            if($model->validateDelete()){
                $model->saveData();
                $this->redirect(Yii::app()->createUrl('employ/index'));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The dept has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('employ/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳圖片
    public function actionUploadImg(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new UploadImgForm();
            $img = CUploadedFile::getInstance($model,'file');
            $city = Yii::app()->user->city();
            $path =Yii::app()->basePath."/../upload/images/";
            if (!file_exists($path)){
                mkdir($path);
            }
            $path.=$city."/";
            if (!file_exists($path)){
                mkdir($path);
            }
            $url = "upload/images/".$city."/".date("YmdHsi").".".$img->getExtensionName();
            $model->file = $img->getName();
            if ($model->file && $model->validate()) {
                $img->saveAs($url);
                //$url = "/".Yii::app()->params['systemId']."/".$url;
                $url = "../../".$url;
                echo CJSON::encode(array('status'=>1,'data'=>$url));
            }else{
                echo CJSON::encode(array('status'=>0,'error'=>$model->getErrors()));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('employ/index'));
        }
    }

    //時間運算
    public function actionAddDate(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $date = $_POST['dateTime'];
            $month = $_POST['month'];
            $lastDate = date('Y-m-d', strtotime("$date +$month month"));
            $oldMonth = date("m",strtotime($date));
            $newMonth = date("m",strtotime($lastDate));
            if($newMonth - $oldMonth > $month){
                $oldYear = date("Y",strtotime($date));
                $newMonth = intval($oldMonth)+$month;
                if($newMonth > 12){
                    $oldYear = intval($oldYear)+1;
                    $newMonth-=12;
                }
                $BeginDate = $oldYear."-".$newMonth."-01";
                $lastDate = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
            }
            echo CJSON::encode($lastDate);
        }else{
            $this->redirect(Yii::app()->createUrl('employ/index'));
        }
    }

    //上傳附件
    public function actionAttachmentUp(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new UploadFileForm();
            $file = CUploadedFile::getInstance($model,'file');
            $city = Yii::app()->user->city();
            $path =Yii::app()->basePath."/../upload/";
            if (!file_exists($path)){
                mkdir($path);
            }
            $path =Yii::app()->basePath."/../upload/attachment/";
            if (!file_exists($path)){
                mkdir($path);
            }
            $path.=$city."/";
            if (!file_exists($path)){
                mkdir($path);
            }
            if ($model->validate()) {
                $url = "upload/attachment/".$city."/".date("YmdHis").".".$file->getExtensionName();
                $model->path_url = $url;
                $model->type = $_POST["type"];
                $model->file_name = $file->getName();
                $file->saveAs($url);
                $model->saveData();
                echo CJSON::encode(array('status'=>1,'data'=>$model->getFileList()));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                echo CJSON::encode(array('status'=>0));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('employ/index'));
        }
    }
    //下載附件
    public function actionAttachmentDown($index){
        $model = new UploadFileForm();
        if(!$model->getAttachmentList($index)){
            throw new CHttpException(404,'The requested page does not exist.');
        }else{
            $file = Yii::app()->basePath."/../".$model->path_url;
            // To prevent corrupted zip - Percy
            ob_clean();
            ob_end_flush();
            //
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename='.$model->file_name);
            header("Content-Length: ". filesize($file));
            readfile($file);
        }
    }
    //刪除附件
    public function actionAttachmentDelete(){
        $model = new UploadFileForm();
    }
}