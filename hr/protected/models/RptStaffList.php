<?php
class RptStaffList extends CReport {
	protected function fields() {
		return array(
			'name'=>array('label'=>Yii::t('report','Name'),'width'=>25,'align'=>'L'),
			'gender'=>array('label'=>Yii::t('report','Gender'),'width'=>10,'align'=>'C'),
			'position'=>array('label'=>Yii::t('report','Position'),'width'=>25,'align'=>'L'),
			'user_card'=>array('label'=>Yii::t('report','ID No.'),'width'=>20,'align'=>'L'),
			'user_card_date'=>array('label'=>Yii::t('report','ID Valid Date'),'width'=>15,'align'=>'C'),
			'dob'=>array('label'=>Yii::t('report','DOB'),'width'=>15,'align'=>'C'),
			'address'=>array('label'=>Yii::t('report','Original Address'),'width'=>40,'align'=>'L'),
			'contact_address'=>array('label'=>Yii::t('report','Contact Address'),'width'=>40,'align'=>'L'),
			'phone'=>array('label'=>Yii::t('report','Contact Type'),'width'=>20,'align'=>'L'),
			'fix_time'=>array('label'=>Yii::t('report','Contract Type'),'width'=>15,'align'=>'L'),
			'join_dt'=>array('label'=>Yii::t('report','Join Date'),'width'=>15,'align'=>'C'),
			'start_dt'=>array('label'=>Yii::t('report','Contract Start Date'),'width'=>15,'align'=>'C'),
			'end_dt'=>array('label'=>Yii::t('report','Contract End Date'),'width'=>15,'align'=>'C'),
			'social_code'=>array('label'=>Yii::t('report','Social Security Code'),'width'=>20,'align'=>'L'),
			'emergency_user'=>array('label'=>Yii::t('report','Emergency Contact'),'width'=>30,'align'=>'L'),
			'emergency_phone'=>array('label'=>Yii::t('report','Emergency Phone'),'width'=>20,'align'=>'L'),
			'change_dt'=>array('label'=>Yii::t('report','Leave/Change Date'),'width'=>15,'align'=>'C'),
			'reason'=>array('label'=>Yii::t('report','Leave/Change Reason'),'width'=>40,'align'=>'L'),
			'remarks'=>array('label'=>Yii::t('report','Remarks'),'width'=>40,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = '';
		$output = $this->exportExcel();
		$this->submitEmail($output);
		return $output;
	}
		public function retrieveData() {
		$temp_dt = strtotime($this->criteria['TARGET_DT'].' -1 month');
		$start_dt = date('Y',$temp_dt).'-'.date('m',$temp_dt).'-01 00:00:00';
		$temp_dt = strtotime($this->criteria['TARGET_DT']);
		$end_dt = date('Y',$temp_dt).'/'.date('m',$temp_dt).'/01';

		$city = $this->criteria['CITY'];
		
		$suffix = Yii::app()->params['envSuffix'];

		$sql = "select a.*, b.name as job_title 
				from hr_employee a left outer join hr_dept b on a.position = b.id
				where a.city='$city' and (a.leave_time >= '$start_dt' or a.leave_time is null)
				order by a.name
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['name'] = $row['name'];
				$temp['gender'] = Yii::t('contract',$row['sex']);
				$temp['position'] = $row['job_title'];
				$temp['user_card'] = is_numeric($row['user_card']) ? $row['user_card'].' ' : $row['user_card'];
				$temp['user_card_date'] = General::toDate($row['user_card_date']);
				$temp['dob'] = General::toDate($row['birth_time']);
				$temp['address'] = $row['address'];
				$temp['contact_address'] = $row['contact_address'];
				$temp['phone'] = is_numeric($row['phone']) ? $row['phone'].' ' : $row['phone'];
				$temp['fix_time'] = Yii::t('contract',$row['fix_time']);
				$temp['join_dt'] = General::toDate($row['entry_time']);
				$temp['start_dt'] = General::toDate($row['start_time']);
				$temp['end_dt'] = General::toDate($row['end_time']);
				$temp['social_code'] = is_numeric($row['social_code']) ? $row['social_code'].' ' : $row['social_code'];
				$temp['emergency_user'] = $row['emergency_user'];
				$temp['emergency_phone'] = is_numeric($row['emergency_phone']) ? $row['emergency_phone'].' ' : $row['emergency_phone'];
				$temp['change_dt'] = ($row['staff_status']=-1 && strtotime($row['leave_time']) < strtotime($end_dt)
								? General::toDate($row['leave_time']) : '');
				$temp['reason'] = ($row['staff_status']=-1 && strtotime($row['leave_time']) < strtotime($end_dt)
								? General::toDate($row['leave_reason']) : '');
				$temp['remarks'] = '';
				$this->data[] = $temp;
			}
		}
		
		return true;	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
	
	public function submitEmail($output) {
		$city = $this->criteria['CITY'];
		$date = $this->criteria['TARGET_DT'];
		
		$users = $this->getUsersWithRight($city,'ZB01');

		$to = General::getEmailByUserIdArray($users);
		$to = General::dedupToEmailList($to);
		$cc = array();
		
		$subject = Yii::t('report','Staff List').' ('.General::getCityName($city).') - '.General::toDate($date);
		$desc = Yii::t('report','Staff List').' ('.General::getCityName($city).') - '.General::toDate($date);
		
		$param = array(
				'from_addr'=>Yii::app()->params['systemEmail'],
				'to_addr'=>json_encode($to),
				'cc_addr'=>json_encode($cc),
				'subject'=>$subject,
				'description'=>$desc,
				'message'=>Yii::t('report','Please find the attached report for your reference.'),
			);
		$fn = Yii::t('report','Staff List').'_'.General::getCityName($city).'.xlsx';
		$connection = Yii::app()->db;
		$this->sendEmailWithAttachment($connection, $param, array($fn=>$output));
	}
	
	protected function getUsersWithRight($city, $right) {
		$rtn = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select a.username, b.city from security$suffix.sec_user_access a, security$suffix.sec_user b
				where a.a_read_only like '%$right%' or a.a_read_write like '%$right%'
				and a.username=b.username and b.status='A'
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (!empty($rows)) {
			$cityo = new City;
			$citylist = $cityo->getAncestorList($city).','.$city;
			foreach ($rows as $row) {
				if (strpos($citylist,$row['city'])!==false) $rtn[] = $row['username'];
			}
		}
		return $rtn;
	}
}
?>