<?php

class WorkList extends CListPageModel
{
    public $employee_id;//員工id
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'work_code'=>Yii::t('fete','Work Code'),
			'work_type'=>Yii::t('fete','Work Type'),
			'employee_id'=>Yii::t('contract','Employee Name'),
			'start_time'=>Yii::t('contract','Start Time'),
			'end_time'=>Yii::t('contract','End Time'),
			'log_time'=>Yii::t('fete','Log Date'),
			'status'=>Yii::t('contract','Status'),
		);
	}

	//驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->employee_id = $rows["employee_id"];
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
        $employee_id = $this->employee_id;
		$sql1 = "select * from hr_employee_work 
                where employee_id='$employee_id' 
			";
		$sql2 = "select count(id)
				from hr_employee_work 
				where employee_id='$employee_id' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'work_code':
					$clause .= General::getSqlConditionClause('work_code',$svalue);
					break;
				case 'employee_id':
					$clause .= General::getSqlConditionClause('employee_id',$svalue);
					break;
				case 'start_time':
					$clause .= General::getSqlConditionClause('start_time',$svalue);
					break;
				case 'end_time':
					$clause .= General::getSqlConditionClause('end_time',$svalue);
					break;
				case 'log_time':
					$clause .= General::getSqlConditionClause('log_time',$svalue);
					break;
				case 'status':
					$clause .= General::getSqlConditionClause('status',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$costNumList = $this->getWorkTypeList();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $colorList = $this->statusToColor($record['status']);
                $employeeList = EmployeeForm::getEmployeeOneToId($record['employee_id']);
                if($record['work_type'] == 2){
                    $record['start_time'] = date("Y/m/d",strtotime($record['start_time']));
                    $record['end_time'] = date("Y/m/d",strtotime($record['end_time']));
                    $dayStr ="天";
                }else{
                    $record['start_time'] = date("Y/m/d H:i:s",strtotime($record['start_time']));
                    $record['end_time'] = date("Y/m/d H:i:s",strtotime($record['end_time']));
                    $dayStr ="小時";
                }
				$this->attr[] = array(
					'id'=>$record['id'],
					'work_code'=>$record['work_code'],
					'employee_id'=>$employeeList["name"],
					'start_time'=>$record['start_time'],
					'end_time'=>$record['end_time'],
					'log_time'=>$record['log_time'].$dayStr,
					'work_type'=>$costNumList[$record['work_type']],
					'status'=>$colorList["status"],
					'style'=>$colorList["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['work_01'] = $this->getCriteria();
		return true;
	}

	//加班類型列表
    public function getWorkTypeList(){
	    return array(Yii::t("fete","Working days"),Yii::t("fete","Weekend off"),Yii::t("fete","Statutory leave day"));
    }
	//獲取小時列表
    public function getHoursList(){
        $arr = array();
        for ($i = 0;$i<24;$i++){
            if($i <10){
                $key = "0$i:00";
            }else{
                $key = "$i:00";
            }
            $arr[$key] = $key;
        }
	    return $arr;
    }
    //根據狀態獲取顏色
    public function statusToColor($status){
        switch ($status){
            // text-danger
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>""
                );
            case 1:
                return array(
                    "status"=>Yii::t("contract","Sent, pending approval"),//已發送，等待審核
                    "style"=>" text-primary"
                );
            case 2:
                return array(
                    "status"=>Yii::t("contract","audit"),//審核通過
                    "style"=>" text-yellow"
                );
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),//拒絕
                    "style"=>" text-danger"
                );
            case 4:
                return array(
                    "status"=>Yii::t("fete","approve"),//批准
                    "style"=>" text-green"
                );
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }
}
