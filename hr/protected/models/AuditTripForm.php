<?php

class AuditTripForm extends CFormModel
{
    public $id;
    public $trip_code;
    public $employee_id;
    public $employee_code="";
    public $employee_name="";
    public $city="";

    public $trip_cause;
    public $trip_cost;
    public $trip_address;
    public $start_time;
    public $start_time_lg;
    public $end_time;
    public $end_time_lg='PM';
    public $log_time=0;
    public $z_index=4;//1:部門審核、2：主管、3：總監、4：你
    public $status=0;
    public $audit_remark;
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
    public $reject_cause;
    public $lcd;
    public $audit = false;//是否需要審核

    public $addTime=array(
        array('id'=>0,
            'trip_id'=>0,
            'start_time'=>'',
            'start_time_lg'=>'AM',
            'end_time'=>'',
            'end_time_lg'=>'PM',
            'uflag'=>'N',
        ),
    );

    public $addMoney=array(
        array('id'=>0,
            'trip_id'=>0,
            'money_set_id'=>'',
            'trip_money'=>'',
            'uflag'=>'N',
        ),
    );

    public $no_of_attm = array(
        'trip'=>0
    );
    public $docType = 'TRIP';
    public $docMasterId = array(
        'trip'=>0
    );
    public $files;
    public $removeFileId = array(
        'trip'=>0
    );

