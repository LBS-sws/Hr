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
            Yii::app()->db->createCommand()->update('hr_employee', array(
                'jj_card'=>$data['jj_card'],
                'sb_card'=>$data['sb_card'],
                'ld_card'=>$data['ld_card'],
                'staff_status'=>0,
            ), 'id=:id and staff_status=4', array(':id'=>$data['id']));
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
                $url = "/".Yii::app()->params['systemId']."/".$url;
                echo CJSON::encode(array('status'=>1,'data'=>$url));
            }else{
                echo CJSON::encode(array('status'=>0,'error'=>$model->getErrors()));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('employ/index'));
        }
    }
}