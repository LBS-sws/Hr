<?php

class EmployeeDown extends CFormModel
{
    public $checkId=array();//需要處理的員工
    public $staffList;//需要處理的員工
    public $checkAll;//無用
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'checkId'=>Yii::t('contract','Select the employee'),
		);
	}
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('checkId, checkAll','safe'),
            array('checkId','required'),
        );
    }

    public function getEmployeeAll(){
        $city = Yii::app()->user->city();
        $sql = "select * from hr_employee
                where city='$city' AND staff_status = 0
			";
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        if (count($records) > 0) {
            $this->staffList = $records;
        }else{
            $this->staffList = array();
        }
    }

    private function resetCheckId(){
        $arr = array();
        foreach ($this->checkId as $staffId){
            $staff = Yii::app()->db->createCommand()->select()->from("hr_employee")
                ->where('id=:id', array(':id'=>$staffId))->queryRow();
            if($staff){
                $staff["price1"] = WagesForm::getWagesTypeList($staff["price1"]);;
                $staff['price3'] = explode(",",$staff['price3']);
                array_push($arr,$staff);
            }
        }
        return $arr;
    }

    public function downExcel(){
        $staffList = $this->resetCheckId();
        $myExcel = new MyExcelTwo();
        $row = 1;
        foreach ($staffList as $staffId){
            $wagesValue = $staffId["price3"];
            $wagesName = $staffId["price1"];
            $head = array("员工编号","员工姓名","性別","电话","职位");
            $text = array($staffId["code"],$staffId["name"],$staffId["sex"],$staffId["phone"],$staffId["position"]);
            foreach ($wagesName as $key => $value){
                array_push($head,$value["type_name"]);
                if(empty($wagesValue[$key])){
                    $wagesValue[$key] = "";
                }
                array_push($text,$wagesValue[$key]);
            }
            $myExcel->setStartRow($row);
            $myExcel->setDataHeardToOneArr($head);
            $myExcel->setStartRow($row+1);
            $myExcel->setDataHeardToOneArr($text);
            $myExcel->printTable(count($head));
            $row = $row+4;
        }
        $myExcel->outDownExcel("工資單.xls");
    }
}
