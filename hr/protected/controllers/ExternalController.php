<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ExternalController extends Controller
{
	public $function_id='EL01';

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
                'actions'=>array('new','edit','save','delete','audit','finish','uploadImg','fileupload','fileRemove'),
                'expression'=>array('ExternalController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','fileDownload','printImage'),
                'expression'=>array('ExternalController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EL01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EL01');
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public function actionIndex($pageNum=0){
        $model = new ExternalList;
        if (isset($_POST['ExternalList'])) {
            $model->attributes = $_POST['ExternalList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['external_01']) && !empty($session['external_01'])) {
                $criteria = $session['external_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionNew()
    {
        $model = new ExternalForm('new');
        $model->entry_time = $model->test_start_time = date("Y/m/d");
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new ExternalForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new ExternalForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['ExternalForm'])) {
            $model = new ExternalForm($_POST['ExternalForm']['scenario']);
            $model->attributes = $_POST['ExternalForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionAudit()
    {
        if (isset($_POST['ExternalForm'])) {
            $model = new ExternalForm('audit');
            $model->attributes = $_POST['ExternalForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->setScenario($_POST['ExternalForm']['scenario']);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    public function actionFinish()
    {
        if (isset($_POST['ExternalForm'])) {
            $data = $_POST['ExternalForm'];
            $uid = Yii::app()->user->id;
            Yii::app()->db->createCommand()->update('hr_externalee', array(
                'jj_card'=>$data['jj_card'],
                'social_code'=>$data['social_code'],
                'staff_status'=>0,
                'staff_old_status'=>0,
            ), 'id=:id and staff_status=4', array(':id'=>$data['id']));
            //記錄
            Yii::app()->db->createCommand()->insert('hr_externalee_history', array(
                "externalee_id"=>$data['id'],
                "status"=>"finish",
                "lcu"=>$uid,
                "lcd"=>date('Y-m-d H:i:s'),
            ));
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            $this->redirect(Yii::app()->createUrl('external/index'));
        }
    }

    //生成合同
    public function actionGenerate($index=0){
        if (empty($index) || !is_numeric($index)){
            $this->redirect(Yii::app()->createUrl('external/index'));
        }else{
            $bool = ExternaleeForm::updateExternaleeWord($index);
            if (!$bool){
                $this->redirect(Yii::app()->createUrl('external/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Contract formation success'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$index)));
            }
        }
    }

    //刪除草稿
    public function actionDelete(){
        $model = new ExternalForm('delete');
        if (isset($_POST['ExternalForm'])) {
            $model->attributes = $_POST['ExternalForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('external/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The dept has staff being used, please delete the staff first'));
                $this->redirect(Yii::app()->createUrl('external/edit',array('index'=>$model->id)));
            }
        }
    }

    //上傳附件
    public function actionFileupload($doctype) {
        $model = new ExternalForm();
        if (isset($_POST['ExternalForm'])) {
            $model->attributes = $_POST['ExternalForm'];

            $id = ($_POST['ExternalForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($model->docType,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //刪除附件
    public function actionFileRemove($doctype) {
        $model = new ExternalForm();
        if (isset($_POST['ExternalForm'])) {
            $model->attributes = $_POST['ExternalForm'];

            $docman = new DocMan($model->docType,$model->id,'ExternalForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    //下載附件
    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select city from hr_externalee where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $citylist = Yii::app()->user->city_allow();
            if (strpos($citylist, $row['city']) !== false) {
                $docman = new DocMan($doctype,$docId,'ExternalForm');
                $docman->masterId = $mastId;
                $docman->fileDownload($fileId);
            } else {
                throw new CHttpException(404,'Access right not match.');
            }
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }


    public function actionPrintImage($id = 0,$staff = 0,$str="") {
        $id = empty($id)?$staff:$id;
        $rows = Yii::app()->db->createCommand()->select("$str")
            ->from("hr_external")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rows){
            if(empty($rows[$str])){
                echo "圖片不存在";
                return false;
            }else{
                $n = new imgdata;
                $path = "protected/controllers/".$rows[$str];
                if (file_exists($path)) {
                    $n -> getdir($path);
                    $n -> img2data();
                    $n -> data2img();
                } else {
                    echo "地址不存在";
                    return false;
                }
            }
        }else{
            echo "沒找到圖片";
            return false;
        }
    }
}