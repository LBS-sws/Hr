<?php
class TimerCommand extends CConsoleCommand {
	
	public function run() {
        echo "start:";
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $firstday = date("Y/m/d");
        $lastday = date("Y/m/d",strtotime("$firstday + 1 month"));
        $sql = "staff_status=0 and (attachment='' or attachment=0 or attachment is null) and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        $command->reset();
        $aaa = $command->update('hr_employee', array("z_index"=>3),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') <= '$firstday' and replace(test_end_time,'-', '/') >='$firstday'");//試用期
        $command->reset();
        echo "試用期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>2),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') >= '$firstday'");//未入職
        $command->reset();
        echo "未入職:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>1),"staff_status=0 and (test_type=0 or replace(test_end_time,'-', '/') <='$firstday')");//正式員工
        $command->reset();
        echo "正式員工:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>4),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'");//合同即將過期
        $command->reset();
        echo "合同即將過期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>5),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') <'$firstday'");//合同過期
        echo "合同過期:$aaa<br>";
        if($rows){
            foreach ($rows as $row){
                $description="员工合同即将到期 - ".$row["name"];
                $subject="员工合同即将到期 - ".$row["name"];
                $message="<p>员工编号：".$row["code"]."</p>";
                $message.="<p>员工姓名：".$row["name"]."</p>";
                $message.="<p>员工所在城市：".CGeneral::getCityName($row["city"])."</p>";
                $message.="<p>员工入职日期：".$row["entry_time"]."</p>";
                $message.="<p>合同日期：".date("Y-m-d",strtotime($row["start_time"]))." - ".$row["end_time"]."</p>";
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


        $this->signedContract();
        echo "end";
	}

	//員工錄入后2周提示是否簽署合同
	private function signedContract(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$firstDay - 15 day"));
        $sql = "staff_status=0 and replace(entry_time,'-', '/') ='$firstDay'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $description="员工合同签约提醒 - ".$row["name"];
                $subject="员工合同签约提醒 - ".$row["name"];
                $message="<p>员工编号：".$row["code"]."</p>";
                $message.="<p>员工姓名：".$row["name"]."</p>";
                $message.="<p>员工入职日期：".$row["entry_time"]."</p>";
                $message.="<p>温馨提示：员工是否已签合同？如已签请尽快把已签字合同上传到员工附近处，如未签请尽快处理 </p>";
                $email->setDescription($description);
                $email->setMessage($message);
                $email->setSubject($subject);
                $email->addEmailToPrefixAndCity("ZE01",$row["city"]);
                $email->addEmailToCity($row["city"]);
                var_dump($row["city"]);
                var_dump($email->getToAddr());
                //$email->sent("系统生成");
                $test = new Email();
                $test->addEmailToPrefixAndCity("ZE01","FZ");
                $test->addEmailToCity("FZ");
                var_dump($test->getToAddr());
                //$email->resetToAddr();
            }
            $command->reset();
            $aaa=$command->update('hr_employee', array("signed_bool"=>1),$sql);
        }
    }
}
?>