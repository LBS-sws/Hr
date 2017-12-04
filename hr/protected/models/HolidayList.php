<?php

class HolidayList extends CListPageModel
{
    public $type = 0;
    public $only = 0;
    public $employee_id = 0;
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

    public function validateEmployee(){
        if (empty($this->only)){
            $uid = Yii::app()->user->id;
            $city = Yii::app()->user->city();
            $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
                ->where('user_id=:user_id and city=:city',
                    array(':user_id'=>$uid,':city'=>$city))->queryRow();
            if ($rows){
                $this->employee_id = $rows["employee_id"];
                return true;
            }
            return false;
        }
        return true;
    }

	public function getTypeName(){
        if ($this->type == 1){
            return Yii::t("contract","Work");
        }else{
            return Yii::t("contract","holiday");
        }
    }
	public function getTypeAcc(){
	    if ($this->only == 1){
            if ($this->type == 1){
                return "ZE06";
            }else{
                return "ZE05";
            }
        }else{
            if ($this->type == 1){
                return "ZA06";
            }else{
                return "ZA05";
            }
        }
    }
	public function getTitleAppText(){
	    if ($this->only == 1){
            if ($this->type == 1){
                return Yii::t("app","All Work List");
            }else{
                return Yii::t("app","All Holiday List");
            }
        }else{
            if ($this->type == 1){
                return Yii::t("app","Only Work List");
            }else{
                return Yii::t("app","Only Holiday List");
            }
        }
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$type = $this->type;
		$sql1 = "select * from hr_employee_work 
                where type=$type 
			";
		$sql2 = "select count(id)
				from hr_employee_work 
				where type=$type 
			";
		$clause = "";
		if(empty($this->only)){
            $sql1.=" and employee_id = ".$this->employee_id." ";
            $sql2.=" and employee_id = ".$this->employee_id." ";
        }else{
            $sql1.="and city in($city_allow) and status != 0 ";
            $sql2.="and city in($city_allow) and status != 0 ";
        }
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
                    'city'=>CGeneral::getCityName($record["city"]),
					'end_time'=>date("Y-m-d",strtotime($record['end_time'])),
					'start_time'=>date("Y-m-d",strtotime($record['start_time'])),
					'status'=>$this->translateEmploy($record['status']),
					'acc'=>$this->getTypeAcc(),
				);
			}
		}
		$session = Yii::app()->session;
		$session['holiday_01'] = $this->getCriteria();
		return true;
	}

    public function translateEmploy($status){
        switch ($status){
            case 0:
                return array(
                    "status"=>Yii::t("contract","Draft"),
                    "style"=>" "
                );//已製作，待提交
            case 1:
                return array(
                    "status"=>Yii::t("contract","Produced, pending audit"),
                    "style"=>" text-success"
                );//已提交，待審核
            case 2:
                return array(
                    "status"=>Yii::t("contract","audited, pending finish"),
                    "style"=>" text-yellow"
                );//已審核，待確認
            case 3:
                return array(
                    "status"=>Yii::t("contract","reject"),
                    "style"=>" text-red"
                );//已拒絕
            case 4:
                return array(
                    "status"=>Yii::t("contract","Finish"),
                    "style"=>" text-primary"
                );//已完成
        }
        return array(
            "status"=>"",
            "style"=>""
        );
    }
}
