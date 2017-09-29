<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class HolidayConForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $name;
	public $city;
	public $z_index;
	public $type=0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'name'=>Yii::t('contract',' Name'),
			'z_index'=>Yii::t('contract','Level'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, name, z_index, type','safe'),
			array('name','required'),
			array('name','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_Holiday")
            ->where('id!=:id and name=:name and city=:city and type=:type',
                array(':id'=>$this->id,':name'=>$this->name,':city'=>$city,':type'=>$this->type))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract',' Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

    public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Work");
        }else{
            return Yii::t("contract","Holiday");
        }
    }
    public function getTypeAcc(){
        if ($this->type == 1){
            return "ZC04";
        }else{
            return "ZC03";
        }
    }
    //獲取職位列表
	public function getHolidayAllList($type=0){
        $city = Yii::app()->user->city();
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_Holiday")
            ->where('type=:type and city=:city', array(':type'=>$type,':city'=>$city))->order("z_index desc")->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }
    //獲取職位名字
	public function getHolidayToId($holiday_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_holiday")
            ->where('id=:id', array(':id'=>$holiday_id))->queryRow();
        if ($rows){
            return $rows["name"];
        }
        return $holiday_id;
    }

    //職位刪除時必須沒有員工
	public function validateDelete(){
	    if($this->type == 1){
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('position=:position', array(':position'=>$this->id))->queryAll();
        }else{
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('department=:department', array(':department'=>$this->id))->queryAll();
        }
        if ($rows){
            return false;
        }
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_holiday")
            ->where('id=:id and city=:city ', array(':id'=>$index,':city'=>$city))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->name = $row['name'];
				$this->z_index = $row['z_index'];
                $this->type = $row['type'];
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
                $sql = "delete from hr_holiday where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into hr_holiday(
							name, type, z_index, city, lcu
						) values (
							:name, :type, :z_index, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_holiday set
							name = :name, 
							type = :type, 
							z_index = :z_index,
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
		if (strpos($sql,':z_index')!==false)
			$command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
		if (strpos($sql,':type')!==false)
			$command->bindParam(':type',$this->type,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
        return true;
	}
}
