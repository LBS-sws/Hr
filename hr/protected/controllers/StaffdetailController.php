<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class StaffdetailController extends Controller
{

    public function actionIndex($pageNum=0,$index=0){
        $model = new StaffList;
        if (isset($_POST['StaffList'])) {
            $model->attributes = $_POST['StaffList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['criteria_a07']) && !empty($session['criteria_a07'])) {
                $criteria = $session['criteria_a07'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDetailDataByPage($model->pageNum,$index);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new StaffForm('detailedit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['StaffForm'])) {
            $model = new StaffForm($_POST['StaffForm']['scenario']);
            $model->attributes = $_POST['StaffForm'];
            if ($model->validate()) {
                $model->saveData();
                $model->scenario = 'edit';
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('staffdetail/index',array("index"=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    public function actionDelete()
    {
        $id = $_GET["index"];
        $model = new StaffForm('delete');
        if (isset($_POST['StaffForm'])) {
            $model->attributes = $_POST['StaffForm'];
            $id = $model->name;
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('staffdetail/index',array("index"=>$id)));
    }

}