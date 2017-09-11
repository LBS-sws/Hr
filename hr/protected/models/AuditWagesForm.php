<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditWagesForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $name;
	public $code;
	public $phone;
	public $position;
    public $time;
    public $wages_head;
    public $wages_body;
    public $wages_status;
    public $just_remark;
    public $historyList;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'position'=>Yii::t('contract','Position'),
            'time'=>Yii::t('contract','Wages Time'),
            'hour'=>Yii::t('contract','Wages Hour'),
            'sum'=>Yii::t('contract','Wages Sum'),
            'wages_body'=>Yii::t('contract','Wages Group'),
            'just_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, employee_id, wages_status, name, code, phone, position, time, hour, sum, wages_head, wages_body, just_remark','safe'),
			array('just_remark','required',"on"=>"reject"),
/*            array('license_time, organization_time','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d'),
            ),*/
		);
	}

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $fastDate = date('Y-m-01', strtotime(date("Y-m-d")));
        $lastDate = date('Y-m-d', strtotime("$fastDate +1 month -1 day"));
        $records = Yii::app()->db->createCommand()->select()->from("hr_employee_wages")
            ->where("id =:id and lcd >='$fastDate' and lcd <='$lastDate' AND wages_status != 1 AND wages_status != 0",array(":id"=>$index))->queryRow();
		if ($records){
            $staff = EmployeeForm::getEmployeeOneToId($records['employee_id']);
            $this->employee_id = $staff['id'];
            $this->name = $staff['name'];
            $this->code = $staff['code'];
            $this->phone = $staff['phone'];
            $this->position = DeptForm::getDeptToid($staff['position']);

            $this->id = $records["id"];
            $this->time = date('Y-m', strtotime($records["lcd"]));
            $this->wages_status = $records["wages_status"];
            $this->wages_head = $records["wages_head"];
            $this->just_remark = $records["just_remark"];
            $this->wages_body = explode(",",$records["wages_body"]);
            $this->historyList = MakeWagesForm::getHistoryList($this->employee_id);
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
                $sql = "update hr_employee_wages set
							wages_status = 3, 
							luu = :luu 
						where id = :id
						";
                break;
			case 'reject':
				$sql = "update hr_employee_wages set
							wages_status = 4, 
							just_remark = :just_remark, 
							luu = :luu 
						where id = :id
						";
				break;
		}
		if(is_array($this->wages_body)){
            $this->wages_body = implode(",",$this->wages_body);
        }
		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':just_remark')!==false)
			$command->bindParam(':just_remark',$this->just_remark,PDO::PARAM_INT);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);

		$command->execute();
        return true;
	}
}
