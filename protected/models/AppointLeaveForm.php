<?php

class AppointLeaveForm extends CFormModel
{
    public $id;
    public $leave_code;
    public $employee_id;
    public $employee_name;
    public $vacation_id;
    public $leave_cause;//請假原因
    public $leave_cost;//請假費用
    public $start_time;
    public $end_time;
    public $start_time_lg;
    public $end_time_lg;
    public $log_time;
    public $z_index;
    public $status;
    public $audit_remark;
    public $city;//請假員工所在的城市
    public $pers_lcu;
    public $pers_lcd;
    public $user_lcu;
    public $user_lcd;
    public $area_lcu;
    public $area_lcd;
    public $head_lcu;
    public $head_lcd;
    public $you_lcu;
    public $you_lcd;
    public $lcd;
    public $staff_type;//員工的辦公類型
    public $reject_cause;
    public $cost_num;//請假的工資倍率
    public $wage;//合約工資
    public $vacation_list;//倍率
    public $only = 1;//1:部門審核、2：主管、3：總監、4：你


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

    protected $appointList=false;

    public function attributeLabels()
    {
        return array(
            'leave_code'=>Yii::t('fete','Leave Code'),
            'vacation_id'=>Yii::t('fete','Leave Type'),
            'leave_cause'=>Yii::t('fete','Leave Cause'),
            'leave_cost'=>Yii::t('fete','Leave Cost'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'pers_lcu'=>Yii::t('fete','personnel lcu'),
            'pers_lcd'=>Yii::t('fete','personnel lcd'),
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
            array('id,leave_code,employee_id,vacation_id,status,leave_cause,start_time,start_time_lg,city,end_time,,end_time_lg,log_time,only,audit_remark,
            staff_type,employee_name,lcd','safe'),
            array('reject_cause','required',"on"=>"reject"),
            array('id','validateID'),
        );
    }

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("a.*")
            ->from("hr_employee_leave a")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            $this->employee_id = $row["employee_id"];
            $this->z_index = $row["z_index"];
            $this->appointList = AppointSetForm::getAppointSet($this->employee_id);
            if(!$this->appointList){
                $message = "该员工没有指定审核人，数据异常";
                $this->addError($attribute,$message);
            }elseif (!key_exists($this->z_index,$this->appointList)){
                $message = "加班单异常，请与管理员联系。ID:{$this->id}，z_index:{$this->z_index}，employee_id:{$this->employee_id}";
                $this->addError($attribute,$message);
            }else{
                $uid = Yii::app()->user->id;
                $auditUser = $this->appointList[$this->z_index];
                if($auditUser!=$uid){
                    $message = "审核人异常,审核人应该是：".$auditUser;
                    $this->addError($attribute,$message);
                }
            }
        }else{
            $message = "加班单不存在，数据异常";
            $this->addError($attribute,$message);
        }
    }

    public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.wage,b.staff_type,b.name as employee_name,b.city as s_city,docman$suffix.countdoc('LEAVE',a.id) as leavedoc")
            ->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.status in (1,3) and a.id=:id",array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->leave_code = $row['leave_code'];
                $this->employee_id = $row['employee_id'];
                $this->employee_name = $row['employee_name'];
                $this->staff_type = $row['staff_type'];
                $this->wage = $row['wage'];
                $this->vacation_id = $row['vacation_id'];
                $this->leave_cause = $row['leave_cause'];
                $this->leave_cost = $row['leave_cost'];
                $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->start_time_lg = $row['start_time_lg'];
                $this->end_time_lg = $row['end_time_lg'];
                $this->status = $row['status'];
                $this->city = $row['s_city'];
                $this->pers_lcu = isset($row['pers_lcu'])?$row['pers_lcu']:"";
                $this->pers_lcd = isset($row['pers_lcd'])?$row['pers_lcd']:"";
                $this->user_lcu = $row['user_lcu'];
                $this->user_lcd = $row['user_lcd'];
                $this->area_lcu = $row['area_lcu'];
                $this->area_lcd = $row['area_lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->you_lcu = $row['you_lcu'];
                $this->you_lcd = $row['you_lcd'];
                $this->lcd = $row['lcd'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['leave'] = $row['leavedoc'];
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
            $this->saveGoods($connection);
            $transaction->commit();
        }
        catch(Exception $e) {
            $transaction->rollback();
            throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
        }
    }

