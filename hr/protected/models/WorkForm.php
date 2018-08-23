<?php

class WorkForm extends CFormModel
{
	public $id;
	public $work_code;
	public $employee_id;
	public $work_type;
	public $work_cause;//加班原因
    public $work_cost;//加班費用
	public $work_address;
	public $hours="08:00";//開始時間的小時
	public $hours_end="08:00";//開始時間的小時
	public $start_time;
	public $end_time;
	public $log_time;
	public $z_index;
	public $status;
	public $city;
    public $audit_remark;
    public $user_lcu;
    public $user_lcd;
	public $area_lcu;
	public $area_lcd;
	public $head_lcu;
	public $head_lcd;
    public $you_lcu;
    public $you_lcd;
	public $reject_cause;
	public $cost_num;//節假日的工資倍率
	public $audit = false;//是否需要審核
    public $wage;//合約工資
    public $lcd;


    public $no_of_attm = array(
        'workem'=>0
    );
    public $docType = 'WORKEM';
    public $docMasterId = array(
        'workem'=>0
    );
    public $files;
    public $removeFileId = array(
        'workem'=>0
    );
	public function attributeLabels()
	{
		return array(
            'work_code'=>Yii::t('fete','Work Code'),
            'work_type'=>Yii::t('fete','Work Type'),
            'work_address'=>Yii::t('fete','Work Address'),
            'work_cause'=>Yii::t('fete','Work Cause'),
            'work_cost'=>Yii::t('fete','Work Cost'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'user_lcu'=>Yii::t('fete','user lcu'),
            'user_lcd'=>Yii::t('fete','user lcd'),
            'area_lcu'=>Yii::t('fete','area lcu'),
            'area_lcd'=>Yii::t('fete','area lcd'),
            'head_lcu'=>Yii::t('fete','head lcu'),
            'head_lcd'=>Yii::t('fete','head lcd'),
            'you_lcu'=>Yii::t('fete','you lcu'),
            'you_lcd'=>Yii::t('fete','you lcd'),
            'audit_remark'=>Yii::t('fete','Audit Remark'),
            'reject_cause'=>Yii::t('contract','Rejected Remark'),
            'wage'=>Yii::t('contract','Contract Pay'),
            'lcd'=>Yii::t('fete','apply for time'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,work_type,work_address,status,work_cause,start_time,end_time,log_time,hours,hours_end,lcd,city','safe'),
            array('work_type','required','on'=>array("new","edit","audit")),
            array('work_type','validateWorkType','on'=>array("new","edit","audit")),
            array('work_cause','required','on'=>array("new","edit","audit")),
            array('work_address','required','on'=>array("new","edit","audit")),
            array('start_time','required','on'=>array("new","edit","audit")),
            array('end_time','required','on'=>array("new","edit","audit")),
            array('log_time','required','on'=>array("new","edit","audit")),
            array('end_time','validateTime','on'=>array("new","edit","audit")),
            array('log_time','numerical', 'min'=>0.5, 'max'=>8,'allowEmpty'=>true,'integerOnly'=>false,'on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}
    public function validateTime($attribute, $params){
        if(!empty($this->end_time)&&!empty($this->start_time)){
            $date2 = strtotime($this->start_time);
            $date1 = strtotime($this->end_time);
            if($date2>$date1){
                $message = Yii::t('fete','Start time cannot be greater than end time');
                $this->addError($attribute,$message);
            }else{
                if($this->log_time <= 0){
                    $message = Yii::t('fete','Start time cannot be greater than end time');
                    $this->addError($attribute,$message);
                }
            }
        }
    }


    public function validateWorkType($attribute, $params){
        $city = Yii::app()->user->city();
	    if($this->work_type == 2){
            $rows = Yii::app()->db->createCommand()->select("cost_num")->from("hr_fete")
                ->where("start_time<=:start_time and end_time >=:end_time and (city='$city' or only='default')", array(':start_time'=>$this->start_time,':end_time'=>$this->end_time))->queryRow();
            if($rows){
                $this->cost_num = $rows["cost_num"];
            }else{
                $message = Yii::t('fete','This time period is not a legal holiday, please contact the administrator');
                $this->addError($attribute,$message);
            }
        }else if($this->work_type == 1){
	        $week = date("w",strtotime($this->start_time));
            if($week == 6 || $week == 0){
                //是週末
            }else{
                $message = Yii::t('fete','This time period is not a weekend');
                $this->addError($attribute,$message);
            }
        }
    }

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.*,b.wage,b.city as s_city,b.staff_type,b.name as employee_name,docman$suffix.countdoc('WORKEM',a.id) as workemdoc")
            ->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ($city_allow)",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->work_code = $row['work_code'];
                $this->employee_id = $row['employee_name'];
                $this->wage = $row['wage'];
                $this->work_type = $row['work_type'];
                $this->work_cause = $row['work_cause'];
                $this->work_address = $row['work_address'];
                $this->city = $row['s_city'];
                if ($this->work_type == 2){
                    $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                    $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                }else{
                    $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                    $this->hours = date("H:i",strtotime($row['start_time']));
                    $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                    $this->hours_end = date("H:i",strtotime($row['end_time']));
                }
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->status = $row['status'];
                $this->user_lcu = $row['user_lcu'];
                $this->user_lcd = $row['user_lcd'];
                $this->area_lcu = $row['area_lcu'];
                $this->work_cost = $row['work_cost'];
                $this->area_lcd = $row['area_lcd'];
                $this->lcd = $row['lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->you_lcu = $row['you_lcu'];
                $this->you_lcd = $row['you_lcd'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['workem'] = $row['workemdoc'];
                break;
			}
		}
		return true;
	}

	//根據加班id獲取加班信息
	public function getWorkListToWorkId($work_id){
        $connection = Yii::app()->db;
        $sql = "select a.*,b.name AS employee_name,b.code AS employee_code ,b.company_id,b.department
                from hr_employee_work a LEFT JOIN hr_employee b ON a.employee_id = b.id
                where a.id =$work_id
			";
        $records = $connection->createCommand($sql)->queryRow();
        if($records){
            $company = CompanyForm::getCompanyToId($records["company_id"]);
            $records["company_name"]=$company["name"];
            $records["dept_name"]=DeptForm::getDeptToId($records["department"]);
            return $records;
        }else{
            return false;
        }
    }

    //獲取員工的簽名信息
    public function getSignatureToStaffId($staff_id,$bool=true){
	    if($bool){
            $row = Yii::app()->db->createCommand()->select("*")
                ->from("hr_binding")->where("employee_id=:employee_id",array(":employee_id"=>$staff_id))->queryRow();
        }else{
            $row = array("user_id"=>$staff_id);
        }
        if($row){
            $suffix = Yii::app()->params['envSuffix'];
            $user_id = $row["user_id"];
            $field_blob = Yii::app()->db->createCommand()->select("field_blob")
                ->from("security$suffix.sec_user_info")->where("username=:username and field_id='signature'",array(":username"=>$user_id))->queryRow();
            if($field_blob){
                $field_blob = $field_blob["field_blob"];
                $field_value = Yii::app()->db->createCommand()->select("field_value")
                    ->from("security$suffix.sec_user_info")->where("username=:username and field_id='signature_file_type'",array(":username"=>$user_id))->queryRow();
                if($field_value){
                    $field_value = $field_value["field_value"];
                    if(!empty($field_value)&&!empty($field_blob)){
                        return array(
                            "field_blob"=>$field_blob,
                            //"field_blob"=>base64_decode($field_blob),
                            "field_value"=>$field_value,
                        );
                    }
                }
            }
        }
        return false;
    }

    //刪除驗證
    public function deleteValidate(){
        return true;
    }

    //獲取假期的倍率
    public function getMuplite(){
        switch ($this->work_type){
            case 2:
                $city = Yii::app()->user->city();
                $rows = Yii::app()->db->createCommand()->select("cost_num")->from("hr_fete")
                    ->where("start_time<=:start_time and end_time >=:end_time and (city='$city' or only='default')",
                        array(':start_time'=>$this->start_time,':end_time'=>$this->end_time))->queryRow();
                if($rows){
                    if($rows["cost_num"] == 1){
                        $this->cost_num = 3;
                    }else{
                        $this->cost_num = 2;
                    }
                    return $this->cost_num;
                }else{
                    return "1.5";
                }
                break;
            case 1:
                return 2;
                break;
            default:
                return 1.5;
        }
    }
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
            $this->updateDocman($connection,'WORKEM');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

    protected function updateDocman(&$connection, $doctype) {
        if ($this->scenario=='new') {
            $docidx = strtolower($doctype);
            if ($this->docMasterId[$docidx] > 0) {
                $docman = new DocMan($doctype,$this->id,get_class($this));
                $docman->masterId = $this->docMasterId[$docidx];
                $docman->updateDocId($connection, $this->docMasterId[$docidx]);
            }
            $this->scenario = "edit";
        }
    }

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from hr_employee_work where id = :id";
                break;
            case 'cancel':
                $sql = "delete from hr_employee_work where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_employee_work(
							employee_id,work_type,work_cause, work_address, start_time, end_time, log_time, work_cost, city, status, z_index, lcu
						) values (
							:employee_id,:work_type,:work_cause, :work_address, :start_time, :end_time, :log_time, :work_cost, :city, :status, :z_index, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_employee_work set
							work_type = :work_type, 
							work_cause = :work_cause, 
							work_address = :work_address, 
							work_cost = :work_cost, 
							start_time = :start_time, 
							end_time = :end_time, 
							log_time = :log_time, 
							city = :city, 
							status = :status, 
							reject_cause = '', 
							z_index = :z_index,
							lcd = :lcd,
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $employeeList = $this->getEmployeeOneToUser();
        $this->employee_id = $employeeList["id"];
        $city = $employeeList["city"];
        $this->resetWorkCost();//計算員工的工資

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //employee_id,work_type,work_cause, work_address, start_time, end_time, log_time, work_cost, city, lcu
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':work_type')!==false)
            $command->bindParam(':work_type',$this->work_type,PDO::PARAM_STR);
        if (strpos($sql,':work_cause')!==false)
            $command->bindParam(':work_cause',$this->work_cause,PDO::PARAM_STR);
        if (strpos($sql,':work_address')!==false)
            $command->bindParam(':work_address',$this->work_address,PDO::PARAM_STR);
        if (strpos($sql,':work_cost')!==false)
            $command->bindParam(':work_cost',$this->work_cost,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':log_time')!==false)
            $command->bindParam(':log_time',$this->log_time,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false){
            $z_index = AuditConfigForm::getCityAuditToCode($this->employee_id);
            $this->z_index = $z_index;
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_STR);
        }

        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            Yii::app()->db->createCommand()->update('hr_employee_work', array(
                'work_code'=>"J".$this->lenStr($this->id)
            ), 'id=:id', array(':id'=>$this->id));
        }

        //發送郵件
        $this->sendEmail();
		return true;
	}

    protected function sendEmail(){
        if($this->audit){
            $assList=array(
                1=>"ZA08",
                2=>"ZE05",
                3=>"ZG04",
                4=>"ZC10",
            );
            $email = new Email();
            $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
                ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
            if($this->work_type == 2){
                $dayStr ="天";
            }else{
                $dayStr ="小時";
            }
            $description="新的加班单 - ".$row["name"];
            $subject="新的加班单 - ".$row["name"];
            $message="<p>加班编号：".$this->work_code."</p>";
            $message.="<p>员工编号：".$row["code"]."</p>";
            $message.="<p>员工姓名：".$row["name"]."</p>";
            $message.="<p>加班时间：".$this->start_time." ~ ".$this->end_time."  (".$this->log_time."$dayStr)</p>";
            $message.="<p>加班原因：".$this->work_cause."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $assType = $assList[$this->z_index];
            switch ($this->z_index){
                case 1:
                    $email->addEmailToPrefixAndPoi($assType,$row["department"]);
                    break;
                case 2:
                    $email->addEmailToPrefixAndOnlyCity($assType,$row["city"]);
                    break;
                default:
                    $email->addEmailToPrefixAndCity($assType,$row["city"]);
            }
            $email->sent();
        }
    }

	//計算員工的加班費用
    public function resetWorkCost(){
        if($this->audit){
            $this->status = 1;
        }else{
            $this->status = 0;
        }
        $employeeList = EmployeeForm::getEmployeeOneToId($this->employee_id);
        $wage = floatval($employeeList["wage"]);
        switch ($this->work_type){
            case 2:
                if($this->cost_num == 1){
                    $this->cost_num = 3;
                }else{
                    $this->cost_num = 2;
                }
                $this->work_cost = ($wage/21.75)*floatval($this->log_time)*intval($this->cost_num);
                break;
            case 1:
                $this->work_cost = ($wage/(21.75*8))*floatval($this->log_time)*2;
                $this->start_time .=" ".$this->hours;
                $this->end_time .=" ".$this->hours_end;
                break;
            default:
                $this->work_cost = ($wage/(21.75*8))*floatval($this->log_time)*1.5;
                $this->start_time .=" ".$this->hours;
                $this->end_time .=" ".$this->hours_end;
        }
    }

    private function lenStr($id){
        $code = strval($id);
        $str = "4";
        for($i = 0;$i < 5-strlen($code);$i++){
            $str.="0";
        }
        $str .= $code;
        return $str;
    }

	//獲取當前用戶的員工id
	public function getEmployeeIdToUser(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            return $rows["employee_id"];
        }
        return "";
    }
    //獲取當前用戶的員工id
    public function getEmployeeOneToUser(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("b.id,b.city")->from("hr_binding a")->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where('user_id=:user_id',array(':user_id'=>$uid))->queryRow();
        if ($rows){
            return $rows;
        }
        return "";
    }

	//判斷輸入框能否修改
	public function getInputBool(){
        if($this->scenario == "view"){
            return true;
        }
        if($this->status == 1 || $this->status == 2 || $this->status == 4){
            return true;
        }
        return false;
    }
}
