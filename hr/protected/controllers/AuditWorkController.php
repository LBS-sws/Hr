<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditWorkController extends Controller
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
                'actions'=>array('edit','reject','audit'),
                'expression'=>array('AuditWorkController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AuditWorkController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        if(array_key_exists("only",$_GET) && $_GET["only"] == 2){
            return Yii::app()->user->validRWFunction('ZG04');
        }else{
            return Yii::app()->user->validRWFunction('ZE05');
        }
    }

    public static function allowReadOnly() {
        if(array_key_exists("only",$_GET) && $_GET["only"] == 2){
            return Yii::app()->user->validFunction('ZG04');
        }else{
            return Yii::app()->user->validFunction('ZE05');
        }
    }

    public function actionIndex($pageNum=0,$only = 1){
        $model = new AuditWorkList;
        $model->only = $only;
        if (isset($_POST['AuditWorkList'])) {
            $model->attributes = $_POST['AuditWorkList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditwork_01']) && !empty($session['auditwork_01'])) {
                $criteria = $session['auditwork_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index,$only = 1)
    {
        $model = new AuditWorkForm('edit');
        $model->only = $only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$only = 1)
    {
        $model = new AuditWorkForm('view');
        $model->only = $only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    //審核通過
    public function actionAudit()
    {
        if (isset($_POST['AuditWorkForm'])) {
            $model = new AuditWorkForm('audit');
            $model->attributes = $_POST['AuditWorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditWork/index',array('only'=>$model->only)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    //審核不通過
    public function actionReject()
    {
        if (isset($_POST['AuditWorkForm'])) {
            $model = new AuditWorkForm('reject');
            $model->attributes = $_POST['AuditWorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditWork/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditWork/edit',array('index'=>$model->id)));
            }
        }
    }
}