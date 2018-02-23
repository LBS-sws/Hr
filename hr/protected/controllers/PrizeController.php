<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class PrizeController extends Controller
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
                'actions'=>array('new','edit','delete','save','audit','uploadImg'),
                'expression'=>array('PrizeController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PrizeController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZE08');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZE08');
    }

    public static function allowWrite() {
        return true;
    }

    public function actionIndex($pageNum=0){
        $model = new PrizeList;
        if (isset($_POST['PrizeList'])) {
            $model->attributes = $_POST['PrizeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['prize_01']) && !empty($session['prize_01'])) {
                $criteria = $session['prize_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new PrizeForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new PrizeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new PrizeForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['PrizeForm'])) {
            $model = new PrizeForm($_POST['PrizeForm']['scenario']);
            $model->attributes = $_POST['PrizeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('prize/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

/*    public function actionCopy()
    {
        $model = new PrizeForm('new');
        if (isset($_POST['PrizeForm'])) {
            $model->attributes = $_POST['PrizeForm'];
            $this->render('form',array('model'=>$model,));
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }*/

    public function actionAudit()
    {
        if (isset($_POST['PrizeForm'])) {
            $model = new PrizeForm($_POST['PrizeForm']['scenario']);
            $model->attributes = $_POST['PrizeForm'];
            $model->audit = true;
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('prize/edit',array('index'=>$model->id)));
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
        $model = new PrizeForm('delete');
        if (isset($_POST['PrizeForm'])) {
            $model->attributes = $_POST['PrizeForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('assess/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is already in use'));
                $this->redirect(Yii::app()->createUrl('assess/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳圖片
    public function actionUploadImg(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new UploadImgForm();
            $img = CUploadedFile::getInstance($model,'file');
            $city = Yii::app()->user->city();
            $path =Yii::app()->basePath."/../upload/images/";
            if (!file_exists($path)){
                mkdir($path);
            }
            $path.=$city."/";
            if (!file_exists($path)){
                mkdir($path);
            }
            $url = "upload/images/".$city."/".date("YmdHsi").".".$img->getExtensionName();
            $model->file = $img->getName();
            if ($model->file && $model->validate()) {
                $img->saveAs($url);
                //$url = "/".Yii::app()->params['systemId']."/".$url;
                $url = "../../".$url;
                echo CJSON::encode(array('status'=>1,'data'=>$url));
            }else{
                echo CJSON::encode(array('status'=>0,'error'=>$model->getErrors()));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('prize/index'));
        }
    }

}