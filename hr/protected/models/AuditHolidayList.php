<?php

class AuditHolidayList extends CListPageModel
{
    public $type = 0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'id'=>Yii::t('contract','ID'),
            'employee_name'=>Yii::t('contract','Employee Name'),
            'start_time'=>Yii::t('contract','Start Time'),
            'end_time'=>Yii::t('contract','End Time'),
            'holiday_name'=>Yii::t('contract',' Cause'),
            'status'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
		);
	}

    public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Work");
        }else{
            return Yii::t("contract","holiday");
        }
    }
    public function getTypeAcc(){
        if ($this->type == 1){
            return "ZG05";
        }else{
            return "ZG04";
        }
    }
    public function getTitleAppText(){
        if ($this->type == 1){
            return Yii::t("app","Work Audit");
        }else{
            return Yii::t("app","Holiday Audit");
        }
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $type = $this->type;
		$sql1 = "select * from hr_employee_work
                where city IN ($city_allow) AND status != 4 AND status != 0 AND type = '$type' 
			";
		$sql2 = "select count(id)
				from hr_employee_work 
                where city IN ($city_allow) AND status != 4 AND status != 0 AND type = '$type' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('employee_name',$svalue);
                    break;
                case 'holiday_name':
                    $clause .= General::getSqlConditionClause('holiday_name',$svalue);
                    break;
                case 'city':
                    $clause .= General::getSqlConditionClause('city',$svalue);
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

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
                    'id'=>$record['id'],
                    'employee_name'=>$record['employee_name'],
                    'holiday_name'=>$record['holiday_name'],
                    'end_time'=>date("Y-m-d",strtotime($record['end_time'])),
                    'start_time'=>date("Y-m-d",strtotime($record['start_time'])),
                    'status'=>$this->returnStaffStatus($record['status']),
                    'city'=>CGeneral::getCityName($record["city"]),
                    'acc'=>$this->getTypeAcc(),
				);
			}
		}
		$session = Yii::app()->session;
		$session['auditholiday_01'] = $this->getCriteria();
		return true;
	}

	public function returnStaffStatus($wages_status){
        switch ($wages_status){
            case 1:
                return array(
                    "status"=>Yii::t("contract","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
            case 2:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),
                    "style"=>" text-success"
                );//已審核，待確認
            case 3:
                return array(
                    "status"=>Yii::t("contract","Rejected"),
                    "style"=>" text-red"
                );//已拒絕
            default:
                return array(
                    "status"=>Yii::t("contract","Error"),
                    "style"=>" "
                );//已拒絕

        }
    }
}
