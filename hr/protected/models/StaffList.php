<?php

class StaffList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('staff','ID'),
			'code'=>Yii::t('staff','Code'),
			'name'=>Yii::t('staff','Name'),
			'position'=>Yii::t('staff','Position'),
			'email'=>Yii::t('staff','Email'),
			'city_name'=>Yii::t('misc','City'),
            'status'=>Yii::t('staff','status'),
		);
	}
	public function getNameById($index){
        $username = Yii::app()->db->createCommand()->select("name")->from("hr_staff_copy")->where("id=:id",array(":id"=>$index))->queryAll();
        if (count($username) > 0){
            return $username[0]["name"];
        }else{
            return $index;
        }
    }
	public function getChangeTypeById($index){
        $changeType = Yii::app()->db->createCommand()->select("change_type")->from("hr_staff_copy")->where("id=:id",array(":id"=>$index))->queryAll();
        if (count($changeType) > 0){
            return $changeType[0]["change_type"];
        }else{
            return $index;
        }
    }


	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.id, a.code, a.name, a.position, a.email, b.name as city_name 
				from hr_staff_copy a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) AND a.change_type is NULL 
			";
		$sql2 = "select count(id)
				from hr_staff_copy a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) AND a.change_type is NULL 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'position':
					$clause .= General::getSqlConditionClause('a.position',$svalue);
					break;
				case 'email':
					$clause .= General::getSqlConditionClause('a.email',$svalue);
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
					'code'=>$record['code'],
					'name'=>$record['name'],
					'position'=>$record['position'],
					'email'=>$record['email'],
					'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a07'] = $this->getCriteria();
		return true;
	}
	public function retrieveDetailDataByPage($pageNum=1,$index)
	{
	    $username = $this->getNameById($index);

		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city_allow();
		$sql1 = "select a.id, a.code, a.name, a.position, a.email, a.change_type, b.name as city_name 
				from hr_staff_copy a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) AND a.name = '$username'
			";
		$sql2 = "select count(id)
				from hr_staff_copy a, security$suffix.sec_city b 
				where a.city=b.code and a.city in ($city) AND a.name = '$username'
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('a.code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'position':
					$clause .= General::getSqlConditionClause('a.position',$svalue);
					break;
				case 'email':
					$clause .= General::getSqlConditionClause('a.email',$svalue);
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
			    $arr=array(
                    'id'=>$record['id'],
                    'code'=>$record['code'],
                    'name'=>$record['name'],
                    'position'=>$record['position'],
                    'email'=>$record['email'],
                    'city_name'=>$record['city_name']
                );
			    if(!empty($record['change_type'])){
                    $arr['status'] = "change";
                }else{
                    $arr['status'] = "running";
                }
				$this->attr[] = $arr;
			}
		}
		$session = Yii::app()->session;
		$session['criteria_a07'] = $this->getCriteria();
		return true;
	}

}
