<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditHistoryController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new AuditHistoryList;
        if (isset($_POST['AuditHistoryList'])) {
            $model->attributes = $_POST['AuditHistoryList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['audithistory_01']) && !empty($session['audithistory_01'])) {
                $criteria = $session['audithistory_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AuditHistoryForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new AuditHistoryForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['AuditHistoryForm'])) {
            $model = new AuditHistoryForm($_POST['AuditHistoryForm']['scenario']);
            $model->attributes = $_POST['AuditHistoryForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('AuditHistory/Form',array('index'=>$model->id,"type"=>"view")));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    //審核通過
    public function actionAudit()
    {
        if (isset($_POST['AuditHistoryForm'])) {
            $model = new AuditHistoryForm('audit');
            $model->attributes = $_POST['AuditHistoryForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('AuditHistory/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                $model->historyList = AuditHistoryForm::getStaffHistoryList($model->employee_id);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    //審核不通過
    public function actionReject()
    {
        if (isset($_POST['AuditHistoryForm'])) {
            $model = new AuditHistoryForm('reject');
            $model->attributes = $_POST['AuditHistoryForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('AuditHistory/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                $model->historyList = AuditHistoryForm::getStaffHistoryList($model->employee_id);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('AuditHistory/edit',array('index'=>$model->id)));
            }
        }
    }
}