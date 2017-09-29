<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class HolidayController extends Controller
{

    public function actionIndex($pageNum=0,$type=0,$only=0){
        $model = new HolidayList;
        $model->type=$type;
        $model->only=$only;
        if($model->validateEmployee()){
            if (isset($_POST['HolidayList'])) {
                $model->attributes = $_POST['HolidayList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['holiday_01']) && !empty($session['holiday_01'])) {
                    $criteria = $session['holiday_01'];
                    $model->setCriteria($criteria);
                }
            }
            $model->determinePageNum($pageNum);
            $model->retrieveDataByPage($model->pageNum);
            $this->render('index',array('model'=>$model));
        }else{
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }
    }


    public function actionNew($type=0,$only=0)
    {
        $model = new HolidayForm('new');
        $model->type=$type;
        $model->only=$only;
        if($model->validateEmployee()){
            $this->render('form',array('model'=>$model,));
        }else{
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }
    }

    public function actionEdit($index,$type=0,$only=0)
    {
        $model = new HolidayForm('edit');
        $model->type=$type;
        $model->only=$only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$type=0,$only=0)
    {
        $model = new HolidayForm('view');
        $model->type=$type;
        $model->only=$only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['HolidayForm'])) {
            $model = new HolidayForm($_POST['HolidayForm']['scenario']);
            $model->attributes = $_POST['HolidayForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('holiday/edit',array('index'=>$model->id,'only'=>$model->only,'type'=>$model->type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['HolidayForm'])) {
            $model = new HolidayForm($_POST['HolidayForm']['scenario']);
            $model->attributes = $_POST['HolidayForm'];
            if ($model->validate()) {
                $model->status = 1;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('holiday/edit',array('index'=>$model->id,'only'=>$model->only,'type'=>$model->type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionFinish()
    {
        if (isset($_POST['HolidayForm'])) {
            $model = new HolidayForm($_POST['HolidayForm']['scenario']);
            $model->attributes = $_POST['HolidayForm'];
            $model->finishHoliday();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            $this->redirect(Yii::app()->createUrl('holiday/edit',array('index'=>$model->id,'only'=>$model->only,'type'=>$model->type)));
        }
    }

    //刪除
    public function actionDelete(){
        $model = new HolidayForm('delete');
        if (isset($_POST['HolidayForm'])) {
            $model->attributes = $_POST['HolidayForm'];
            if($model->validateDelete()){
                $model->saveData();
                $this->redirect(Yii::app()->createUrl('holiday/index',array('index'=>$model->id,'type'=>$model->type)));
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Error:not find error'));
                $this->redirect(Yii::app()->createUrl('holiday/edit',array('index'=>$model->id,'only'=>$model->only,'type'=>$model->type)));
            }
        }
    }
}