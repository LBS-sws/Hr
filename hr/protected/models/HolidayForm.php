<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class HolidayForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $employee_name;
	public $city;
	public $holiday_id;
	public $holiday_name;
	public $start_time;
	public $end_time;
	public $hour;
	public $remark;
	public $reject_remark;
	public $status=0;
	public $only=0;
    public $type = 0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'holiday_id'=>Yii::t('contract',' Cause'),
			'employee_name'=>Yii::t('contract','Employee Name'),
			'start_time'=>Yii::t('contract','Start Time'),
			'end_time'=>Yii::t('contract','End Time'),
			'hour'=>Yii::t('contract','Hour'),
			'remark'=>Yii::t('contract','Remark'),
			'reject_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	public function validateEmployee(){
	    if (empty($this->only)){
            $uid = Yii::app()->user->id;
            $city = Yii::app()->user->city();
            $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name,city")->from("hr_binding")
                ->where('user_id=:user_id and city=:city',
                    array(':user_id'=>$uid,':city'=>$city))->queryRow();
            if ($rows){
                $this->employee_id = $rows["employee_id"];
                $this->employee_name = $rows["employee_name"];
                $this->city = $rows["city"];
                return true;
            }
            return false;
        }
        return true;
    }
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, only, type, employee_id, employee_name, holiday_id, start_time, end_time, hour, remark, reject_remark','safe'),
			array('holiday_id','required'),
			array('start_time','required'),
			array('end_time','required'),
			array('holiday_id','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("name")->from("hr_holiday")
            ->where('id=:id and city=:city and type=:type',
                array(':id'=>$this->holiday_id,':city'=>$city,':type'=>$this->type))->queryRow();
        if ($rows){
            $this->holiday_name = $rows["name"];
        }else{
            $message = $this->getTypeName(). Yii::t('contract',' Cause'). Yii::t('contract',' Did not find');
            $this->addError($attribute,$message);
        }
    }

    public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Work");
        }else{
            return Yii::t("contract","holiday");
        }
    }
    public function getTypeAcc(){
        if ($this->only == 1){
            if ($this->type == 1){
                return "ZE06";
            }else{
                return "ZE05";
            }
        }else{
            if ($this->type == 1){
                return "ZA06";
            }else{
                return "ZA05";
            }
        }
    }
    public function getInputBool(){
        if($this->scenario == "view" || $this->only == 1){
            return true;
        }
        if(!empty($this->status) && $this->status != 3){
            return true;
        }
        return false;
    }
    //獲取列表
	public function getHolidayAllList($type=0){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
	    $arr=array(""=>"");
        $rows = Yii::app()->db->createCommand()->select()->from("hr_holiday")
            ->where("city in ($city_allow) and type=:type",array(':type'=>$this->type))->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //職位刪除時必須沒有員工
	public function validateDelete(){
/*	    if($this->type == 1){
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('position=:position', array(':position'=>$this->id))->queryAll();
        }else{
            $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('department=:department', array(':department'=>$this->id))->queryAll();
        }
        if ($rows){
            return false;
        }*/
        return true;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee_work")
            ->where("id=:id and city in ($city_allow)", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->employee_id = $row['employee_id'];
				$this->employee_name = $row['employee_name'];
				$this->city = $row['city'];
				$this->holiday_id = $row['holiday_id'];
				$this->start_time = date("Y/m/d",strtotime($row['start_time']));
				$this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->hour = $row['hour'];
                $this->remark = $row['remark'];
                $this->status = $row['status'];
                $this->reject_remark = $row['reject_remark'];
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
                $sql = "delete from hr_employee_work where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into hr_employee_work(
							employee_id,employee_name,type,holiday_id,holiday_name, start_time, end_time, hour, remark, reject_remark, status, city, lcu
						) values (
							:employee_id,:employee_name,:type,:holiday_id,:holiday_name, :start_time, :end_time, :hour, :remark, :reject_remark, :status, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_employee_work set
							employee_id = :employee_id, 
							employee_name = :employee_name, 
							holiday_id = :holiday_id, 
							holiday_name = :holiday_name, 
							start_time = :start_time, 
							end_time = :end_time,
							hour = :hour,
							remark = :remark,
							reject_remark = :reject_remark,
							status = :status,
							luu = :luu 
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
		if (strpos($sql,':employee_name')!==false)
			$command->bindParam(':employee_name',$this->employee_name,PDO::PARAM_STR);
		if (strpos($sql,':holiday_id')!==false)
			$command->bindParam(':holiday_id',$this->holiday_id,PDO::PARAM_STR);
		if (strpos($sql,':holiday_name')!==false)
			$command->bindParam(':holiday_name',$this->holiday_name,PDO::PARAM_STR);
		if (strpos($sql,':start_time')!==false)
			$command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
		if (strpos($sql,':end_time')!==false)
			$command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
		if (strpos($sql,':hour')!==false)
			$command->bindParam(':hour',$this->hour,PDO::PARAM_STR);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':reject_remark')!==false)
			$command->bindParam(':reject_remark',$this->reject_remark,PDO::PARAM_STR);
		if (strpos($sql,':status')!==false)
			$command->bindParam(':status',$this->status,PDO::PARAM_STR);
		if (strpos($sql,':type')!==false)
			$command->bindParam(':type',$this->type,PDO::PARAM_STR);

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

	public function finishHoliday(){
        Yii::app()->db->createCommand()->update('hr_employee_work', array(
            'status'=>4
        ), 'id=:id', array(':id'=>$this->id));
    }
}
