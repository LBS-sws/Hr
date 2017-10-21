<?php

class RewardConController extends Controller
{
	public function actionIndex($pageNum=0) 
	{
		$model = new RewardConList;
		if (isset($_POST['RewardConList'])) {
			$model->attributes = $_POST['RewardConList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rewardCon_ya01']) && !empty($session['rewardCon_ya01'])) {
				$criteria = $session['rewardCon_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['RewardConForm'])) {
			$model = new RewardConForm($_POST['RewardConForm']['scenario']);
			$model->attributes = $_POST['RewardConForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('rewardCon/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new RewardConForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new RewardConForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new RewardConForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new RewardConForm('delete');
        if (isset($_POST['RewardConForm'])) {
            $model->attributes = $_POST['RewardConForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('rewardCon/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("contract","The reward has staff being used, please delete the staff first"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('rewardCon/index'));
        }
    }

}
