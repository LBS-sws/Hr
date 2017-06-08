<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class StaffForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $code;
	public $name;
	public $position;
	public $join_dt;
	public $ctrt_start_dt;
	public $change_type;
	public $ctrt_period;
	public $email;
	public $change_time;
	public $change_reason;
	public $remarks;
	public $staff_type;
	public $leader;
	public $city;


	public function getCityList(){
        $arr = array();
	    $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix.".sec_city";
        $rs = Yii::app()->db->createCommand()->select("code,name")->from($suffix)->queryAll();
        foreach ($rs as $rows){
            $arr[$rows["code"]] = $rows["name"];
        }
	    return $arr;
    }
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'code'=>Yii::t('staff','Code'),
			'name'=>Yii::t('staff','Name'),
			'position'=>Yii::t('staff','Position'),
			'join_dt'=>Yii::t('staff','Join Date').' '.Yii::t('misc','(Y/M/D)'),
			'ctrt_start_dt'=>Yii::t('staff','Cont. Start Date').' '.Yii::t('misc','(Y/M/D)'),
			'change_type'=>Yii::t('staff','Change Type'),
			'ctrt_period'=>Yii::t('staff','Cont. Period'),
			'email'=>Yii::t('staff','Email'),
			'change_time'=>Yii::t('staff','Change Time').' '.Yii::t('misc','(Y/M/D)'),
			'change_reason'=>Yii::t('staff','Change Reason'),
			'remarks'=>Yii::t('staff','Remarks'),
			'staff_type'=>Yii::t('staff','Staff Type'),
			'leader'=>Yii::t('staff','Team/Group Leader'),
			'city'=>Yii::t('user','City'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, position, change_type, change_time, change_reason, remarks, email, staff_type, leader, city','safe'),
			array('name','required'),
			array('join_dt','required'),
			array('ctrt_start_dt','required'),
			array('change_time','required','on'=>'edit'),
/*
			array('code','unique','allowEmpty'=>true,
					'attributeName'=>'code',
					'caseSensitive'=>false,
					'className'=>'Staff',
					'on'=>'new',
				),
*/	
			array('code','validateCode'),
			array('city','validateEditCity','on'=>'edit'),
			array('name','validateName','on'=>'new'),
//			array('code','safe','on'=>'edit'),
			array('ctrt_period','numerical','allowEmpty'=>true,'integerOnly'=>true),
			array('ctrt_period','in','range'=>range(0,600)),
			array('join_dt, ctrt_start_dt, change_time','date','allowEmpty'=>true,
				'format'=>array('yyyy/MM/dd','yyyy-MM-dd','yyyy/M/d'),
			),
//			array('email','email','allowEmpty'=>true),
		);
	}

	public function validateCode($attribute, $params) {
		$code = $this->$attribute;
		$city = Yii::app()->user->city();
		if (!empty($code)) {
			switch ($this->scenario) {
				case 'new':
					if (Staff::model()->exists('code=? and city=?',array($code,$city))) {
						$this->addError($attribute, Yii::t('staff','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
				case 'edit':
					if (Staff::model()->exists('code=? and city=? and id<>?',array($code,$city,$this->id))) {
						$this->addError($attribute, Yii::t('staff','Code')." '".$code."' ".Yii::t('app','already used'));
					}
					break;
			}
		}
	}

	public function validateName($attribute, $params) {
        $code = $this->attributes;
        $name = $code['name'];
		if ($this->scenario == 'new') {
            $rows = Yii::app()->db->createCommand()->from("hr_staff_copy")->where("name=:name",array(":name"=>$name))->queryAll();
            if(count($rows) > 0){
                $this->addError($attribute, Yii::t('staff','The user name already exists'));
            }
		}
	}
	public function validateEditCity($attribute, $params) {
		$code = $this->attributes;
		$city = Yii::app()->user->city();
		if ($this->scenario == 'edit' && $code["change_type"] == "transfer") {
		    if($code["city"] == $city){
                $this->addError($attribute, Yii::t('staff','Employees are in the city'));
            }
		}
	}

	public function retrieveData($index)
	{
		$city = Yii::app()->user->city_allow();
		$sql = "select * from hr_staff_copy where id=$index and city in ($city)";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->name = $row['name'];
				$this->position = $row['position'];
				$this->join_dt = General::toDate($row['join_dt']);
				$this->ctrt_start_dt = General::toDate($row['ctrt_start_dt']);
				$this->change_type = $row['change_type'];
				$this->ctrt_period = $row['ctrt_period'];
				$this->email = $row['email'];
				$this->change_time = General::toDate($row['change_time']);
				$this->change_reason = $row['change_reason'];
				$this->remarks = $row['remarks'];
				$this->staff_type = $row['staff_type'];
				$this->leader = $row['leader'];
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
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from hr_staff_copy where id = :id and city = :city";
				break;
			case 'new':
				$sql = "insert into hr_staff_copy(
							name, code, position, join_dt, ctrt_start_dt, ctrt_period, email ,
						    remarks, staff_type, leader, city, luu, lcu
						) values (
							:name, :code, :position, :join_dt, :ctrt_start_dt, :ctrt_period, :email,
							 :remarks, :staff_type, :leader, :city, :luu, :lcu
						)";
				break;
			case 'edit':
				$sql = "update hr_staff_copy set
							name = :name, 
							code = :code, 
							position = :position,
							join_dt = :join_dt,
							ctrt_start_dt = :ctrt_start_dt, 
							ctrt_period = :ctrt_period,
							email = :email,
							remarks = :remarks,
							staff_type = :staff_type,
							leader = :leader,
							luu = :luu ,
							change_type = :change_type, 
							change_time = :change_time,
							change_reason = :change_reason
						where id = :id
						";
				break;
			case 'detailedit':
				$sql = "update hr_staff_copy set
							name = :name, 
							code = :code, 
							position = :position,
							join_dt = :join_dt,
							ctrt_start_dt = :ctrt_start_dt, 
							ctrt_period = :ctrt_period,
							email = :email,
							remarks = :remarks,
							staff_type = :staff_type,
							leader = :leader,
							luu = :luu ,
							change_type = :change_type, 
							change_time = :change_time,
							change_reason = :change_reason
						where id = :id
						";
				break;
		}

		$city = Yii::app()->user->city();
		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':code')!==false)
			$command->bindParam(':code',$this->code,PDO::PARAM_STR);
		if (strpos($sql,':position')!==false)
			$command->bindParam(':position',$this->position,PDO::PARAM_STR);
		if (strpos($sql,':join_dt')!==false) {
			$jdate = General::toMyDate($this->join_dt);
			$command->bindParam(':join_dt',$jdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':ctrt_start_dt')!==false) {
			$csdate = General::toMyDate($this->ctrt_start_dt);
			$command->bindParam(':ctrt_start_dt',$csdate,PDO::PARAM_STR);
		}
		if (strpos($sql,':change_type')!==false) {
			$command->bindParam(':change_type',$this->change_type,PDO::PARAM_INT);
		}
		if (strpos($sql,':ctrt_period')!==false) {
			$cp = General::toMyNumber($this->ctrt_period);
			$command->bindParam(':ctrt_period',$cp,PDO::PARAM_INT);
		}
		if (strpos($sql,':email')!==false)
			$command->bindParam(':email',$this->email,PDO::PARAM_STR);
		if (strpos($sql,':change_time')!==false) {
			$ldate = General::toMyDate($this->change_time);
			$command->bindParam(':change_time',$ldate,PDO::PARAM_STR);
		}
		if (strpos($sql,':change_reason')!==false)
			$command->bindParam(':change_reason',$this->change_reason,PDO::PARAM_STR);
		if (strpos($sql,':remarks')!==false)
			$command->bindParam(':remarks',$this->remarks,PDO::PARAM_STR);
		if (strpos($sql,':staff_type')!==false)
			$command->bindParam(':staff_type',$this->staff_type,PDO::PARAM_STR);
		if (strpos($sql,':leader')!==false)
			$command->bindParam(':leader',$this->leader,PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		$command->execute();

		if ($this->scenario=='new')
			$this->id = Yii::app()->db->getLastInsertID();
		if($this->scenario == 'edit'){
            $code = $this->attributes;
            $code["ctrt_start_dt"] = $code["change_time"];
            if($code['change_type'] == "transfer"){
                //條件為調職時，修改員工所在城市
            }
            unset($code['change_type']);
            unset($code['change_time']);
            unset($code['change_reason']);
            unset($code['id']);
            unset($code['remarks']);
            unset($code['code']);
            Yii::app()->db->createCommand()->insert('hr_staff_copy',$code);
        }
		return true;
	}
}
