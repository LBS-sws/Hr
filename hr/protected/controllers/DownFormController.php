<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class DownFormController extends Controller
{

	public $function_id='ZD04';

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
                'actions'=>array('new','edit','delete','save'),
                'expression'=>array('DownFormController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','downfile'),
                'expression'=>array('DownFormController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('ZD04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('ZD04');
    }

    public function actionIndex($pageNum=0){
        $model = new DownFormList;
        if (isset($_POST['DownFormList'])) {
            $model->attributes = $_POST['DownFormList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['downForm_01']) && !empty($session['downForm_01'])) {
                $criteria = $session['downForm_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new DownFormForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionDownfile($index)
    {
        $url =DownFormForm::getDocxUrlToId($index);
        if($url){
            $type = end(explode(".",$url["docx_url"]));
            $file = Yii::app()->basePath."/../".$url["docx_url"];
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="'.$url["name"].".".$type.'"');
            header("Content-Length: ". filesize($file));
            readfile($file);
        }else{
            $this->render('index');
        }

    }

    public function actionEdit($index)
    {
        $model = new DownFormForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new DownFormForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['DownFormForm'])) {
            $model = new DownFormForm($_POST['DownFormForm']['scenario']);
            $model->attributes = $_POST['DownFormForm'];
            if ($model->validate()) {
                if(empty($model->docx_url)){
                    $docx = CUploadedFile::getInstance($model,'file');
                    $path = Yii::app()->basePath."/../upload/form/";
                    if (!file_exists($path)){
                        mkdir ($path);
                        $myfile = fopen($path."index.php", "w");
                        fclose($myfile);
                    }
                    if(is_null($docx)){
                        Dialog::message(Yii::t('dialog','Validation Message'), "文件不存在");
                        $this->render('form',array('model'=>$model,));
                        return false;
                    }else{
                        $model->docx_url = 'upload/form/'.date("YmdHis").".".$docx->getExtensionName();
                        $docx->saveAs($model->docx_url);
                    }
                }

                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('downForm/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除文檔
    public function actionDelete(){
        $model = new DownFormForm('delete');
        if (isset($_POST['DownFormForm'])) {
            $model->attributes = $_POST['DownFormForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','This word is under contract. Please delete the contract first'));
                $this->redirect(Yii::app()->createUrl('downForm/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('downForm/index'));
    }
}