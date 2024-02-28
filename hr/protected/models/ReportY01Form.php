<?php
/* Reimbursement Form */

class ReportY01Form extends CReportForm
{
	public $region;
	public $email_bool=false;

	protected function labelsEx() {
		return array(
				'region'=>Yii::t('report','Region'),
			);
	}
	
	protected function rulesEx() {
		return array(
				array('region','safe'),
			);
	}
	
	protected function queueItemEx() {
		return array(
				'REGION'=>$this->region,
				'EMAILBOOL'=>$this->email_bool,
			);
	}
	
	public function init() {
		$this->id = 'RptStaffList';
		$this->name = Yii::t('report','Staff List');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'year_no,month_no,city';
		$this->year = date("Y");
		$this->month = date("m");
	}

	public function resetCityForAllow(){
        $this->email_bool=false;
        $city = $this->city;
        $city_allow = City::model()->getDescendantList($city);
        $cstr = $city;
        $city_allow .= (empty($city_allow)) ? "'$cstr'" : ",'$cstr'";
        $this->region = $city_allow;
        $this->target_dt = date("Y-m-d",strtotime($this->year."/".$this->month."/01"));
    }
}
