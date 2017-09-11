<?php

class AuditWagesList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('contract','ID'),
			'name'=>Yii::t('contract','Employee Name'),
			'code'=>Yii::t('contract','Employee Code'),
			'phone'=>Yii::t('contract','Employee Phone'),
			'position'=>Yii::t('contract','Position'),
			'company_id'=>Yii::t('contract','Company Name'),
			'contract_id'=>Yii::t('contract','Contract Name'),
			'staff_status'=>Yii::t('contract','Wages Status'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $fastDate = date('Y-m-01', strtotime(date("Y-m-d")));
        $lastDate = date('Y-m-d', strtotime("$fastDate +1 month -1 day"));
		$sql1 = "select * from hr_employee_wages
                where city='$city' AND wages_status != 1 AND wages_status != 0 and lcd >='$fastDate' and lcd <='$lastDate'
			";
		$sql2 = "select count(id)
				from hr_employee_wages 
				where city='$city' AND wages_status != 1 AND wages_status != 0 and lcd >='$fastDate' and lcd <='$lastDate'
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
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
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $arr = $this->returnStaffStatus($record['wages_status']);
			    $staff = EmployeeForm::getEmployeeOneToId($record['employee_id']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$staff['name'],
					'code'=>$staff['code'],
					'position'=>DeptForm::getDeptToid($staff['position']),
					'company_id'=>CompanyForm::getCompanyToId($staff['company_id'])["name"],
					'phone'=>$staff['phone'],
					'staff_status'=>$arr["status"],
					'style'=>$arr["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a07'] = $this->getCriteria();
		return true;
	}

	public function returnStaffStatus($wages_status){
        switch ($wages_status){
            case 2:
                return array(
                    "status"=>Yii::t("contract","pending approval"),
                    "style"=>" text-yellow"
                );//已提交，待審核
            case 3:
                return array(
                    "status"=>Yii::t("contract","Finish approval"),
                    "style"=>" text-success"
                );//已審核，待確認
            case 4:
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
