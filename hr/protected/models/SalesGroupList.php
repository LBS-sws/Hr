<?php

class SalesGroupList extends CListPageModel
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
            'group_name'=>Yii::t('contract','group name'),
            'staff_num'=>Yii::t('contract','staff num'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,count(b.id) as staff_num from hr_sales_staff b
                RIGHT JOIN hr_sales_group a ON a.id=b.group_id 
                where (a.local=0 or (a.local=1 and a.city='$city'))  
			";
		$sql2 = "select count(a.id) from hr_sales_group a
                where (a.local=0 or (a.local=1 and a.city='$city')) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'id':
					$clause .= General::getSqlConditionClause('id',$svalue);
					break;
				case 'set_name':
					$clause .= General::getSqlConditionClause('a.set_name',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and a.code in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause." group by a.id ".$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'group_name'=>$record['group_name'],
					'staff_num'=>$record['staff_num']
				);
			}
		}
		$session = Yii::app()->session;
		$session['salesGroup_01'] = $this->getCriteria();
		return true;
	}
}
