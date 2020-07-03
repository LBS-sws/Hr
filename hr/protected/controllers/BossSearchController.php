<?php

/**
 * Created by PhpStorm.
 * User: 老總年度考核
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class BossSearchController extends Controller
{
	public $function_id='BA02';

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
                'actions'=>array('edit'),
                'expression'=>array('BossSearchController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('BossSearchController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('back'),
                'expression'=>array('BossSearchController','allowBack'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('BA02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('BA02');
    }

    public static function allowBack() {
        return Yii::app()->user->validFunction('ZR16');
    }

    public function actionIndex($pageNum=0){
        $model = new BossSearchList;
        if (isset($_POST['BossSearchList'])) {
            $model->attributes = $_POST['BossSearchList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['bossSearch_01']) && !empty($session['bossSearch_01'])) {
                $criteria = $session['bossSearch_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new BossSearchForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new BossSearchForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionBack()
    {
        $model = new BossSearchForm('back');
        $model->attributes = $_POST['BossSearchForm'];
        if ($model->validate()) {
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','finish to send back'));
            $this->redirect(Yii::app()->createUrl('bossSearch/index'));
        } else {
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('bossSearch/edit',array('index'=>$model->id)));
        }
    }
}