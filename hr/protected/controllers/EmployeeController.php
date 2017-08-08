<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class EmployeeController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new EmployeeList;
        if (isset($_POST['EmployeeList'])) {
            $model->attributes = $_POST['EmployeeList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['employee_01']) && !empty($session['employee_01'])) {
                $criteria = $session['employee_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new EmployeeForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new EmployeeForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionSave()
    {
        if (isset($_POST['EmployeeForm'])) {
            $model = new EmployeeForm($_POST['EmployeeForm']['scenario']);
            $model->attributes = $_POST['EmployeeForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //生成合同
    public function actionGenerate($index=0){
        if (empty($index) || !is_numeric($index)){
            $this->redirect(Yii::app()->createUrl('employee/index'));
        }else{
            $bool = EmployeeForm::updateEmployeeWord($index);
            if (!$bool){
                $this->redirect(Yii::app()->createUrl('employee/index'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','Contract formation success'));
                $this->redirect(Yii::app()->createUrl('employee/edit',array('index'=>$index)));
            }
        }
    }

    //下載合同

    public function actionDownfile($index)
    {
        $url = EmployeeForm::updateEmployeeWord($index);
        if($url){
            $file = Yii::app()->basePath."/../".$url["word_url"];
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename='.$url["name"].'.docx');
            header("Content-Length: ". filesize($file));
            readfile($file);
        }else{
            $this->render('index');
        }

    }

    //變更合同
    public function actionChanges($index){
        echo "沒有變更合同的功能文檔，無法開發";
    }

    //員工離職
    public function actionDeparture($index){
        echo "沒有員工離職的功能文檔，無法開發";
    }


/*    //刪除員工
    public function actionDelete(){
        $model = new EmployeeForm('delete');
        if (isset($_POST['EmployeeForm'])) {
            $model->attributes = $_POST['EmployeeForm'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('employee/index'));
    }*/
}