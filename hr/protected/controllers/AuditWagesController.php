<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditWagesController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new AuditWagesList;
        if (isset($_POST['AuditWagesList'])) {
            $model->attributes = $_POST['AuditWagesList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditwages_01']) && !empty($session['auditwages_01'])) {
                $criteria = $session['auditwages_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new AuditWagesForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new AuditWagesForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionReject()
    {
        if (isset($_POST['AuditWagesForm'])) {
            $model = new AuditWagesForm("reject");
            $model->attributes = $_POST['AuditWagesForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditWages/edit',array('index'=>$model->id)));
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
        if (isset($_POST['AuditWagesForm'])) {
            $model = new AuditWagesForm("audit");
            $model->attributes = $_POST['AuditWagesForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditWages/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->historyList = MakeWagesForm::getHistoryList($model->employee_id);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
}