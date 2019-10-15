<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ReviewAllotController extends Controller
{
	public $function_id='RE01';

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
                'actions'=>array('new','edit','draft','save'),
                'expression'=>array('ReviewAllotController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('ReviewAllotController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('RE01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('RE01');
    }

    public function actionIndex($pageNum=0){
        $model = new ReviewAllotList();
        if (isset($_POST['ReviewAllotList'])) {
            $model->attributes = $_POST['ReviewAllotList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['reviewAllot_01']) && !empty($session['reviewAllot_01'])) {
                $criteria = $session['reviewAllot_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index,$year,$year_type)
    {
        $model = new ReviewAllotForm('edit');
        if (!$model->retrieveData($index,$year,$year_type)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ReviewAllotForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['ReviewAllotForm'])) {
            $model = new ReviewAllotForm($_POST['ReviewAllotForm']['scenario']);
            $model->attributes = $_POST['ReviewAllotForm'];
            $model->status_type = 1;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
            } else {
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionDraft()
    {
        if (isset($_POST['ReviewAllotForm'])) {
            $model = new ReviewAllotForm($_POST['ReviewAllotForm']['scenario']);
            $model->attributes = $_POST['ReviewAllotForm'];
            $model->status_type = 4;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->employee_id,'year'=>$model->year,'year_type'=>$model->year_type)));
            } else {
                $model->status_type = 0;
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除
    public function actionDelete(){
        $model = new ReviewAllotForm('delete');
        if (isset($_POST['ReviewAllotForm'])) {
            $model->attributes = $_POST['ReviewAllotForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('reviewAllot/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                //$this->redirect(Yii::app()->createUrl('reviewAllot/edit',array('index'=>$model->id)));
                $this->render('form',array('model'=>$model,));
            }
        }
    }

}