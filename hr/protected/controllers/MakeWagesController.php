<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class MakeWagesController extends Controller
{

    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('edit','finish','save','audit'),
                'expression'=>array('MakeWagesController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('MakeWagesController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validFunction('ZA04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZA04');
    }

    public function actionIndex($pageNum=0){
        $model = new MakeWagesList;
        if (isset($_POST['MakeWagesList'])) {
            $model->attributes = $_POST['MakeWagesList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['makewages_01']) && !empty($session['makewages_01'])) {
                $criteria = $session['makewages_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new MakeWagesForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new MakeWagesForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['MakeWagesForm'])) {
            $model = new MakeWagesForm($_POST['MakeWagesForm']['scenario']);
            $model->attributes = $_POST['MakeWagesForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('makeWages/edit',array('index'=>$model->employee_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->historyList = MakeWagesForm::getHistoryList($model->employee_id);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['MakeWagesForm'])) {
            $scenario = $_POST['MakeWagesForm']['scenario'];
            $model = new MakeWagesForm($scenario);
            $model->attributes = $_POST['MakeWagesForm'];
            if ($model->validate()) {
                $model->audit = 1;
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done and Submit to Sent Notification'));
                $this->redirect(Yii::app()->createUrl('makeWages/edit',array('index'=>$model->employee_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->historyList = MakeWagesForm::getHistoryList($model->employee_id);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    public function actionFinish($index){
        $rs = MakeWagesForm::finishWages($index);
        if(!empty($rs)){
            $this->redirect(Yii::app()->createUrl('makeWages/edit',array('index'=>$rs)));
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('dialog','Error:404 not find'));
            $this->redirect(Yii::app()->createUrl('makeWages/index'));
        }
    }
}