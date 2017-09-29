<?php

class HolidayConList extends CListPageModel
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
			'z_index'=>Yii::t('contract','Level'),
			'name'=>Yii::t('contract',' Name'),
            'name_0'=>Yii::t('contract','Holiday Name'),
			'name_1'=>Yii::t('contract','Work Name'),
		);
	}
	public function getTypeName(){
	    if ($this->type == 1){
            return Yii::t("contract","Work");
        }else{
            return Yii::t("contract","Holiday");
        }
    }
	public function getTypeAcc(){
	    if ($this->type == 1){
            return "ZC04";
        }else{
            return "ZC03";
        }
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$type = $this->type;
		$sql1 = "select * from hr_holiday 
                where city='$city' AND type=$type 
			";
		$sql2 = "select count(id)
				from hr_dept 
				where city='$city'AND type=$type 
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
		$userList = CompanyForm::getUserList();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'z_index'=>$record['z_index'],
                    'acc'=>$this->getTypeAcc()
				);
			}
		}
		$session = Yii::app()->session;
		$session['holidaycon_01'] = $this->getCriteria();
		return true;
	}

}
