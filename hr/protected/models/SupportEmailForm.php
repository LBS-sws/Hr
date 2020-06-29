<?php

class SupportEmailForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $employee_name;
	public $city="ZY";
	public $dept_name;
	public $code;
	public $phone;
	public $status_type;
	public $support_city;

	public function attributeLabels()
	{
		return array(
            'employee_name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'dept_name'=>Yii::t('contract','Position'),
            'status_type'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'support_city'=>Yii::t('contract','support city'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,status_type,support_city','safe'),
            array('employee_id','required'),
            array('support_city','required'),
            array('employee_id','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("a.name,a.code,b.name as dept_name")->from("hr_employee a")
            ->leftJoin("hr_dept b","a.position = b.id")
            ->where("a.id=:id and a.city ='$this->city' AND a.staff_status = 0",array(":id"=>$this->employee_id))->queryRow();
        if($rows){
            $this->code = $rows["code"];
            $this->employee_name = $rows["name"];
            $this->dept_name = $rows["dept_name"];
            $rows = Yii::app()->db->createCommand()->select("id,support_city")->from("hr_apply_support_email")
                ->where("employee_id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($rows){
                $this->id = $rows["id"];
                $this->setScenario("edit");
            }else{
                $this->setScenario("new");
            }
        }else{
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
		$row = Yii::app()->db->createCommand()
            ->select("a.id,a.name,a.code,d.name as dept_name")
            ->from("hr_employee a")
            ->leftJoin("hr_dept d","a.position = d.id")
            ->where("a.id=:id and a.city ='$this->city' AND a.staff_status = 0",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->employee_id = $row['id'];
            $this->employee_name = $row['name'];
            $this->dept_name = $row['dept_name'];
            $this->code = $row['code'];
            $review = Yii::app()->db->createCommand()->select("id,support_city")->from("hr_apply_support_email")
                ->where("employee_id=:id",array(":id"=>$this->employee_id))->queryRow();
            if($review){
                $this->id = $review["id"];
                $this->support_city = $review["support_city"];
            }
            return true;
		}else{
		    return false;
        }
	}


	public function getReadonly(){
        if ($this->getScenario()=='view'){
            return true;//只读
        }else{
            return false;
        }
    }


    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("id")->from("hr_apply_support_email")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		//$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			//$transaction->commit();
		}
		catch(Exception $e) {
			//$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
        $uid = Yii::app()->user->id;
        switch ($this->scenario) {
            case 'new':
                $connection->createCommand()->insert("hr_apply_support_email", array(
                    'employee_id'=>$this->employee_id,
                    'support_city'=>$this->support_city,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                break;
            case 'edit':
                $connection->createCommand()->update('hr_apply_support_email', array(
                    'support_city'=>$this->support_city,
                    'luu'=>$uid,
                ), 'id=:id', array(':id'=>$this->id));
                break;
            case 'delete':
                $connection->createCommand()->delete('hr_apply_support_email', 'id=:id', array(':id'=>$this->id));
                break;
        }

		return true;
	}

}
