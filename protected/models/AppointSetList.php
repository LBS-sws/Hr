<?php

class AppointSetList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'username'=>Yii::t('contract','username'),
            'appoint_code'=>Yii::t('contract','appoint code'),
            'employee_name'=>Yii::t('contract','employee name'),
            'city_name'=>Yii::t('contract','City'),
			'audit_user_str'=>Yii::t('contract','appoint audit'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.code as employee_code,b.name as employee_name,f.name as city_name
				from hr_appoint a
				LEFT JOIN hr_employee b ON a.employee_id = b.id
				LEFT JOIN security$suffix.sec_city f ON b.city = f.code
				where b.city in ({$city_allow}) ";
        $sql2 = "select count(a.id) 
				from hr_appoint a
				LEFT JOIN hr_employee b ON a.employee_id = b.id
				LEFT JOIN security$suffix.sec_city f ON b.city = f.code
				where b.city in ({$city_allow}) ";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'appoint_code':
					$clause .= General::getSqlConditionClause('a.appoint_code',$svalue);
					break;
				case 'employee_name':
					$clause .= " and (b.name like'%{$svalue}%' or b.code like '%{$svalue}%')";
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
				case 'audit_user_str':
					$clause .= General::getSqlConditionClause('a.audit_user_str',$svalue);
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
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'appoint_code'=>$record['appoint_code'],
                    'employee_name'=>$record['employee_name']." ({$record['employee_code']})",
                    'city_name'=>$record['city_name'],
                    'audit_user_str'=>$record['audit_user_str'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['appointSet_c01'] = $this->getCriteria();
		return true;
	}

}
