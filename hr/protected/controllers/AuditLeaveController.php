<?php

/**
 * Created by PhpStorm.
 * User: 請假審核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditLeaveController extends Controller
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
                'expression'=>array('AuditLeaveController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('AuditLeaveController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        if(array_key_exists("only",$_GET) && $_GET["only"] == 2){
            return Yii::app()->user->validRWFunction('ZG05');
        }else{
            return Yii::app()->user->validRWFunction('ZE06');
        }
    }

    public static function allowReadOnly() {
        if(array_key_exists("only",$_GET) && $_GET["only"] == 2){
            return Yii::app()->user->validFunction('ZG05');
        }else{
            return Yii::app()->user->validFunction('ZE06');
        }
    }

    public function actionIndex($pageNum=0,$only = 1){
        $model = new AuditLeaveList;
        $model->only = $only;
        if (isset($_POST['AuditLeaveList'])) {
            $model->attributes = $_POST['AuditLeaveList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditleave_01']) && !empty($session['auditleave_01'])) {
                $criteria = $session['auditleave_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index,$only = 1)
    {
        $model = new AuditLeaveForm('edit');
        $model->only = $only;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$only = 1)
    {
        $model = new AuditLeaveForm('view');
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
        if (isset($_POST['AuditLeaveForm'])) {
            $model = new AuditLeaveForm('audit');
            $model->attributes = $_POST['AuditLeaveForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditLeave/index',array('only'=>$model->only)));
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
        if (isset($_POST['AuditLeaveForm'])) {
            $model = new AuditLeaveForm('reject');
            $model->attributes = $_POST['AuditLeaveForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditLeave/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('auditLeave/edit',array('index'=>$model->id)));
            }
        }
    }

    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_employee_leave where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'LeaveForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }
}