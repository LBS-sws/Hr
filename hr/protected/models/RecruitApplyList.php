<?php

class RecruitApplyList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'year'=>Yii::t('recruit','year'),
			'city'=>Yii::t('recruit','city'),
			'dept_name'=>Yii::t('recruit','dept name'),
			'recruit_num'=>Yii::t('recruit','recruit num'),
			'now_num'=>Yii::t('recruit','now num'),
			'leave_num'=>Yii::t('recruit','leave num'),
			'lack_num'=>Yii::t('recruit','lack num'),
			'completion_rate'=>Yii::t('recruit','completion rate'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name as city_name,f.name as dept_name 
				from hr_recruit a 
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				LEFT JOIN hr_dept f ON a.dept_id=f.id 
				where a.city='{$city}' 
			";
		$sql2 = "select count(a.id)
				from hr_recruit a 
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code 
				where a.city='{$city}' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'year':
					$clause .= General::getSqlConditionClause('a.year',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'dept_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $arr = self::recruitLoading($record);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'year'=>$record['year'],
                    'city'=>$record['city_name'],
                    'dept_id'=>$record['dept_id'],
                    'recruit_num'=>$record['recruit_num'],
                    'dept_name'=>$record['dept_name'],
                    'now_num'=>$arr['now_num'],
                    'leave_num'=>$arr['leave_num'],
                    'lack_num'=>$arr['lack_num'],
                    'completion_rate'=>$arr['completion_rate'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['recruitApply_c01'] = $this->getCriteria();
		return true;
	}

    public static function recruitLoading($record){
        $arr=array(
            'now_num'=>0,
            'leave_num'=>0,
            'lack_num'=>0,
            'completion_rate'=>0,
            'staff_list'=>array()
        );
        $rows = Yii::app()->db->createCommand()->select("id,staff_status")->from("hr_employee")
            ->where("position=:position and entry_time like'{$record['year']}%' and staff_status!=1",array(":position"=>$record["dept_id"]))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr["staff_list"][]=$row["id"];
                $arr["now_num"]++;
                if($row["staff_status"]==-1){
                    $arr["leave_num"]++;
                }
            }
        }
        $arr["lack_num"] = $record["recruit_num"] - ($arr["now_num"]-$arr["leave_num"]);
        $arr["completion_rate"] = round(($arr["now_num"]-$arr["leave_num"])/$record["recruit_num"],2)*100;
        $arr["completion_rate"].= "%";
        return $arr;
    }
}
