<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class HistoryController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new HistoryList;
        if (isset($_POST['HistoryList'])) {
            $model->attributes = $_POST['HistoryList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['history_01']) && !empty($session['history_01'])) {
                $criteria = $session['history_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionForm($index,$type = "")
    {
        $model = new HistoryForm($type);
        if(!$model->validateStaff($index,$type)){
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('contract','The employee has changed the information, please complete the change first'));
            $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
        }
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionDetail($index)
    {
        $model = new HistoryForm("view");
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $model->staff_status = 2;
            $this->render('detail',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new HistoryForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new HistoryForm('view');
        if(!$model->validateStaff($index,'view')){
            Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('contract','The employee has changed the information, please complete the change first'));
            $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
        }
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['HistoryForm'])) {
            $model = new HistoryForm($_POST['HistoryForm']['scenario']);
            $model->attributes = $_POST['HistoryForm'];
            $model->staff_status = 1;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('history/Form',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                $model->historyList = AuditHistoryForm::getStaffHistoryList($model->employee_id);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //變更要求審核
    public function actionAudit()
    {
        if (isset($_POST['HistoryForm'])) {
            $model = new HistoryForm($_POST['HistoryForm']['scenario']);
            $model->attributes = $_POST['HistoryForm'];
            $model->staff_status = 2;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('history/Form',array('index'=>$model->id,"type"=>"view")));
            } else {
                $message = CHtml::errorSummary($model);
                $model->historyList = AuditHistoryForm::getStaffHistoryList($model->employee_id);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    //變更合同
    public function actionFinish(){
        if (isset($_POST['HistoryForm'])) {
            $model = new HistoryForm("finish");
            $model->attributes = $_POST['HistoryForm'];
            $model->finish();

            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            $this->redirect(Yii::app()->createUrl('auditHistory/index'));
        }
    }

}