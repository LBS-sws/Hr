<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class AuditHolidayController extends Controller
{

    public function actionIndex($pageNum=0,$type=0){
        $model = new AuditHolidayList;
        $model->type = $type;
        if (isset($_POST['AuditHolidayList'])) {
            $model->attributes = $_POST['AuditHolidayList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['auditholiday_01']) && !empty($session['auditholiday_01'])) {
                $criteria = $session['auditholiday_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index,$type=0)
    {
        $model = new AuditHolidayForm('edit');
        $model->type = $type;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$type=0)
    {
        $model = new AuditHolidayForm('view');
        $model->type = $type;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionReject()
    {
        if (isset($_POST['AuditHolidayForm'])) {
            $model = new AuditHolidayForm("reject");
            $model->attributes = $_POST['AuditHolidayForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditHoliday/edit',array('index'=>$model->id,'type'=>$model->type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['AuditHolidayForm'])) {
            $model = new AuditHolidayForm("audit");
            $model->attributes = $_POST['AuditHolidayForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('auditHoliday/edit',array('index'=>$model->id,'type'=>$model->type)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
}