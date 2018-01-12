<?php

class LeaveForm extends CFormModel
{
	public $id;
	public $leave_code;
	public $employee_id;
	public $vacation_id;
	public $leave_cause;//加班原因
    public $leave_cost;//加班費用
	public $start_time;
	public $start_time_lg;
	public $end_time;
	public $end_time_lg;
	public $log_time;
	public $z_index;
	public $status;
	public $audit_remark;
    public $user_lcu;
    public $user_lcd;
	public $area_lcu;
	public $area_lcd;
	public $head_lcu;
	public $head_lcd;
	public $reject_cause;
	public $vacation_list;//倍率
	public $city;
	public $audit = false;//是否需要審核


    public $no_of_attm = array(
        'leave'=>0
    );
    public $docType = 'LEAVE';
    public $docMasterId = array(
        'leave'=>0
    );
    public $files;
    public $removeFileId = array(
        'leave'=>0
    );

	public function attributeLabels()
	{
		return array(
            'leave_code'=>Yii::t('fete','Leave Code'),
            'vacation_id'=>Yii::t('fete','Leave Type'),
            'leave_cause'=>Yii::t('fete','Leave Cause'),
            'leave_cost'=>Yii::t('fete','Leave Cost'),
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
            'audit_remark'=>Yii::t('fete','Audit Remark'),
            'reject_cause'=>Yii::t('contract','Rejected Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id,vacation_id,city,status,leave_cause,start_time,end_time,start_time_lg,end_time_lg,log_time','safe'),
            array('employee_id','validateUser','on'=>array("new","edit","audit")),
            array('vacation_id','required','on'=>array("new","edit","audit")),
            array('leave_cause','required','on'=>array("new","edit","audit")),
            array('log_time','required','on'=>array("new","edit","audit")),
            array('start_time','required','on'=>array("new","edit","audit")),
            array('end_time','required','on'=>array("new","edit","audit")),
            array('vacation_id','validateLeaveType','on'=>array("new","edit","audit")),
            array('log_time','validateLogTime','on'=>array("new","edit","audit")),
            array('log_time','numerical','allowEmpty'=>true,'integerOnly'=>false,'on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}

	public function validateUser($attribute, $params){
        if(Yii::app()->user->validFunction('ZR06')&&empty($this->employee_id)){
            $message = Yii::t('contract','Employee Name').Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
    }
	//驗證請假類型
    public function validateLeaveType($attribute, $params){
	    $id = $this->vacation_id;
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_vacation")->where("id='$id'")->queryRow();
        if($rows){
            $this->vacation_list = $rows;
            if($rows["log_bool"]  == 1){
                if(floatval($this->log_time) > floatval($rows["max_log"])){
                    $message = Yii::t('fete','Log Date')."不能大于".$rows["max_log"]."天";
                    $this->addError($attribute,$message);
                }
            }
        }else{
            $message = Yii::t('fete','Leave Type').Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
    }
	//驗證時間週期
    public function validateLogTime($attribute, $params){
        if(!empty($this->log_time)){
            if(!is_numeric($this->log_time)){
                $message = Yii::t('fete','Log Date')."必須为数字";
                $this->addError($attribute,$message);
            }else{
                if (strpos($this->log_time,'.')!==false){
                    //含有小數
                    $float = end(explode(".",$this->log_time));
                    $float = intval($float);
                    if($float !== 5 && $float !== 0){
                        $message = Yii::t('fete','Log Date')."的小数必须为0.5";
                        $this->addError($attribute,$message);
                    }
                }
            }
        }
    }

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $uid = $this->getEmployeeIdToUser();
		$rows = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('LEAVE',id) as leavedoc")
            ->from("hr_employee_leave")->where("id=:id and employee_id=:employee_id",array(":id"=>$index,":employee_id"=>$uid))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
			    $employeeList = EmployeeForm::getEmployeeOneToId($row['employee_id']);
                $this->id = $row['id'];
                $this->leave_code = $row['leave_code'];
                $this->employee_id = $employeeList["name"];
                $this->vacation_id = $row['vacation_id'];
                $this->leave_cause = $row['leave_cause'];
                $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->start_time_lg = $row['start_time_lg'];
                $this->end_time_lg = $row['end_time_lg'];
                $this->status = $row['status'];
                $this->user_lcu = $row['user_lcu'];
                $this->user_lcd = $row['user_lcd'];
                $this->area_lcu = $row['area_lcu'];
                $this->area_lcd = $row['area_lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->city = $row['city'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['leave'] = $row['leavedoc'];
                break;
			}
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        return true;
    }

    //獲取當前城市的所有請假類型
    public function getLeaveTypeList($city){
        if(empty($city)){
            $city = Yii::app()->user->city();
        }
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_vacation")->where("city='$city' OR only='default'")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
            $this->updateDocman($connection,'LEAVE');
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
                $sql = "delete from hr_employee_leave where id = :id";
                break;
            case 'cancel':
                $sql = "delete from hr_employee_leave where id = :id";
                break;
            case 'new':
                $sql = "insert into hr_employee_leave(
							employee_id,vacation_id,leave_cause, start_time_lg, end_time_lg, start_time, end_time, log_time, leave_cost, city, status, lcu
						) values (
							:employee_id,:vacation_id,:leave_cause, :start_time_lg, :end_time_lg, :start_time, :end_time, :log_time, :leave_cost, :city, :status, :lcu
						)";
                break;
            case 'edit':
                $sql = "update hr_employee_leave set
							vacation_id = :vacation_id, 
							leave_cause = :leave_cause, 
							leave_cost = :leave_cost, 
							start_time_lg = :start_time_lg, 
							end_time_lg = :end_time_lg, 
							start_time = :start_time, 
							end_time = :end_time, 
							log_time = :log_time, 
							city = :city, 
							status = :status, 
							reject_cause = '', 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;//ZR06
        if(!Yii::app()->user->validFunction('ZR06')){
            $this->employee_id = $this->getEmployeeIdToUser();
        }
        $this->resetLeaveCost();//計算員工的工資

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':vacation_id')!==false)
            $command->bindParam(':vacation_id',$this->vacation_id,PDO::PARAM_STR);
        if (strpos($sql,':leave_cause')!==false)
            $command->bindParam(':leave_cause',$this->leave_cause,PDO::PARAM_STR);
        if (strpos($sql,':leave_cost')!==false)
            $command->bindParam(':leave_cost',$this->leave_cost,PDO::PARAM_STR);
        if (strpos($sql,':start_time_lg')!==false)
            $command->bindParam(':start_time_lg',$this->start_time_lg,PDO::PARAM_STR);
        if (strpos($sql,':end_time_lg')!==false)
            $command->bindParam(':end_time_lg',$this->end_time_lg,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':log_time')!==false)
            $command->bindParam(':log_time',$this->log_time,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            Yii::app()->db->createCommand()->update('hr_employee_leave', array(
                'leave_code'=>"Q".$this->lenStr($this->id)
            ), 'id=:id', array(':id'=>$this->id));
        }
		return true;
	}

	//獲取綁定員工的列表
    public function getBindEmployeeList(){
        $arr = array();
        $rows = Yii::app()->db->createCommand()->select("a.employee_id as id,b.name as name")->from("hr_binding a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }


    //獲取上午下午列表
    public function getAMPMList(){
        return array(
            "AM"=>Yii::t("fete","AM"),
            "PM"=>Yii::t("fete","PM")
        );
    }

	//計算員工的請假費用
    public function resetLeaveCost(){
        if($this->audit){
            $this->status = 1;
        }else{
            $this->status = 0;
        }
        $employeeList = EmployeeForm::getEmployeeOneToId($this->employee_id);
        $wage = floatval($employeeList["wage"]);
        $vacationList = $this->vacation_list;
        if($vacationList["sub_bool"] == 1){ //
            $dayNum = $employeeList["staff_type"] == "Office"?22:26;
            $sub_multiple = floatval($vacationList["sub_multiple"])/100;
            $this->leave_cost = ($wage/$dayNum)*floatval($this->log_time)*$sub_multiple;
        }else{
            $this->leave_cost = 0;
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
