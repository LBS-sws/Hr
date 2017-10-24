<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class AuditHistoryForm extends CFormModel
{
	/* User Fields */
	public $employee_id;
	public $id;
	public $name;
	public $city;
	public $code;
    public $sex;
	public $staff_id;
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
    public $ject_remark;//拒絕備註
    public $remark;//備註
    public $price1;//每月工資
    public $price2;//加班工資
    public $price3;//每月補貼
    public $image_user;//員工照片
    public $image_code;//身份證照片
    public $image_work;//工作證明照片
    public $image_other;//其它照片
    public $ld_card;//勞動保障卡號
    public $sb_card;//社保卡號
    public $jj_card;//公積金卡號
    public $update_remark;//員工修改備註
    public $operation;//員工修改備註
    public $historyList;//員工歷史
    public $staff_type;//员工类别
    public $staff_leader;//队长/组长
    public $test_length;//
    public $attachment="";//附件
    public $nation;//民族
    public $household;//户籍类型
    public $empoyment_code;//就业登记证号
    public $social_code;//社会保障卡号
    public $fix_time=0;//合同類型
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
            'staff_id'=>Yii::t('contract','Employee Belong'),
            'company_id'=>Yii::t('contract','Employee Contract Belong'),
            'contract_id'=>Yii::t('contract','Employee Contract'),
            'address'=>Yii::t('contract','Old Address'),
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
            'price1'=>Yii::t('contract','Wages Name'),
            'price3'=>Yii::t('contract','Wages Type'),
            'image_user'=>Yii::t('contract','Staff photo'),
            'image_code'=>Yii::t('contract','Id photo'),
            'image_work'=>Yii::t('contract','Work photo'),
            'image_other'=>Yii::t('contract','Other photo'),
            'ld_card'=>Yii::t('contract','Labor security card'),
            'sb_card'=>Yii::t('contract','Social security card'),
            'jj_card'=>Yii::t('contract','Accumulation fund card'),
            'ject_remark'=>Yii::t('contract','Rejected Remark'),
            'update_remark'=>Yii::t('contract',"Operation")."".Yii::t('contract','Remark'),
            'staff_type'=>Yii::t('staff','Staff Type'),
            'staff_leader'=>Yii::t('staff','Team/Group Leader'),
            'test_length'=>Yii::t('contract','Probation Time Longer'),
            'attachment'=>Yii::t('contract','Attachment'),
            'nation'=>Yii::t('contract','nation'),
            'household'=>Yii::t('contract','Household type'),
            'empoyment_code'=>Yii::t('contract','Employment registration certificate'),
            'social_code'=>Yii::t('contract','Social security card number'),
            'fix_time'=>Yii::t('contract','contract deadline'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id,employee_id,ject_remark,operation,update_remark, code, name, staff_id, company_id, contract_id, address, address_code, contact_address, contact_address_code, phone, phone2, user_card, department, position, wage,time,
             start_time, end_time, test_type, test_start_time, sex, test_end_time, test_wage, word_status, city, entry_time, age, birth_time, health,staff_status,
             ld_card, sb_card, jj_card,attachment,nation, household, empoyment_code, social_code, fix_time,
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
    //自動變化表頭
    public function setFormTitle(){
        return Yii::t("app","Employee Update Audit");
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

    //獲取員工歷史
    public function getStaffHistoryList($staff_id){
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee_history")
            ->where('employee_id=:id', array(':id'=>$staff_id))->order('id desc')->queryAll();
        $staff = Yii::app()->db->createCommand()->select("code,name")->from("hr_employee")
            ->where('id=:id', array(':id'=>$staff_id))->queryAll();
        if ($rows){
            foreach ($rows as $key => $row){
                if($staff){
                    $rows[$key]["code"] = $staff[0]["code"];
                    $rows[$key]["name"] = $staff[0]["name"];
                }else{
                    $rows[$key]["code"] = "";
                    $rows[$key]["name"] = "";
                }
            }
        }else{
            return "";
        }
        return $rows;
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee_operate")
            ->where('id=:id and finish != 1', array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
                $this->id = $row['id'];
                $this->employee_id = $row['employee_id'];
                $this->update_remark = $row['update_remark'];
                $this->code = $row['code'];
                $this->name = $row['name'];
                $this->sex = $row['sex'];
                $this->staff_id = $row['staff_id'];
                $this->company_id = $row['company_id'];
                $this->contract_id = $row['contract_id'];
                $this->address = $row['address'];
                $this->contact_address = $row['contact_address'];
                $this->phone = $row['phone'];
                $this->city = $row['city'];
                $this->user_card = $row['user_card'];
                $this->department = DeptForm::getDeptToid($row['department']);
                $this->position = DeptForm::getDeptToid($row['position']);
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
                $this->ject_remark = $row['ject_remark'];
                $this->price1 = $row['price1'];
                $this->price3 = explode(",",$row['price3']);
                $this->image_user = $row['image_user'];
                $this->image_code = $row['image_code'];
                $this->image_work = $row['image_work'];
                $this->image_other = $row['image_other'];
                $this->ld_card = $row['ld_card'];
                $this->sb_card = $row['sb_card'];
                $this->jj_card = $row['jj_card'];
                $this->operation = $row['operation'];
                $this->historyList = AuditHistoryForm::getStaffHistoryList($row["employee_id"]);
                $this->test_length = $row['test_length'];
                $this->staff_type = $row['staff_type'];
                $this->staff_leader = $row['staff_leader'];
                $this->attachment = $row['attachment'];
                $this->nation = $row['nation'];
                $this->household = $row['household'];
                $this->empoyment_code = $row['empoyment_code'];
                $this->social_code = $row['social_code'];
                $this->fix_time = $row['fix_time'];
				break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
        $uid = Yii::app()->user->id;
        $lud = date("Y-m-d H:i:s");
        switch ($this->scenario){
            case "audit"://通過
                Yii::app()->db->createCommand()->update('hr_employee_operate', array(
                    'staff_status'=>4,
                    'luu'=>$uid,
                    'lud'=>$lud,
                ), 'id=:id', array(':id'=>$this->id));
                break;
            case "reject"://拒絕
                Yii::app()->db->createCommand()->update('hr_employee_operate', array(
                    'staff_status'=>3,
                    'ject_remark'=>$this->ject_remark,
                    'luu'=>$uid,
                    'lud'=>$lud,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }

        //記錄
        Yii::app()->db->createCommand()->insert('hr_employee_history', array(
            "employee_id"=>$this->employee_id,
            "status"=>$this->scenario,
            "remark"=>$this->ject_remark,
            "lcu"=>$uid,
            "lcd"=>$lud,
        ));
        if($this->scenario == "audit"){
            $this->finish();
        }
	}


    public function setAttachment(){
        $str = $this->attachment;
        if(empty($str)){
            $arr = array();
        }else{
            $arr = explode(",",$str);
            for($i = 0;$i<count($arr);$i++){
                $rows = Yii::app()->db->createCommand()->select()->from("hr_attachment")
                    ->where('id=:id', array(':id'=>$arr[$i]))->queryRow();
                if($rows){
                    $arr[$i] = $rows;
                }else{
                    unset($arr[$i]);
                }
            }
        }
        $this->attachment = $arr;
        return $arr;
    }

    //變更完成
    public function finish(){
        $uid = Yii::app()->user->id;
        $date = date("Y-m-d H:i:s");
        $staff = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
        $staffNew = Yii::app()->db->createCommand()->select()->from("hr_employee_operate")
            ->where('id=:id', array(':id'=>$this->id))->queryRow();
        unset($staff["id"]);
        unset($staff["lcd"]);
        unset($staff["lud"]);
        unset($staff["luu"]);
        unset($staff["lcu"]);
        $keyList =array_keys($staff);
        $operation = $staffNew['operation'];
        $dateKey = array("test_start_time","test_end_time","entry_time","birth_time");
        foreach ($staffNew as $key =>$value){
            if (!in_array($key,$keyList)){
                unset($staffNew[$key]);
                continue;
            }
            if(empty($value)&&in_array($key,$dateKey)){
                unset($staffNew[$key]);
            }
        }
        if($operation === "departure"){
            $staffNew["staff_status"] = -1;//離職
        }else{
            $staffNew["staff_status"] = 0;
        }
        $staff["finish"] = 1;
        Yii::app()->db->createCommand()->update('hr_employee', $staffNew, 'id=:id', array(':id'=>$this->employee_id));
        Yii::app()->db->createCommand()->update('hr_employee_operate', $staff, 'id=:id', array(':id'=>$this->id));

        //記錄
        Yii::app()->db->createCommand()->insert('hr_employee_history', array(
            "employee_id"=>$this->employee_id,
            "status"=>"finish",
            "lcu"=>$uid,
            "lcd"=>$date,
        ));
    }
}
