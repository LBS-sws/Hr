<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditHolidayForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $employee_name;
	public $holiday_id;
	public $holiday_name;
	public $start_time;
	public $end_time;
	public $hour;
	public $remark;
	public $reject_remark;
	public $status=0;
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
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, status, type, employee_id, employee_name, holiday_id, start_time, end_time, hour, remark, reject_remark','safe'),
			array('reject_remark','required','on'=>"reject"),
		);
	}

    public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Work");
        }else{
            return Yii::t("contract","holiday");
        }
    }
    public function getTypeAcc(){
        if ($this->type == 1){
            return "ZG05";
        }else{
            return "ZG04";
        }
    }
    public function getTitleAppText(){
        if ($this->type == 1){
            return Yii::t("app","Work Audit");
        }else{
            return Yii::t("app","Holiday Audit");
        }
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
            case 'audit':
                $sql = "update hr_employee_work set
							status = 2, 
							luu = :luu 
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update hr_employee_work set
							status = 3, 
							reject_remark = :reject_remark, 
							luu = :luu 
						where id = :id
						";
                break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':reject_remark')!==false)
			$command->bindParam(':reject_remark',$this->reject_remark,PDO::PARAM_STR);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);

		$command->execute();
        return true;
	}
}
