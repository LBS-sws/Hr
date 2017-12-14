<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class WorkController extends Controller
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
                'actions'=>array('new','edit','delete','save','audit'),
                'expression'=>array('WorkController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('WorkController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('addDate'),
                'expression'=>array('WorkController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validFunction('ZA05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZA05');
    }

    public static function allowWrite() {
        return true;
    }

    public function actionIndex($pageNum=0){
        $model = new WorkList;
        if($model->validateEmployee()){
            if (isset($_POST['WorkList'])) {
                $model->attributes = $_POST['WorkList'];
            } else {
                $session = Yii::app()->session;
                if (isset($session['work_01']) && !empty($session['work_01'])) {
                    $criteria = $session['work_01'];
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


    public function actionNew()
    {
        $model = new WorkForm('new');
        $employeeName = WorkList::getEmployeeName();
        if(empty($employeeName)){
            throw new CHttpException(404,Yii::t("contract",'The account has no binding staff, please contact the administrator'));
        }else{
            $model->employee_id = $employeeName;
        }
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new WorkForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new WorkForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['WorkForm'])) {
            $model = new WorkForm($_POST['WorkForm']['scenario']);
            $model->attributes = $_POST['WorkForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['WorkForm'])) {
            $model = new WorkForm($_POST['WorkForm']['scenario']);
            $model->attributes = $_POST['WorkForm'];
            $model->audit = true;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->audit = false;
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new WorkForm('delete');
        if (isset($_POST['WorkForm'])) {
            $model->attributes = $_POST['WorkForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('work/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('work/edit',array('index'=>$model->id)));
            }
        }
    }


    //時間運算
    public function actionAddDate(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $startDate = $_POST['startDate'];
            $day = $_POST['day'];
            $type = $_POST['type'];
            $str = $type == 2?"天数":"小時";
            if(empty($startDate)||empty($day)){
                echo CJSON::encode(array("status"=>0,"message"=>"時間不能為空"));
                return true;
            }
            if(!is_numeric($day)){
                echo CJSON::encode(array("status"=>0,"message"=>$str."只能為數字"));
                return true;
            }
            if(intval($day) != $day){
                echo CJSON::encode(array("status"=>0,"message"=>$str."只能為正整數"));
                return true;
            }
            if($day < 2){
                echo CJSON::encode(array("status"=>0,"message"=>$str."必須大於1"));
                return true;
            }
            if($type == 2){
                $day--;
                $lastDate = date('Y/m/d', strtotime("$startDate +$day day"));
            }else{
                $lastDate = date('Y/m/d H:i', strtotime("$startDate +$day hours"));
            }
            echo CJSON::encode(array("status"=>1,"lastDate"=>$lastDate));
        }else{
            $this->redirect(Yii::app()->createUrl(''));
        }
    }
}