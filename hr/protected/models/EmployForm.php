<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class EmployForm extends StaffForm
{
	/**
     *
	 * Declares the validation rules.
	 */
	public function rulesEx()
	{
	    $requiredList = $this->getRequiredList();
	    $requiredStr = implode(",",$requiredList);
		return array(
			array('entry_time,emergency_user,emergency_phone','required','on'=>"audit"),
			array($requiredStr,'required','on'=>"audit"),
			array('wage','validateWage','on'=>"audit"),//由於工資有些用戶沒有權限
			array('end_time','validateEndTime','on'=>"audit"),
			array('test_type','validateTestType','on'=>"audit"),
            array('year_day', 'validateYearDay','on'=>"audit"),
		);
	}

    public function validateID($attribute, $params){
        if($this->getScenario()!='new'){
            $allow_city = Yii::app()->user->city_allow();
            $row = Yii::app()->db->createCommand()->select("id,city")->from("hr_employee")
                ->where("id=:id and city in ({$allow_city}) and staff_status in (1,3) and table_type=1", array(':id'=>$this->id))
                ->queryRow();
            if($row){
                $this->city = $row["city"];
            }else{
                $this->addError($attribute,"员工不存在，请刷新重试");
            }
        }else{
            $this->city = Yii::app()->user->city();
        }
    }

	public function validateName($attribute, $params){
/*        $rows = Yii::app()->db->createCommand()->select()->from("hr_employee")
            ->where('id!=:id and name=:name ', array(':id'=>$this->id,':name'=>$this->name))->queryAll();
        if (count($rows) > 0){
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' can not repeat');
            $this->addError($attribute,$message);
        }*/
    }

    public function retrieveData($index)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $allow_city = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('EMPLOY',id) as employdoc")->from("hr_employee")
            ->where("id=:id and city in ({$allow_city}) and table_type=1", array(':id'=>$index))->queryRow();
        if ($row){
            $this->no_of_attm['employ'] = $row['employdoc'];
            $arr = $this->getMyAttr();
            foreach ($arr as $key => $type){
                switch ($type){
                    case 1://原值
                        $value = $row[$key];
                        $value = $value===""?null:$value;
                        $this->$key = $value;
                        break;
                    case 2://日期
                        $this->$key = empty($row[$key])?null:General::toDate($row[$key]);
                        break;
                    case 3://数字
                        $this->$key = $row[$key]===null?null:floatval($row[$key]);
                        break;
                    default:
                }
            }
        }
        return true;
    }
/*
    public function parseWagesToArr($str){
        $arr = explode(",",$str);
        for($i=0;$i<count($arr);$i++){
            if(empty($arr[$i])){
                $arr[$i] = 0;
            }
        }
        return $arr;
    }
*/

    protected function saveStaff(&$connection){
        $sql = '';
        $audit= $this->audit;
        $city = Yii::app()->user->city();
        $allow_city = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
        $list = self::getSaveList();
        switch ($this->scenario) {
            case 'delete':
                $connection->createCommand()->delete("hr_employee","id=:id and city in ({$allow_city})", array(":id" =>$this->id));
                //删除员工记录
                $connection->createCommand()->delete('hr_employee_history', 'employee_id=:id',array(":id"=>$this->id));
                break;
            case 'new':
                $list["city"] = $city;
                $list["lcu"] = $uid;
                $connection->createCommand()->insert("hr_employee", $list);
                break;
            case 'edit':
                unset($list["city"]);
                $list["luu"] = $uid;
                $connection->createCommand()->update("hr_employee", $list, "id=:id and city in ({$allow_city})", array(":id" => $this->id));
                break;
        }

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->lenStr();
            Yii::app()->db->createCommand()->update('hr_employee', array(
                'code'=>$this->code
            ), 'id=:id', array(':id'=>$this->id));
        }

        //審核
        if($audit){
            //記錄
            Yii::app()->db->createCommand()->insert('hr_employee_history', array(
                "employee_id"=>$this->id,
                "status"=>"inset",
                "lcu"=>$uid,
                "lcd"=>date('Y-m-d H:i:s'),
            ));

            //發送郵件
            $this->sendEmail();
        }
        return true;
    }

	protected function sendEmail(){
        $description="员工录入 - ".$this->name;
        $subject="员工录入 - ".$this->name;
        $message="<p>员工编号：".$this->code."</p>";
        $message.="<p>员工姓名：".$this->name."</p>";
        $message.="<p>员工所在城市：".Yii::app()->user->city_name()."</p>";
        $message.="<p>员工职位：".DeptForm::getDeptToId($this->position)."</p>";
        $message.="<p>员工入职日期：".$this->entry_time."</p>";
        $email = new Email($subject,$message,$description);
        $email->addEmailToPrefix("ZG01");
        $email->sent();
    }

    public function readonly(){
        return $this->getScenario()=='view'||!in_array($this->staff_status,array(1,3));
    }
}
