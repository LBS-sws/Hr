<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class SalesReviewController extends Controller
{
	public $function_id='SR02';

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
                'actions'=>array('new','edit','draft','save','undo','back','fileupload','fileRemove','fileRemove'),
                'expression'=>array('SalesReviewController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload'),
                'expression'=>array('SalesReviewController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SR02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SR02');
    }

    public function actionIndex($pageNum=0){
        $model = new SalesReviewList();
        if (isset($_POST['SalesReviewList'])) {
            $model->attributes = $_POST['SalesReviewList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['salesReview_01']) && !empty($session['salesReview_01'])) {
                $criteria = $session['salesReview_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionView($index,$year,$year_type,$city='')
    {
        $model = new SalesReviewForm('view');
        if (empty($year)||empty($year_type)||!$model->retrieveData($index,$year,$year_type,$city)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }
}