<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class HolidayConController extends Controller
{

    public function actionIndex($pageNum=0,$type=0){
        $model = new HolidayConList;
        $model->type=$type;
        if (isset($_POST['HolidayConList'])) {
            $model->attributes = $_POST['HolidayConList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['holidaycon_01']) && !empty($session['holidaycon_01'])) {
                $criteria = $session['holidaycon_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew($type=0)
    {
        $model = new HolidayConForm('new');
        $model->type = $type;
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new HolidayConForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new HolidayConForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['HolidayConForm'])) {
            $model = new HolidayConForm($_POST['HolidayConForm']['scenario']);
            $model->attributes = $_POST['HolidayConForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('holidayCon/edit',array('index'=>$model->id,'type'=>$model->type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    //刪除職位
    public function actionDelete(){
        $model = new HolidayConForm('delete');
        if (isset($_POST['HolidayConForm'])) {
            $model->attributes = $_POST['HolidayConForm'];
            if($model->validateDelete()){
                $model->saveData();
                $this->redirect(Yii::app()->createUrl('holidayCon/index'),array("type",$model->type));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The holidayCon has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('holidayCon/edit',array('index'=>$model->id,'type'=>$model->type)));
            }
        }
    }
}