	public function attributeLabels()
	{
        if(in_array($this->status,array(4,5))){
            $reject_cause = Yii::t('contract','cancel cause');
        }else{
            $reject_cause = Yii::t('contract','Rejected Remark');
        }
        return array(
            'trip_code'=>Yii::t('fete','trip code'),
            'addTime'=>Yii::t('fete','trip date'),
            'employee_id'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'trip_cause'=>Yii::t('fete','trip cause'),
            'trip_cost'=>Yii::t('fete','trip cost'),
            'trip_address'=>Yii::t('fete','trip address'),
            'log_time'=>Yii::t('fete','Log Date'),
            'status'=>Yii::t('contract','Status'),
            'pers_lcu'=>Yii::t('fete','personnel lcu'),
            'pers_lcd'=>Yii::t('fete','personnel lcd'),
            'user_lcu'=>Yii::t('fete','user lcu'),
            'user_lcd'=>Yii::t('fete','user lcd'),
            'area_lcu'=>Yii::t('fete','area lcu'),
            'area_lcd'=>Yii::t('fete','area lcd'),
            'head_lcu'=>Yii::t('fete','trip lcu'),
            'head_lcd'=>Yii::t('fete','trip lcd'),
            'you_lcu'=>Yii::t('fete','you lcu'),
            'you_lcd'=>Yii::t('fete','you lcd'),
            'audit_remark'=>Yii::t('fete','Audit Remark'),
            'reject_cause'=>$reject_cause,
            'wage'=>Yii::t('contract','Contract Pay'),
            'lcd'=>Yii::t('fete','apply for time'),
            'state'=>Yii::t('contract','Status'),
            'result_id'=>Yii::t('fete','trip result'),
            'result_text'=>Yii::t('fete','trip result text'),
            'money_set_id'=>Yii::t('fete','project name'),
            'trip_money'=>Yii::t('fete','trip amount'),
            'addMoney'=>Yii::t('fete','trip money'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,addTime,trip_code,trip_cost,trip_address,employee_id,employee_code,employee_name,city,status,trip_cause,start_time,end_time,start_time_lg,end_time_lg,log_time,lcd,reject_cause','safe'),
            array('employee_id,trip_cost,trip_address','required','on'=>array("new","edit","audit")),
            array('files, removeFileId, docMasterId','safe'),
		);
	}


	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()
            ->select("a.*,b.wage,b.city as s_city,b.staff_type,b.code as employee_code,b.name as employee_name,docman$suffix.countdoc('TRIP',a.id) as tripdoc")
            ->from("hr_employee_trip a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->where("a.id=:id and b.city in ({$city_allow})",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->trip_code = $row['trip_code'];
                $this->employee_id = $row['employee_id'];
                $this->employee_code = $row['employee_code'];
                $this->employee_name = $row['employee_name'];
                $this->trip_cause = $row['trip_cause'];
                $this->trip_address = $row['trip_address'];
                $this->trip_cost = floatval($row['trip_cost']);
                $this->start_time = date("Y/m/d",strtotime($row['start_time']));
                $this->end_time = date("Y/m/d",strtotime($row['end_time']));
                $this->log_time = $row['log_time'];
                $this->z_index = $row['z_index'];
                $this->start_time_lg = $row['start_time_lg'];
                $this->end_time_lg = $row['end_time_lg'];
                $this->status = $row['status'];
                $this->pers_lcu = isset($row['pers_lcu'])?$row['pers_lcu']:"";
                $this->pers_lcd = isset($row['pers_lcd'])?$row['pers_lcd']:"";
                $this->user_lcu = $row['user_lcu'];
                $this->user_lcd = $row['user_lcd'];
                $this->area_lcu = $row['area_lcu'];
                $this->area_lcd = $row['area_lcd'];
                $this->lcd = $row['lcd'];
                $this->head_lcu = $row['head_lcu'];
                $this->head_lcd = $row['head_lcd'];
                $this->you_lcu = $row['you_lcu'];
                $this->you_lcd = $row['you_lcd'];
                $this->city = $row['s_city'];
                $this->audit_remark = $row['audit_remark'];
                $this->reject_cause = $row['reject_cause'];
                $this->no_of_attm['trip'] = $row['tripdoc'];
                break;
			}
		}
        $sql = "select * from hr_employee_trip_info where trip_id=$index";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->addTime = array();
            foreach ($rows as $row) {

                $temp = array();
                $temp['id'] = $row['id'];
                $temp['trip_id'] = $row['trip_id'];
                $temp['start_time'] = General::toDate($row['start_time']);
                $temp['end_time'] = General::toDate($row['end_time']);
                $temp['start_time_lg'] = $row['start_time_lg'];
                $temp['end_time_lg'] = $row['end_time_lg'];
                $temp['uflag'] = 'N';
                $this->addTime[] = $temp;
            }
        }

        $sql = "select * from hr_employee_trip_money where trip_id=$index";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $this->addMoney = array();
            foreach ($rows as $row) {
                $temp = array();
                $temp['id'] = $row['id'];
                $temp['trip_id'] = $row['trip_id'];
                $temp['money_set_id'] = $row['money_set_id'];
                $temp['trip_money'] = floatval($row['trip_money']);
                $temp['uflag'] = 'N';
                $this->addMoney[] = $temp;
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

    /*  id;employee_id;employee_code;employee_name;reward_id;reward_name;reward_money;reward_goods;remark;city;*/
	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'audit':
                $sql = "update hr_employee_trip set
							status = 2, 
							head_lcu = :head_lcu, 
							head_lcd = :head_lcd, 
							luu = :luu
						where id = :id
						";
                break;
            case 'reject':
                $sql = "update hr_employee_trip set
							status = 3, 
							reject_cause = :reject_cause, 
							head_lcu = :head_lcu, 
							head_lcd = :head_lcd, 
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':reject_cause')!==false)
            $command->bindParam(':reject_cause',$this->reject_cause,PDO::PARAM_STR);

        if (strpos($sql,':status')!==false)
            $command->bindParam(':status',$this->status,PDO::PARAM_INT);
        if (strpos($sql,':head_lcu')!==false)
            $command->bindParam(':head_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':head_lcd')!==false){
            $head_lcd = date("Y/m/d H:i:s");
            $command->bindParam(':head_lcd',$head_lcd,PDO::PARAM_STR);
        }
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();
		return true;
	}
}
