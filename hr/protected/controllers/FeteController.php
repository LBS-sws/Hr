<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class FeteController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new FeteList;
        if (isset($_POST['FeteList'])) {
            $model->attributes = $_POST['FeteList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['fete_01']) && !empty($session['fete_01'])) {
                $criteria = $session['fete_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new FeteForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new FeteForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new FeteForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['FeteForm'])) {
            $model = new FeteForm($_POST['FeteForm']['scenario']);
            $model->attributes = $_POST['FeteForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('fete/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new FeteForm('delete');
        if (isset($_POST['FeteForm'])) {
            $model->attributes = $_POST['FeteForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('fete/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('fete/edit',array('index'=>$model->id)));
            }
        }
    }

    //時間運算
    public function actionAddDate(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $startDate = $_POST['startDate'];
            $day = $_POST['day'];
            if(empty($startDate)||empty($day)){
                echo CJSON::encode(array("status"=>0,"message"=>"時間不能為空"));
                return true;
            }
            if(!is_numeric($day)){
                echo CJSON::encode(array("status"=>0,"message"=>"天數只能為數字"));
                return true;
            }
            if(intval($day) != $day){
                echo CJSON::encode(array("status"=>0,"message"=>"天數只能為正整數"));
                return true;
            }
            if($day < 2){
                echo CJSON::encode(array("status"=>0,"message"=>"天數必須大於1天"));
                return true;
            }
            $day--;
            $lastDate = date('Y/m/d', strtotime("$startDate +$day day"));
            echo CJSON::encode(array("status"=>1,"lastDate"=>$lastDate));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }
}