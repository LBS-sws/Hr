<?php

class ReviewSearchList extends CListPageModel
{

    public $employee_id;


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
			'status'=>Yii::t('contract','Status'),
			'city'=>Yii::t('contract','City'),
            'city_name'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'year'=>Yii::t('contract','what year'),
            'year_type'=>Yii::t('contract','year type'),
            'name_list'=>Yii::t('contract','reviewAllot manager'),
            'review_sum'=>Yii::t('contract','review sum'),
		);
	}

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows||Yii::app()->user->validFunction('ZR09')){
            $this->employee_id = isset($rows["employee_id"])?$rows["employee_id"]:"";
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        //FIND_IN_SET
        $expr_sql = " and b.status_type in (1,2,3)";
        if(!Yii::app()->user->validFunction('ZR09')){//沒有所有權限
            $expr_sql.=" and (FIND_IN_SET('$this->employee_id',b.id_s_list) or b.employee_id = '$this->employee_id' or b.lcu = '$this->employee_id')";
        }
		$sql1 = "select c.name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,b.status_type,b.year,b.year_type,b.id,b.name_list,b.review_sum 
                from hr_review b 
                LEFT JOIN hr_employee c ON c.id = b.employee_id
                LEFT JOIN hr_company d ON c.company_id = d.id
                LEFT JOIN hr_dept e ON c.position = e.id
                where c.city IN ($city_allow) AND c.staff_status = 0 $expr_sql
			";
		$sql2 = "select count(*)  
                from hr_review b 
                LEFT JOIN hr_employee c ON c.id = b.employee_id
                LEFT JOIN hr_company d ON c.company_id = d.id
                LEFT JOIN hr_dept e ON c.position = e.id
                where c.city IN ($city_allow) AND c.staff_status = 0 $expr_sql
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('c.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('c.code',$svalue);
					break;
				case 'phone':
					$clause .= General::getSqlConditionClause('c.phone',$svalue);
					break;
                case 'position':
                    $clause .= General::getSqlConditionClause('e.name',$svalue);
                    break;
                case 'year':
                    $clause .= General::getSqlConditionClause('b.year',$svalue);
                    break;
                case 'city_name':
                    $clause .= ' and c.city in '.WordForm::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by b.id asc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = ReviewAllotList::getReviewStatuts($record["status_type"]);
				$this->attr[] = array(
					'id'=>$record['id'],
					'name'=>$record['name'],
					'year'=>$record['year'],
					'year_type'=>ReviewAllotList::getYearTypeList($record['year_type']),
					'code'=>$record['code'],
					'position'=>$record['dept_name'],
					'company_id'=>$record['company_name'],
					'phone'=>$record['phone'],
					'status'=>$arr["status"],
					'style'=>$arr["style"],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'entry_time'=>$record["entry_time"],
                    'name_list'=>$record["name_list"],
                    'review_sum'=>$record["review_sum"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['reviewSearch_01'] = $this->getCriteria();
		return true;
	}
}
