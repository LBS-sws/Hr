<?php

/**
 * Created by PhpStorm.
 * User: 老總年度考核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class BossAuditController extends Controller
{
	public $function_id='BA03';

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
                'expression'=>array('BossAuditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('BossAuditController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('BA03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('BA03');
    }

    public function actionIndex($pageNum=0){
        $model = new BossAuditList;
        if (isset($_POST['BossAuditList'])) {
            $model->attributes = $_POST['BossAuditList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['bossAudit_01']) && !empty($session['bossAudit_01'])) {
                $criteria = $session['bossAudit_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new BossAuditForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new BossAuditForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('audit');
            $model->attributes = $_POST['BossAuditForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
            $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id)));
        }
    }

    public function actionReject()
    {
        if (isset($_POST['BossAuditForm'])) {
            $model = new BossAuditForm('reject');
            $model->attributes = $_POST['BossAuditForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Request Denied'));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
            }
            $this->redirect(Yii::app()->createUrl('bossAudit/edit',array('index'=>$model->id)));
        }
    }
}