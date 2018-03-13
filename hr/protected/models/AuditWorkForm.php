<?php

class AuditWorkForm extends CFormModel
{
    public $id;
    public $work_code;
    public $employee_id;
    public $employee_name;
    public $work_type;
    public $city;
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
    public $audit_remark;
    public $user_lcu;
    public $user_lcd;
    public $area_lcu;
    public $area_lcd;
    public $head_lcu;
    public $head_lcd;
    public $reject_cause;
    public $lcd;
    public $cost_num;//節假日的工資倍率
    public $wage;//合約工資
    public $only = 1;//  3：領導審核   1：地區審核  2：總部審核
    public $bool_cost = 1;//是否支付加班費用  1：支付  0：不支付


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
            'bool_cost'=>Yii::t('fete','Bool Work Cost'),
            'head_lcd'=>Yii::t('fete','Bool Work Cost'),
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
            array('id,employee_id,work_type,work_address,status,work_cause,start_time,end_time,log_time,only,audit_remark,employee_name,bool_cost,city,lcd','safe'),
            array('work_type','required','on'=>array("audit")),
            array('work_type','validateWorkType','on'=>array("audit")),
            array('work_cause','required','on'=>array("audit")),
            array('work_address','required','on'=>array("audit")),
            array('start_time','required','on'=>array("audit")),
            array('end_time','required','on'=>array("audit")),
            array('log_time','required','on'=>array("audit")),
            array('end_time','validateTime','on'=>array("audit")),
            array('log_time','numerical','allowEmpty'=>true,'integerOnly'=>true,'on'=>array("audit")),
            array('reject_cause','required',"on"=>"reject"),
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
        $city = Yii::app()->user->city();
        $staff_id = BindingForm::getEmployeeIdToUsername();
        $suffix = Yii::app()->params['envSuffix'];
        if($this->only == 2){
            $sql = "a.id=:id and b.city in ($city_allow) and b.id !=$staff_id";
        }else{
            $sql = "a.id=:id and b.city = '$city' and b.id !=$staff_id";
        }
        $rows = Yii::app()->db->createCommand()->select("a.*,b.wage,b.staff_type,b.city AS s_city,b.name as employee_name,docman$suffix.countdoc('WORKEM',a.id) as workemdoc")
            ->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where($sql,array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->work_code = $row['work_code'];
                $this->employee_name = $row['employee_id'];
                $this->employee_id = $row['employee_name'];
                $this->wage = $row['wage'];
                $this->work_type = $row['work_type'];
                $this->work_cause = $row['work_cause'];
                $this->work_address = $row['work_address'];
                $this->work_cost = $row['work_cost'];
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
                $this->area_lcd = $row['area_lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->lcd = $row['lcd'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['workem'] = $row['workemdoc'];
                break;
            }
        }
        return true;
    }

    //驗證員工是不是經理級別的審核流程
    public function validateManagerToEmployeeId($employeeId = ""){
        $rows = Yii::app()->db->createCommand()->select("b.manager")->from("hr_employee a")
            ->leftJoin("hr_dept b","b.id = a.position")->where("a.id=$employeeId")->queryRow();
        if($rows){
            if(!empty($rows["manager"])){
                return true;
            }
        }
        return false;
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
        $sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_employee_work set
							z_index = :z_index,
							audit_remark = :audit_remark,
							 ";
                $this->z_index = $this->only;
                if($this->only == 1){
                    $sql.="area_lcu = :area_lcu, area_lcd = :area_lcd, ";
                }elseif($this->only == 3){
                    $z_index = AuditConfigForm::getCityAuditToCode($this->city);
                    $bool = $this->validateManagerToEmployeeId($this->employee_name);
                    $this->z_index = ($z_index==2||$bool)?1:0;
                    $sql.="user_lcu = :user_lcu, user_lcd = :user_lcd, ";
                }else{
                    //總部審核
                    $this->resetWorkCost();//計算員工的工資
                    $sql.="work_type = :work_type, 
							work_cause = :work_cause, 
							work_address = :work_address, 
							work_cost = :work_cost, 
							start_time = :start_time, 
							end_time = :end_time, 
							log_time = :log_time, ";
                    $sql.="status = 4, head_lcu = :head_lcu, head_lcd = :head_lcd, ";
                }
                $sql.="luu = :luu
						where id = :id";
                break;
            case 'reject':
                $sql = "update hr_employee_work set
							status = 3, 
							reject_cause = :reject_cause, 
							audit_remark = :audit_remark, 
							";
                if($this->only == 1){
                    $sql.="area_lcu = :area_lcu, area_lcd = :area_lcd, ";
                }elseif($this->only == 3){
                    $sql.="area_lcu = :area_lcu, area_lcd = :area_lcd, ";
                }else{
                    $sql.="head_lcu = :head_lcu, head_lcd = :head_lcd, ";
                }
                $sql.="luu = :luu
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
        if (strpos($sql,':reject_cause')!==false)
            $command->bindParam(':reject_cause',$this->reject_cause,PDO::PARAM_STR);
        if (strpos($sql,':audit_remark')!==false)
            $command->bindParam(':audit_remark',$this->audit_remark,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_STR);

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
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();
        return true;
    }

    //計算員工的加班費用
    public function resetWorkCost(){
        if($this->bool_cost == 0){ //不支付加班工資
            $wage = 0;
        }else{
            $employeeList = EmployeeForm::getEmployeeOneToId($this->employee_name);
            $wage = floatval($employeeList["wage"]);
        }
        switch ($this->work_type){
            case 2:
                if($this->cost_num == 1){
                    $this->cost_num = 3;
                }else{
                    $this->cost_num = 2;
                }
                $this->work_cost = ($wage/21.75)*intval($this->log_time)*intval($this->cost_num);
                break;
            case 1:
                $this->work_cost = ($wage/(21.75*8))*intval($this->log_time)*2;
                $this->start_time .=" ".$this->hours;
                $this->end_time .=" ".$this->hours_end;
                break;
            default:
                $this->work_cost = ($wage/(21.75*8))*intval($this->log_time)*1.5;
                $this->start_time .=" ".$this->hours;
                $this->end_time .=" ".$this->hours_end;
        }
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        if($this->only == 2&&$this->getScenario()!='view'){
            return false;
        }else{
            return true;
        }
    }

    //支付不支付列表
    public function getPayList(){
        return array(Yii::t("fete","Do not pay"),Yii::t("fete","pay"));
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

    //獲取本月加班記錄
    public function getHistoryList(){
        $thisWork = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_work")->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($thisWork){
            $start_month = date("Y-m-01");
            $end_month = date("Y-m-d",strtotime("$start_month + 1 month - 1 day"));
            $historyList = array();
            $employeeList = EmployeeForm::getEmployeeOneToId($thisWork["employee_id"]);
            $workList = Yii::app()->db->createCommand()->select("*")
                ->from("hr_employee_work")->where("employee_id=:id and status = 4 and start_time>='$start_month' and start_time<='$end_month'",
                    array(":id"=>$thisWork["employee_id"]))->queryAll();
            foreach ($workList as $list){
                if($list['work_type'] == 2){
                    $list['start_time'] = date("Y/m/d",strtotime($list['start_time']));
                    $list['end_time'] = date("Y/m/d",strtotime($list['end_time']));
                    $dayStr ="天";
                }else{
                    $list['start_time'] = date("Y/m/d H:i:s",strtotime($list['start_time']));
                    $list['end_time'] = date("Y/m/d H:i:s",strtotime($list['end_time']));
                    $dayStr ="小時";
                }
                array_push($historyList,array(
                    "employee_code"=>$employeeList["code"],
                    "employee_name"=>$employeeList["name"],
                    "start_time"=>$list["start_time"],
                    "end_time"=>$list["end_time"],
                    "log_time"=>$list["log_time"].$dayStr,
                ));
            }
            return $historyList;
        }
        return array();
    }
}
