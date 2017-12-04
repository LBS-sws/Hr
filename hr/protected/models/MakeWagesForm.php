<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class MakeWagesForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $name;
	public $city;
	public $code;
	public $phone;
	public $position;
    public $time;
    public $wages_head;
    public $wages_body;
    public $audit = 0;
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
            'city'=>Yii::t('contract','City'),
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
            array('id, employee_id, wages_status, name, code, city, phone, position, time, hour, sum, wages_head, wages_body, just_remark','safe'),
			array('wages_body','required'),
			array('wages_body','validateList'),
/*            array('license_time, organization_time','date','allowEmpty'=>true,
                'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d'),
            ),*/
		);
	}
    public function validateList($attribute, $params){
        if(!empty($this->wages_body)){
            if(is_array($this->wages_body)){
                foreach ($this->wages_body as $value){
                    if(empty($value)){
                        $message = Yii::t('contract','Wages Group'). Yii::t('contract',' can not be empty');
                        $this->addError($attribute,$message);
                        return false;
                    }
                }
            }else{
                $message = Yii::t('contract','Wages Group'). Yii::t('contract',' Error');
                $this->addError($attribute,$message);
            }
        }
    }

    //獲取員工的歷史工資
    public function getHistoryList($employee_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee_wages")
            ->where('employee_id=:id and wages_status=0', array(':id'=>$employee_id))->queryAll();
        if($rows){
            return $rows;
        }
        return array();
    }

    //完成操作
    public function finishWages($id){
        $city_allow = Yii::app()->user->city_allow();
        $rs = Yii::app()->db->createCommand()->select()->from("hr_employee_wages")
            ->where("id=:id and city in($city_allow)", array(':id'=>$id))->queryRow();
        if($rs){
            Yii::app()->db->createCommand()->update('hr_employee_wages', array(
                'wages_status'=>0,
            ), 'id=:id', array(':id'=>$id));
            return $rs["employee_id"];
        }else{
            return "";
        }
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where("id=:id and city in($city_allow)", array(':id'=>$index))->queryAll();
        $fastDate = date('Y-m-01', strtotime(date("Y-m-d")));
        $lastDate = date('Y-m-d', strtotime("$fastDate +1 month -1 day"));
        $records = Yii::app()->db->createCommand()->select()->from("hr_employee_wages")
            ->where("employee_id =:id and lcd >='$fastDate' and lcd <='$lastDate'",array(":id"=>$index))->queryRow();
		if (count($rows) > 0){
			foreach ($rows as $row)
			{
				$this->employee_id = $row['id'];
				$this->name = $row['name'];
				$this->code = $row['code'];
                $this->phone = $row['phone'];
                $this->city = $row['city'];
                $this->position = DeptForm::getDeptToid($row['position']);
                if ($records){
                    $this->scenario ="edit";
                    $this->id = $records["id"];
                    $this->time = date('Y-m', strtotime($records["lcd"]));
                    $this->wages_status = $records["wages_status"];
                    $this->wages_head = $records["wages_head"];
                    $this->just_remark = $records["just_remark"];
                    $this->wages_body = explode(",",$records["wages_body"]);
                    $this->historyList = MakeWagesForm::getHistoryList($this->employee_id);
                }else{
                    $this->scenario ="new";
                    $this->resetTableHeader();
                    $this->wages_body = explode(",",$row["price3"]);
                    $this->time = date('Y-m', strtotime(date("Y-m-d")));
                }
				break;
			}
		}
		return true;
	}

	public function resetTableHeader(){
        $rows = Yii::app()->db->createCommand()->select("price1")->from("hr_employee")
            ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
        $row = WagesForm::getWagesTypeList($rows["price1"]);
        $arr = array();
        foreach ($row as $value){
            array_push($arr,$value["type_name"]);
        }
        $this->wages_head = implode(",",$arr);
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
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->user_display_name();
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from hr_company where id = :id and city in ($city_allow)";
				break;
			case 'new':
				$sql = "insert into hr_employee_wages(
							city, employee_id, wages_head, wages_body, lcu, wages_status
						) values (
							:city, :employee_id, :wages_head, :wages_body, :lcu, :wages_status
						)";
				break;
			case 'edit':
				$sql = "update hr_employee_wages set
							wages_body = :wages_body, 
							wages_status = :wages_status, 
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
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':wages_status')!==false){
            if($this->audit === 1){
                $this->wages_status = 2;
                $command->bindParam(':wages_status',$this->wages_status,PDO::PARAM_INT);
            }else{
                $this->wages_status = 1;
                $command->bindParam(':wages_status',$this->wages_status,PDO::PARAM_INT);
            }
        }
		if (strpos($sql,':wages_head')!==false)
			$command->bindParam(':wages_head',$this->wages_head,PDO::PARAM_STR);
		if (strpos($sql,':wages_body')!==false)
			$command->bindParam(':wages_body',$this->wages_body,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);
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
