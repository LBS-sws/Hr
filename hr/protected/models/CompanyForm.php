<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class CompanyForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $name;
	public $city;
	public $head;
	public $agent;
	public $address;
	public $phone;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'name'=>Yii::t('contract','Company Name'),
			'head'=>Yii::t('contract','Company Head'),
			'agent'=>Yii::t('contract','Company Agent'),
			'address'=>Yii::t('contract','Company Address'),
			'phone'=>Yii::t('contract','Company Phone'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, name, head, agent, address, phone','safe'),
			array('name','required'),
			array('name','validateName'),
			array('head','required'),
			array('agent','required'),
			array('address','required'),
		);
	}

	public function validateName($attribute, $params){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_company")
            ->where('id!=:id and name=:name ', array(':id'=>$this->id,':name'=>$this->name))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Company Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }
    //根據公司id獲取公司信息
	public function getCompanyToId($company_id){
	    $arr=array("name"=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_company")
            ->where('id=:id', array(':id'=>$company_id))->queryAll();
        if (count($rows) > 0){
            $arr=$rows[0];
        }
        return $arr;
    }

    //公司刪除時必須沒有員工
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('company_id=:company_id', array(':company_id'=>$this->id))->queryAll();
        if ($rows){
            return false;
        }
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_company")
            ->where('id=:id and city=:city ', array(':id'=>$index,':city'=>$city))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->head = $row['head'];
                $this->agent = $row['agent'];
                $this->address = $row['address'];
                $this->phone = $row['phone'];
                $this->city = $row['city'];
				break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_company where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into hr_company(
							name, agent, head, city, address, phone, lcu, lcd
						) values (
							:name, :agent, :head, :city, :address, :phone, :lcu, :lcd
						)";
				break;
			case 'edit':
				$sql = "update hr_company set
							name = :name, 
							agent = :agent, 
							head = :head,
							address = :address,
							phone = :phone,
							lud = :lud,
							luu = :luu 
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':agent')!==false)
			$command->bindParam(':agent',$this->agent,PDO::PARAM_STR);
		if (strpos($sql,':head')!==false)
			$command->bindParam(':head',$this->head,PDO::PARAM_STR);
		if (strpos($sql,':address')!==false)
			$command->bindParam(':address',$this->address,PDO::PARAM_STR);
		if (strpos($sql,':phone')!==false)
			$command->bindParam(':phone',$this->phone,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false)
			$command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
		if (strpos($sql,':lud')!==false)
			$command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
        return true;
	}
}
