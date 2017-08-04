<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $name;
	public $city;
	public $code;
    public $sex;
	public $company_id;
	public $address;
	public $address_code;
	public $contact_address;
	public $contact_address_code;
	public $phone;
	public $phone2;//緊急電話
	public $contract_id;
	public $user_card;
	public $department;
	public $position;
	public $wage;
	public $time=1;
	public $start_time;
	public $end_time;
	public $test_start_time;
	public $test_end_time;
	public $test_wage;
	public $word_status=1;
	public $test_type=1;
	public $word_html="";
	public $staff_status = 1;
	public $entry_time;//入職時間
	public $birth_time;//出生日期
	public $age;//年齡
	public $health;//身體狀況
	public $education;//學歷
	public $experience;//工作經驗
	public $english;//外語水平
	public $technology;//技術水平
	public $other;//其它說明
	public $year_day;//年假
	public $email;//員工郵箱
	public $remark;//備註
	public $price1;//每月工資
	public $price2;//加班工資
	public $price3;//每月補貼
	public $image_user;//員工照片
	public $image_code;//身份證照片
	public $image_work;//工作證明照片
	public $image_other;//其它照片
	public $ject_remark;//拒絕原因
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t('staff','Record ID'),
			'code'=>Yii::t('contract','Employee Code'),
			'sex'=>Yii::t('contract','Sex'),
			'age'=>Yii::t('contract','Age'),
			'birth_time'=>Yii::t('contract','Birth Date'),
			'name'=>Yii::t('contract','Employee Name'),
			'company_id'=>Yii::t('contract','Employee Belong'),
			'contract_id'=>Yii::t('contract','Employee Contract'),
			'address'=>Yii::t('contract','Address'),
			'contact_address'=>Yii::t('contract','Contact Address'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'phone2'=>Yii::t('contract','Emergency call'),
            'user_card'=>Yii::t('contract','ID Card'),
            'department'=>Yii::t('contract','Department'),
            'position'=>Yii::t('contract','Position'),
            'wage'=>Yii::t('contract','Wage'),
            'time'=>Yii::t('contract','Contract Time'),
            'start_time'=>Yii::t('contract','Contract Start Time'),
            'end_time'=>Yii::t('contract','Contract End Time'),
            'test_type'=>Yii::t('contract','Probation Type'),
            'test_time'=>Yii::t('contract','Probation Time'),
            'test_start_time'=>Yii::t('contract','Probation Start Time'),
            'test_end_time'=>Yii::t('contract','Probation End Time'),
            'test_wage'=>Yii::t('contract','Probation Wage'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'health'=>Yii::t('contract','Physical condition'),
            'education'=>Yii::t('contract','Degree level'),
            'experience'=>Yii::t('contract','Work experience'),
            'english'=>Yii::t('contract','Foreign language level'),
            'technology'=>Yii::t('contract','Technical level'),
            'other'=>Yii::t('contract','Other'),
            'year_day'=>Yii::t('contract','Annual leave'),
            'email'=>Yii::t('contract','Email'),
            'remark'=>Yii::t('contract','Remark'),
            'price1'=>Yii::t('contract','Basic salary'),
            'price2'=>Yii::t('contract','Overtime pay'),
            'price3'=>Yii::t('contract','Subsidies'),
            'image_user'=>Yii::t('contract','Staff photo'),
            'image_code'=>Yii::t('contract','Id photo'),
            'image_work'=>Yii::t('contract','Work photo'),
            'image_other'=>Yii::t('contract','Other photo'),
            'ject_remark'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, code, name, company_id, contract_id, address, address_code, contact_address, contact_address_code, phone, phone2, user_card, department, position, wage,time,
             start_time, end_time, test_type, test_start_time, sex, test_end_time, test_wage, word_status, city, entry_time, age, birth_time, health,ject_remark,staff_status,
              education, experience, english, technology, other, year_day, email, remark, price1, price2, price3, image_user, image_code, image_work, image_other',
                'safe'),
			array('ject_remark','required',"on"=>"reject"),
		);
	}

    //獲取可用公司
    public function getCompanyToCity(){
	    $arr = array(""=>"");
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_company")
            ->where('city=:city ', array(':city'=>$city))->queryAll();
        if(count($rows)>0){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }
    //獲取可用合同
    public function getContractToCity(){
	    $arr = array(""=>"");
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_contract")
            ->where('city=:city ', array(':city'=>$city))->queryAll();
        if(count($rows)>0){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id=:id and city=:city ', array(':id'=>$index,':city'=>$city))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->code = $row['code'];
				$this->name = $row['name'];
				$this->sex = $row['sex'];
				$this->company_id = $row['company_id'];
                $this->contract_id = $row['contract_id'];
                $this->address = $row['address'];
                $this->contact_address = $row['contact_address'];
                $this->phone = $row['phone'];
                $this->city = $row['city'];
                $this->user_card = $row['user_card'];
                $this->department = $row['department'];
                $this->position = $row['position'];
                $this->wage = $row['wage'];
                $this->start_time = $row['start_time'];
                $this->end_time = $row['end_time'];
                $this->test_type = $row['test_type'];
                $this->test_end_time = $row['test_end_time'];
                $this->test_start_time = $row['test_start_time'];
                $this->test_wage = $row['test_wage'];
                $this->word_status = $row['word_status'];
                $this->address_code = $row['address_code'];
                $this->contact_address_code = $row['contact_address_code'];
                $this->phone2 = $row['phone2'];
                $this->entry_time = $row['entry_time'];
                $this->birth_time = $row['birth_time'];
                $this->age = $row['age'];
                $this->health = $row['health'];
                $this->education = $row['education'];
                $this->staff_status = $row['staff_status'];
                $this->experience = $row['experience'];
                $this->english = $row['english'];
                $this->technology = $row['technology'];
                $this->other = $row['other'];
                $this->year_day = $row['year_day'];
                $this->email = $row['email'];
                $this->remark = $row['remark'];
                $this->price1 = $row['price1'];
                $this->price2 = $row['price2'];
                $this->price3 = $row['price3'];
                $this->image_user = $row['image_user'];
                $this->image_code = $row['image_code'];
                $this->image_work = $row['image_work'];
                $this->image_other = $row['image_other'];
                $this->ject_remark = $row['ject_remark'];
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
		}catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}


	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'reject':
				$sql = "update hr_employee set
							staff_status = 3,
							ject_remark = :ject_remark,
							lud = :lud,
							luu = :luu 
						where id = :id
						";
				break;
			case 'audit':
				$sql = "update hr_employee set
							staff_status = 4,
							lud = :lud,
							luu = :luu 
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':ject_remark')!==false)
			$command->bindParam(':ject_remark',$this->ject_remark,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lud')!==false)
			$command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);

        //die();
		$command->execute();
        return true;
	}
}