    protected function saveGoods(&$connection) {
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $sql = '';
        $only = $this->z_index;
        $userLcdList = array(
            10=>"pers_lcd",
            11=>"user_lcd",
            12=>"area_lcd",
            13=>"head_lcd",
            14=>"you_lcd",
        );
        $auditSql="";
        $clause=$userLcdList[$only]." = :".$userLcdList[$only].",";
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_employee_leave set
							z_index = :z_index,
							audit_remark = :audit_remark,
							 ";
                $only++;
                if(is_array($this->appointList)&&!key_exists($only,$this->appointList)){
                    $auditSql = "status = 4,";
                }
                $sql.=$clause.$auditSql."luu = :luu
						where id = :id";
                break;
            case 'reject':
                $sql = "update hr_employee_leave set
							status = 3, 
							reject_cause = :reject_cause, 
							audit_remark = :audit_remark, 
							";
                $sql.=$clause."luu = :luu
						where id = :id";
                break;
        }
        if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        if (strpos($sql,':audit_remark')!==false)
            $command->bindParam(':audit_remark',$this->audit_remark,PDO::PARAM_STR);
        if (strpos($sql,':reject_cause')!==false)
            $command->bindParam(':reject_cause',$this->reject_cause,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false){
            $command->bindParam(':z_index',$only,PDO::PARAM_STR);
            $this->z_index = $only;
        }
        /*總部審核start*/
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
        /*總部審核end*/

        if (strpos($sql,':pers_lcu')!==false)
            $command->bindParam(':pers_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':pers_lcd')!==false)
            $command->bindParam(':pers_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':user_lcu')!==false)
            $command->bindParam(':user_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':user_lcd')!==false)
            $command->bindParam(':user_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':area_lcu')!==false)
            $command->bindParam(':area_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':area_lcd')!==false)
            $command->bindParam(':area_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':head_lcu')!==false)
            $command->bindParam(':head_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':head_lcd')!==false)
            $command->bindParam(':head_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':you_lcu')!==false)
            $command->bindParam(':you_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':you_lcd')!==false)
            $command->bindParam(':you_lcd',date("Y-m-d"),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();


        //發送郵件
        $this->sendEmail();
        return true;
    }

    protected function sendEmail(){
        $email = new Email();
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_employee")
            ->where('id=:id', array(':id'=>$this->employee_id))->queryRow();
        $message="<p>请假编号：".$this->leave_code."</p>";
        $message.="<p>员工编号：".$row["code"]."</p>";
        $message.="<p>员工姓名：".$row["name"]."</p>";
        $message.="<p>员工城市：".General::getCityName($row["city"])."</p>";
        $message.="<p>请假时间：".$this->start_time." ~ ".$this->end_time."  (".$this->log_time."天)</p>";
        if($this->scenario == "audit"){
            if (is_array($this->appointList)&&key_exists($this->z_index,$this->appointList)){
                $key = $this->z_index-10;
                $description="请假单{$key}次审核 - ".$row["name"];
                $subject="请假单{$key}次审核 - ".$row["name"];
                $email->addEmailToLcu($this->appointList[$this->z_index]);
            }else{
                $description="请假单审核通过 - ".$row["name"];
                $subject="请假单审核通过 - ".$row["name"];
                $email->addEmailToStaffId($row["id"]);
            }
        }else{
            $description="请假单被拒絕 - ".$row["name"];
            $subject="请假单被拒絕 - ".$row["name"];
            $message.="<p>拒絕原因：".$this->reject_cause."</p>";
            $email->addEmailToStaffId($row["id"]);
        }
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->sent();
    }

    //計算員工的請假費用
    public function resetLeaveCost(){
        $startPm = $this->start_time_lg;
        $endPm = $this->end_time_lg;
        if($startPm == "AM"){
            $this->start_time.=" 9:00:00";
        }else{
            $this->start_time.=" 13:00:00";
        }
        if($endPm == "AM"){
            $this->end_time.=" 12:00:00";
        }else{
            $this->end_time.=" 18:00:00";
        }
        $employeeList = StaffFun::getEmployeeOneToId($this->employee_id);
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
    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }
    //獲取假期的倍率
    public function getMuplite(){
        $id = $this->vacation_id;
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("hr_vacation")->where("id='$id'")->queryRow();
        if($rows){
            $sub_multiple = floatval($rows["sub_multiple"])/100;
        }else{
            $sub_multiple = 1.5;
        }
        return $sub_multiple;
    }
    //獲取員工工作日
    public function getUserWorkDay(){
        $dayNum = $this->staff_type == "Office"?22:26;
        return $dayNum;
    }
    //獲取本月加班記錄
    public function getHistoryList(){
        $thisWork = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_leave")->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($thisWork){
            $start_month = date("Y-m-01");
            $end_month = date("Y-m-d",strtotime("$start_month + 1 month - 1 day"));
            $historyList = array();
            $employeeList = StaffFun::getEmployeeOneToId($thisWork["employee_id"]);
            $workList = Yii::app()->db->createCommand()->select("*")
                ->from("hr_employee_leave")->where("employee_id=:id and status = 4 and start_time>='$start_month' and start_time<='$end_month'",
                    array(":id"=>$thisWork["employee_id"]))->queryAll();
            foreach ($workList as $list){
                array_push($historyList,array(
                    "employee_code"=>$employeeList["code"],
                    "employee_name"=>$employeeList["name"],
                    "start_time"=>date("Y/m/d",strtotime($list['start_time'])),
                    "end_time"=>date("Y/m/d",strtotime($list['end_time'])),
                    "log_time"=>$list["log_time"]."天",
                ));
            }
            return $historyList;
        }
        return array();
    }
}
