<?php

class AuditLeaveForm extends CFormModel
{
    public $id;
    public $leave_code;
    public $employee_id;
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
    public $city;//請假員工所在的城市
    public $area_lcu;
    public $area_lcd;
    public $head_lcu;
    public $head_lcd;
    public $reject_cause;
    public $cost_num;//請假的工資倍率
    public $wage;//合約工資
    public $only = 1;//1：地區審核  2：總部審核


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
            'area_lcu'=>Yii::t('fete','area lcu'),
            'area_lcd'=>Yii::t('fete','area lcd'),
            'head_lcu'=>Yii::t('fete','head lcu'),
            'head_lcd'=>Yii::t('fete','head lcd'),
            'reject_cause'=>Yii::t('contract','Rejected Remark'),
            'wage'=>Yii::t('contract','Contract Pay'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id,employee_id,vacation_id,status,leave_cause,start_time,city,end_time,log_time,only','safe'),
            array('reject_cause','required',"on"=>"reject"),
        );
    }


    public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("*,docman$suffix.countdoc('LEAVE',id) as leavedoc")
            ->from("hr_employee_leave")->where("id=:id and city in ($city_allow)",array(":id"=>$index))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $employeeList = EmployeeForm::getEmployeeOneToId($row['employee_id']);
                $this->id = $row['id'];
                $this->leave_code = $row['leave_code'];
                $this->employee_id = $employeeList["name"];
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
                $this->city = $row['city'];
                $this->area_lcu = $row['area_lcu'];
                $this->area_lcd = $row['area_lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->reject_cause = $row['reject_cause'];
                $this->wage = $employeeList['wage'];
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
        $sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_employee_leave set
							z_index = :z_index, ";
                if($this->only == 1){
                    $sql.="area_lcu = :area_lcu, area_lcd = :area_lcd, ";
                }else{
                    $sql.="status = 4, head_lcu = :head_lcu, head_lcd = :head_lcd, ";
                }
                $sql.="luu = :luu
						where id = :id";
                break;
            case 'reject':
                $sql = "update hr_employee_leave set
							status = 3, 
							reject_cause = :reject_cause, 
							";
                if($this->only == 1){
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
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->only,PDO::PARAM_STR);

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

    //判斷輸入框能否修改
    public function getInputBool(){
        return true;
    }
    //獲取本月加班記錄
    public function getHistoryList(){
        $thisWork = Yii::app()->db->createCommand()->select("*")
            ->from("hr_employee_leave")->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($thisWork){
            $start_month = date("Y-m-01");
            $end_month = date("Y-m-d",strtotime("$start_month + 1 month - 1 day"));
            $historyList = array();
            $employeeList = EmployeeForm::getEmployeeOneToId($thisWork["employee_id"]);
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
