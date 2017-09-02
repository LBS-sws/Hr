<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class DepartureController extends Controller
{

    public function actionIndex($pageNum=0){
        $model = new DepartureList;
        if (isset($_POST['DepartureList'])) {
            $model->attributes = $_POST['DepartureList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['departure_01']) && !empty($session['departure_01'])) {
                $criteria = $session['departure_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionEdit($index)
    {
        $model = new DepartureForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


/*    //刪除員工
    public function actionDelete(){
        $model = new DepartureForm('delete');
        if (isset($_POST['DepartureForm'])) {
            $model->attributes = $_POST['DepartureForm'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('departure/index'));
    }*/
}