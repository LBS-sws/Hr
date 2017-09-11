<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class CompanyController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new CompanyList;
        if (isset($_POST['CompanyList'])) {
            $model->attributes = $_POST['CompanyList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['company_01']) && !empty($session['company_01'])) {
                $criteria = $session['company_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new CompanyForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new CompanyForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new CompanyForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['CompanyForm'])) {
            $model = new CompanyForm($_POST['CompanyForm']['scenario']);
            $model->attributes = $_POST['CompanyForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('company/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //設置默認公司
    public function actionTacitly($index)
    {
        $model = new CompanyForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            Yii::app()->db->createCommand()->update('hr_company', array('tacitly'=>0));
            $rs = Yii::app()->db->createCommand()->update('hr_company', array(
                'tacitly'=>1
            ), 'id=:id', array(':id'=>$index));
            $this->redirect(Yii::app()->createUrl('company/index'));
        }
    }

    //刪除公司
    public function actionDelete(){
        $model = new CompanyForm('delete');
        if (isset($_POST['CompanyForm'])) {
            $model->attributes = $_POST['CompanyForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('contract','The company has employees, please delete employees first'));
                $this->redirect(Yii::app()->createUrl('company/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('company/index'));
    }
}