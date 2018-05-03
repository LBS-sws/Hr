<?php
class TimerCommand extends CConsoleCommand {
	
	public function run() {
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $firstday = date("Y/m/d");
        $firstdayTwo = date("Y-m-d");
        $lastday = date("Y/m/d",strtotime("$firstday +1 month"));
        $sql = "staff_status=0 and (attachment='' or attachment=0 or attachment is null) and fix_time='fixation' and end_time <='$lastday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        $command->reset();
        $command->update('hr_employee', array("z_index"=>3),"staff_status=0 and test_type=1 and test_start_time <= '$firstday' and test_end_time >='$firstdayTwo'");//試用期
        $command->reset();
        $command->update('hr_employee', array("z_index"=>2),"staff_status=0 and test_type=1 and test_start_time >= '$firstday'");//未入職
        $command->reset();
        $command->update('hr_employee', array("z_index"=>1),"staff_status=0 and (test_type=0 or test_end_time <='$firstdayTwo')");//正式員工
        $command->update('hr_employee', array("z_index"=>4),"staff_status=0 and fix_time='fixation' and end_time <='$lastday'");//合同即將過期
        $command->update('hr_employee', array("z_index"=>5),"staff_status=0 and fix_time='fixation' and end_time <='$firstday'");//合同過期
        if($rows){
            foreach ($rows as $row){
                $description="员工合同即将到期 - ".$row["name"];
                $subject="员工合同即将到期 - ".$row["name"];
                $message="<p>员工编号：".$row["code"]."</p>";
                $message.="<p>员工姓名：".$row["name"]."</p>";
                $message.="<p>员工所在城市：".CGeneral::getCityName($row["city"])."</p>";
                $message.="<p>员工入职日期：".$row["entry_time"]."</p>";
                $message.="<p>合同日期：".date("Y/m/d",strtotime($row["start_time"]))." - ".$row["end_time"]."</p>";
                $email->setDescription($description);
                $email->setMessage($message);
                $email->setSubject($subject);
                $email->addEmailToPrefixAndCity(array("ZG02","ZE04"),$row["city"]);
                $email->sent("系统生成");
                $email->resetToAddr();
            }
            $command->reset();
            $command->update('hr_employee', array("attachment"=>1),$sql);
        }
	}
}
?>