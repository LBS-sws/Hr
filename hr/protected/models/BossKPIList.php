<?php

class BossKPIList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'kpi_name'=>Yii::t('contract','kpi name'),
            'sum_bool'=>Yii::t('contract','kpi sum bool'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select id,kpi_name,kpi_str,sum_bool from hr_kpi 
                where id>0 
			";
		$sql2 = "select count(id)
				from hr_kpi 
                where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'kpi_name':
					$clause .= General::getSqlConditionClause('kpi_str',$svalue);
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
					'kpi_name'=>Yii::t("contract",$record['kpi_name']),
					'sum_bool'=>empty($record['sum_bool'])?Yii::t("contract","Off"):Yii::t("contract","On"),
				);
			}
		}
		$session = Yii::app()->session;
		$session['bossKPI_01'] = $this->getCriteria();
		return true;
	}
}
