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
        $aaa = $command->update('hr_employee', array("z_index"=>2),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') <= '$firstday' and replace(test_end_time,'-', '/') >='$firstday'");//試用期
        $command->reset();
        echo "試用期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>1),"staff_status=0 and test_type=1 and replace(test_start_time,'-', '/') >= '$firstday'");//未入職
        $command->reset();
        echo "未入職:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>5),"staff_status=0 and (test_type=0 or replace(test_end_time,'-', '/') <='$firstday')");//正式員工
        $command->reset();
        echo "正式員工:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>4),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') >='$firstday' and replace(end_time,'-', '/') <='$lastday'");//合同即將過期
        $command->reset();
        echo "合同即將過期:$aaa<br>";
        $aaa = $command->update('hr_employee', array("z_index"=>3),"staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') <'$firstday'");//合同過期
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
        $this->contractCitySendEmail();
        $this->contractAgoSendEmail();

        //加班、請假批准后的郵件提示（開始)
        $this->leaveThreeSendEmail();
        $this->leaveSevenSendEmail();
        $this->leaveMoreSendEmail();
        $this->workThreeSendEmail();
        $this->workSevenSendEmail();
        $this->workMoreSendEmail();
        //加班、請假批准后的郵件提示（結束)
        echo "end";
	}

	//員工錄入后2周提示是否簽署合同
	private function signedContract(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstDay = date("Y/m/d");
        $firstDay = date("Y/m/d",strtotime("$firstDay - 14 day"));
        $sql = "staff_status=0 and signed_bool=0 and replace(entry_time,'-', '/') ='$firstDay'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $description="员工合同签约提醒 - ".$row["name"];
                $subject="员工合同签约提醒 - ".$row["name"];
                $message="<p>员工编号：".$row["code"]."</p>";
                $message.="<p>员工姓名：".$row["name"]."</p>";
                $message.="<p>员工入职日期：".$row["entry_time"]."</p>";
                $message.="<p>温馨提示：员工是否已签合同？如已签请尽快把已签字合同上传到员工附件处，如未签请尽快处理 </p>";
                $email->setDescription($description);
                $email->setMessage($message);
                $email->setSubject($subject);
                $email->addEmailToPrefixAndCity("ZE01",$row["city"]);
                $email->addEmailToCity($row["city"]);
                $email->sent("系统生成");
                $email->resetToAddr();
            }
            $command->reset();
            $command->update('hr_employee', array("signed_bool"=>1),$sql);
        }
    }

    //員工合同7天將過期時給地區總監發送郵件
    private function contractCitySendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday + 7 day"));
        $sql = "staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') ='$firstday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $description="【紧急】".$row["name"]."的合同将于".date("Y年m月d日",strtotime($row["end_time"]))."到期,请记得安排续约";
                $subject=$description;
                $message=$description;
                $email->setDescription($description);
                $email->setMessage($message);
                $email->setSubject($subject);
                $email->addEmailToOnlyCityBoss($row["city"]);
                $email->sent("系统生成");
                $email->resetToAddr();
            }
        }
    }
    //員工合同過期10天給饒總發送郵件
    private function contractAgoSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 10 day"));
        $sql = "staff_status=0 and fix_time='fixation' and replace(end_time,'-', '/') ='$firstday'";
        $rows = $command->select("*")->from("hr_employee")->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                $description="【紧急】".$row["name"]."的合同于".date("Y年m月d日",strtotime($row["end_time"]))."已到期";
                $subject=$description;
                $message=$description;
                $email->setDescription($description);
                $email->setMessage($message);
                $email->setSubject($subject);
                $email->addToAddrEmail("joeyiu@lbsgroup.com.cn");
                $email->sent("系统生成");
                $email->resetToAddr();
            }
        }
    }

    //加班附件提示(3天)
    private function workThreeSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 3 day"));
        $sql = "a.status=4 and date_format(a.lud,'%Y/%m/%d') = '$firstday'";
        $rows = $command->select("a.*,b.code,b.name,b.city as s_city")->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                if ($this->docmanSearch("WORKEM", $row["id"], $row["lud"])) {
                    $description = "加班申请附件处还未上传文档 - " . $row["name"];
                    $subject = "加班申请附件处还未上传文档 - " . $row["name"];
                    $message = "<p>员工编号：" . $row["code"] . "</p>";
                    $message .= "<p>员工姓名：" . $row["name"] . "</p>";
                    $message .= "<p>员工城市：" . CGeneral::getCityName($row["s_city"]) . "</p>";
                    $message .= "<p>加班编号：" . $row["work_code"] . "</p>";
                    $message .= "<p>温馨提示：加班“批准”后3天，附件处还未上传文档。 </p>";
                    $email->setDescription($description);
                    $email->setMessage($message);
                    $email->setSubject($subject);
                    $email->addEmailToCity($row["s_city"]);
                    $email->sent("系统生成");
                    $email->resetToAddr();
                }
            }
        }
    }

    //加班附件提示(7天)
    private function workSevenSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 7 day"));
        $sql = "a.status=4 and date_format(a.lud,'%Y/%m/%d') = '$firstday'";
        $rows = $command->select("a.*,b.code,b.name,b.city as s_city")->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row) {
                if ($this->docmanSearch("WORKEM", $row["id"], $row["lud"])) {
                    $description = "加班申请附件处还未上传文档 - " . $row["name"];
                    $subject = "加班申请附件处还未上传文档 - " . $row["name"];
                    $message = "<p>员工编号：" . $row["code"] . "</p>";
                    $message .= "<p>员工姓名：" . $row["name"] . "</p>";
                    $message .= "<p>员工城市：" . CGeneral::getCityName($row["s_city"]) . "</p>";
                    $message .= "<p>加班编号：" . $row["work_code"] . "</p>";
                    $message .= "<p>温馨提示：加班“批准”后7天，附件处还未上传文档。 </p>";
                    $email->setDescription($description);
                    $email->setMessage($message);
                    $email->setSubject($subject);
                    $email->addEmailToOnlyCityBoss($row["s_city"]);
                    $email->sent("系统生成");
                    $email->resetToAddr();
                }
            }
        }
    }

    //加班附件提示(15天)
    private function workMoreSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 15 day"));
        $sql = "a.status=4 and date_format(a.lud,'%Y/%m/%d') = '$firstday'";
        $rows = $command->select("a.*,b.code,b.name,b.city as s_city")->from("hr_employee_work a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                if($this->docmanSearch("WORKEM",$row["id"],$row["lud"])) {
                    $description = "加班申请附件处还未上传文档 - " . $row["name"];
                    $subject = "加班申请附件处还未上传文档 - " . $row["name"];
                    $message = "<p>员工编号：" . $row["code"] . "</p>";
                    $message .= "<p>员工姓名：" . $row["name"] . "</p>";
                    $message .= "<p>员工城市：" . CGeneral::getCityName($row["s_city"]) . "</p>";
                    $message .= "<p>加班编号：" . $row["work_code"] . "</p>";
                    $message .= "<p>温馨提示：加班“批准”后15天，附件处还未上传文档。 </p>";
                    $email->setDescription($description);
                    $email->setMessage($message);
                    $email->setSubject($subject);
                    $email->addToAddrEmail("joeyiu@lbsgroup.com.cn");
                    $email->sent("系统生成");
                    $email->resetToAddr();
                }
            }
        }
    }

    //請假附件提示(3天)
    private function leaveThreeSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 3 day"));
        $sql = "a.status=4 and date_format(a.lud,'%Y/%m/%d') = '$firstday'";
        $rows = $command->select("a.*,b.code,b.name,b.city as s_city")->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                if($this->docmanSearch("LEAVE",$row["id"],$row["lud"])) {
                    $description = "请假申请附件处还未上传文档 - " . $row["name"];
                    $subject = "请假申请附件处还未上传文档 - " . $row["name"];
                    $message = "<p>员工编号：" . $row["code"] . "</p>";
                    $message .= "<p>员工姓名：" . $row["name"] . "</p>";
                    $message .= "<p>员工城市：" . CGeneral::getCityName($row["s_city"]) . "</p>";
                    $message .= "<p>请假编号：" . $row["leave_code"] . "</p>";
                    $message .= "<p>温馨提示：请假“批准”后3天，附件处还未上传文档。 </p>";
                    $email->setDescription($description);
                    $email->setMessage($message);
                    $email->setSubject($subject);
                    $email->addEmailToCity($row["s_city"]);
                    $email->sent("系统生成");
                    $email->resetToAddr();
                }
            }
        }
    }

    //請假附件提示(7天)
    private function leaveSevenSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 7 day"));
        $sql = "a.status=4 and date_format(a.lud,'%Y/%m/%d') = '$firstday'";
        $rows = $command->select("a.*,b.code,b.name,b.city as s_city")->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                if($this->docmanSearch("LEAVE",$row["id"],$row["lud"])) {
                    $description = "请假申请附件处还未上传文档 - " . $row["name"];
                    $subject = "请假申请附件处还未上传文档 - " . $row["name"];
                    $message = "<p>员工编号：" . $row["code"] . "</p>";
                    $message .= "<p>员工姓名：" . $row["name"] . "</p>";
                    $message .= "<p>员工城市：" . CGeneral::getCityName($row["s_city"]) . "</p>";
                    $message .= "<p>请假编号：" . $row["leave_code"] . "</p>";
                    $message .= "<p>温馨提示：请假“批准”后7天，附件处还未上传文档。 </p>";
                    $email->setDescription($description);
                    $email->setMessage($message);
                    $email->setSubject($subject);
                    $email->addEmailToOnlyCityBoss($row["s_city"]);
                    $email->sent("系统生成");
                    $email->resetToAddr();
                }
            }
        }
    }

    //請假附件提示(15天)
    private function leaveMoreSendEmail(){
        $email = new Email();
        $command = Yii::app()->db->createCommand();
        $command->reset();
        $firstday = date("Y/m/d");
        $firstday = date("Y/m/d",strtotime("$firstday - 15 day"));
        $sql = "a.status=4 and date_format(a.lud,'%Y/%m/%d') = '$firstday'";
        $rows = $command->select("a.*,b.code,b.name,b.city as s_city")->from("hr_employee_leave a")
            ->leftJoin("hr_employee b","a.employee_id=b.id")
            ->where($sql)->queryAll();
        if($rows){
            foreach ($rows as $row){
                if($this->docmanSearch("LEAVE",$row["id"],$row["lud"])){
                    $description="请假申请附件处还未上传文档 - ".$row["name"];
                    $subject="请假申请附件处还未上传文档 - ".$row["name"];
                    $message="<p>员工编号：".$row["code"]."</p>";
                    $message.="<p>员工姓名：".$row["name"]."</p>";
                    $message .= "<p>员工城市：" . CGeneral::getCityName($row["s_city"]) . "</p>";
                    $message.="<p>请假编号：".$row["leave_code"]."</p>";
                    $message.="<p>温馨提示：请假“批准”后15天，附件处还未上传文档。 </p>";
                    $email->setDescription($description);
                    $email->setMessage($message);
                    $email->setSubject($subject);
                    $email->addToAddrEmail("joeyiu@lbsgroup.com.cn");
                    $email->sent("系统生成");
                    $email->resetToAddr();
                }
            }
        }
    }

    //請假、加班附件變更查詢
    private function docmanSearch($docType,$id,$date){
        $date = date("Y/m/d H:i:s",strtotime($date));
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.lcd")->from("docman$suffix.dm_master a")
            ->leftJoin("docman$suffix.dm_file b","b.mast_id = a.id")
            ->where("a.doc_type_code='$docType' and a.doc_id = '$id' and date_format(b.lcd,'%Y/%m/%d %H:%i:%s') > '$date'")->queryRow();
        if($rows){
            return false;//不需要發送郵件
        }else{
            return true;//需要發送郵件
        }

    }
}
?>