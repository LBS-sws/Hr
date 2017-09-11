<?php

class MakeWagesList extends CListPageModel
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
		$sql1 = "select * from hr_employee
                where city='$city' AND staff_status = 0
			";
		$sql2 = "select count(id)
				from hr_employee 
				where city='$city' AND staff_status = 0
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('code',$svalue);
					break;
				case 'phone':
					$clause .= General::getSqlConditionClause('phone',$svalue);
					break;
				case 'position':
					$clause .= General::getSqlConditionClause('phone',$svalue);
					break;
				case 'company_id':
					//$clause .= General::getSqlConditionClause('company_id',$svalue);
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
			    $arr = $this->returnStaffStatus($record['id']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'code'=>$record['code'],
					'position'=>DeptForm::getDeptToid($record['position']),
					'company_id'=>CompanyForm::getCompanyToId($record['company_id'])["name"],
					'phone'=>$record['phone'],
					'staff_status'=>$arr["status"],
					'style'=>$arr["style"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a07'] = $this->getCriteria();
		return true;
	}

	public function returnStaffStatus($staff_id){
        $fastDate = date('Y-m-01', strtotime(date("Y-m-d")));
        $lastDate = date('Y-m-d', strtotime("$fastDate +1 month -1 day"));
        $records = Yii::app()->db->createCommand()->select("wages_status")->from("hr_employee_wages")
            ->where("employee_id =:id and lcd >='$fastDate' and lcd <='$lastDate'",array(":id"=>$staff_id))->queryRow();
        if($records){
            switch ($records["wages_status"]){
                case 0:
                    return array(
                        "status"=>Yii::t("contract","Finish"),
                        "style"=>" text-primary"
                    );//已完成
                case 1:
                    return array(
                        "status"=>Yii::t("contract","Produced, pending submission"),
                        "style"=>" "
                    );//已製作，待提交
                case 2:
                    return array(
                        "status"=>Yii::t("contract","Produced, pending audit"),
                        "style"=>" text-success"
                    );//已提交，待審核
                case 3:
                    return array(
                        "status"=>Yii::t("contract","audited, pending finish"),
                        "style"=>" text-yellow"
                    );//已審核，待確認
                case 4:
                    return array(
                        "status"=>Yii::t("contract","reject"),
                        "style"=>" text-red"
                    );//已拒絕

            }
        }else{
            return array(
                "status"=>Yii::t("contract","To be made"),
                "style"=>" text-danger"
            );//未生成工資單
        }
    }
}
