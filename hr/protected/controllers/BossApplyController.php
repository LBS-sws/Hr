<?php

/**
 * Created by PhpStorm.
 * User: 老總年度考核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class BossApplyController extends Controller
{
	public $function_id='BA01';

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
                'actions'=>array('edit','add','new','save','audit','ajaxPlanYear'),
                'expression'=>array('BossApplyController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('BossApplyController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('BA01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('BA01');
    }

    public function actionIndex($pageNum=0){
        $model = new BossApplyList;
        if($model->validateEmployee()){
            //該賬號已綁定員工
            if (isset($_POST['BossApplyList'])) {
                $model->attributes = $_POST['BossApplyList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['bossApply_01']) && !empty($session['bossApply_01'])) {
                    $criteria = $session['bossApply_01'];
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

    public function actionAdd()
    {
        $model = new BossApplyForm('add');
        if (!$model->validateEmployee()) {
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        } else {
            $this->render('add',array('model'=>$model,));
        }
    }

    public function actionNew($year)
    {
        $model = new BossApplyForm('new');
        if (!$model->validateEmployee()) {
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        } else {
            $model->audit_year = $year;
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new BossApplyForm('edit');
        if (!$model->validateEmployee()) {
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        } else {
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionView($index)
    {
        $model = new BossApplyForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    { //草稿
        if (isset($_POST['BossApplyForm'])) {
            $model = new BossApplyForm($_POST['BossApplyForm']['scenario']);
            $model->attributes = $_POST['BossApplyForm'];
            $model->status_type = 0;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('bossApply/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }


    public function actionAudit()
    {
        if (isset($_POST['BossApplyForm'])) {
            $model = new BossApplyForm($_POST['BossApplyForm']['scenario']);
            $model->attributes = $_POST['BossApplyForm'];
            $model->status_type = 1;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('bossApply/edit',array('index'=>$model->id)));
            } else {
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAjaxPlanYear() {

        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new BossApplyForm();
            $data = $model->getAjaxPlanYear($_POST);
            echo CJSON::encode($data);
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }
}