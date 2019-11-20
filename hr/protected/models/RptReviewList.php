<?php
class RptReviewList extends CReport {
	protected function fields() {
		return array(
			'code'=>array('label'=>Yii::t('contract','Employee Code'),'width'=>15,'align'=>'L'),
			'name'=>array('label'=>Yii::t('contract','Employee Name'),'width'=>20,'align'=>'L'),
			'city'=>array('label'=>Yii::t('contract','City'),'width'=>20,'align'=>'C'),
			'department'=>array('label'=>Yii::t('contract','Department'),'width'=>15,'align'=>'C'),
			'position'=>array('label'=>Yii::t('contract','Position'),'width'=>15,'align'=>'C'),
			'entry_time'=>array('label'=>Yii::t('contract','Entry Time'),'width'=>20,'align'=>'C'),
            'name_list'=>array('label'=>Yii::t('contract','reviewAllot manager'),'width'=>30,'align'=>'L'),
            'year_period'=>array('label'=>Yii::t('contract','Evaluation period'),'width'=>25,'align'=>'C'),
            'review_sum'=>array('label'=>Yii::t('contract','review sum'),'width'=>20,'align'=>'C'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Staffs').':'.$this->criteria['STAFFSDESC']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
        $year = $this->criteria['YEAR'];
        $year_type = $this->criteria['YEARTYPE'];
        $dateTime = ReviewAllotList::getReviewDateTime($year,$year_type);
        if($year_type == 1){
            $year_period = "$year 4月 - $year 9月";
        }else{
            $year_period = "$year 10月 - ".($year+1)." 3月";
        }

		$city = $this->criteria['CITY'];
		$staff_id = $this->criteria['STAFFS'];
		
		$citymodel = new City();
		$citylist = $citymodel->getDescendantList($city);
		$citylist = empty($citylist) ? "'$city'" : "$citylist,'$city'";
		
		$suffix = Yii::app()->params['envSuffix'];
		
		$cond_staff = '';
		if (!empty($staff_id)) {
			$ids = explode('~',$staff_id);
			if(count($ids)>1){
                $cond_staff = implode(",",$ids);
            }else{
                $cond_staff = $staff_id;
            }
			if ($cond_staff!=''){
                $cond_staff = " and a.id in ($cond_staff)";
            } 
		}
        $sql = "select a.id,a.name,a.code,b.name as city_name,a.entry_time,d.name as dept_name,d.review_type ,e.name as ment_name 
                from hr_employee a 
                LEFT JOIN security$suffix.sec_city b ON a.city = b.code
                LEFT JOIN hr_dept d ON a.position = d.id
                LEFT JOIN hr_dept e ON a.department = e.id
                where a.city IN ($citylist) AND a.staff_status = 0 $cond_staff AND replace(entry_time,'-', '/')<='$dateTime' 
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['code'] = $row['code'];
				$temp['name'] = $row['name'];
				$temp['city'] = $row['city_name'];
				$temp['department'] = $row['ment_name'];
				$temp['position'] = $row['dept_name'];
				$temp['entry_time'] = $row['entry_time'];
				$temp['year_period'] = $year_period;
                $temp['name_list'] = Yii::t("contract","undistributed");
                $temp['review_sum'] = Yii::t("contract","undistributed");

                $this->resetTemp($row,$temp);
				$this->data[] = $temp;
			}
		}
		return true;
	}

	protected function resetTemp($row,&$temp){
        $year = $this->criteria['YEAR'];
        $year_type = $this->criteria['YEARTYPE'];
        $arr = Yii::app()->db->createCommand()->select("name_list,review_sum,status_type")->from("hr_review")
            ->where("year = :year and year_type = :year_type and employee_id = :employee_id",
                array(":year"=>$year,":year_type"=>$year_type,":employee_id"=>$row['id']))->queryRow();

        if($arr){
            switch ($arr["status_type"]){
                case 1:
                    $temp['name_list'] = $arr["name_list"];
                    $temp['review_sum'] = Yii::t("contract","in review");
                    break;
                case 2:
                    $temp['name_list'] = $arr["name_list"];
                    $temp['review_sum'] = Yii::t("contract","more review");
                    break;
                case 3:
                    $temp['name_list'] = $arr["name_list"];
                    $temp['review_sum'] = $arr["review_sum"];
                    break;
            }
        }
    }
	
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>