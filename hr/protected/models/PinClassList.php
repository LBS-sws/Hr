<?php

class PinClassList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('app','Pin Class'),
            'z_index'=>Yii::t('contract','Level'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
		$sql1 = "select id,name,z_index from hr_pin_class 
                where id>0 
			";
		$sql2 = "select count(id) from hr_pin_class 
                where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
				case 'z_index':
					$clause .= General::getSqlConditionClause('z_index',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by z_index asc ";
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
					'name'=>$record['name'],
					'z_index'=>$record['z_index'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['pinClass_01'] = $this->getCriteria();
		return true;
	}
}
