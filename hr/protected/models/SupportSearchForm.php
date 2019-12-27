<?php

class SupportSearchForm extends CFormModel
{
	public $id;
	public $support_code;
	public $apply_date;
	public $apply_num;
	public $apply_type;
	public $service_type;
	public $apply_end_date;
	public $apply_length=1;
	public $apply_remark;
	public $length_type=1;
	public $apply_city;
	public $city_name;
	public $apply_lcu;
	public $update_type;
	public $update_remark;
	public $employee_id;
	public $employee_name;
	public $audit_remark;
	public $tem_list;
	public $tem_s_ist;
	public $tem_str;
	public $tem_sum;
	public $sumNum=0;
	public $review_sum;
	public $status_type=1;
	public $change_num;
	public $early_remark;
	public $early_date;
	public $reject_remark;
	public $city="HK";

	public function attributeLabels()
	{
        return array(
            'support_code'=>Yii::t('contract','support code'),
            'apply_city'=>Yii::t('contract','apply city'),
            'apply_date'=>Yii::t('contract','Start Time'),
            'length_type'=>Yii::t('contract','support length'),
            'apply_end_date'=>Yii::t('contract','End Time'),
            'employee_id'=>Yii::t('contract','support employee'),
            'apply_remark'=>Yii::t('contract','apply remark'),
            'review_sum'=>Yii::t('contract','review sum'),
            'status_type'=>Yii::t('contract','Status'),

            'update_remark'=>Yii::t('contract','update remark'),
            'audit_remark'=>Yii::t('contract','audit remark'),

            'early_remark9'=>Yii::t('contract','early remark'),
            'early_date9'=>Yii::t('contract','early date'),

            'early_remark10'=>Yii::t('contract','renewal remark'),
            'early_date10'=>Yii::t('contract','renewal date'),
            'reject_remark'=>Yii::t('contract','Rejected Remark'),
            'apply_type'=>Yii::t('contract','support type'),
            'service_type'=>Yii::t('contract','service type'),
        );
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city;
        $city_allow = Yii::app()->user->city_allow();
        if(Yii::app()->user->validFunction('ZR11')){
            $sqlEx = " and a.apply_city in ($city_allow) ";
        }else{
            $bindEmployee = BindingForm::getEmployeeIdToUsername();
            $sqlEx = " and a.employee_id=$bindEmployee ";
        }
        $row = Yii::app()->db->createCommand()->select("a.*,b.name as employee_name,c.name as city_name")->from("hr_apply_support a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("security$suffix.sec_city c","c.code = a.apply_city")
            ->where(" a.status_type != 1 $sqlEx and a.id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->support_code = $row['support_code'];
            $this->apply_city = $row['apply_city'];
            $this->city_name = $row['city_name'];
            $this->apply_date = $row['apply_date'];
            $this->apply_end_date = $row['apply_end_date'];
            $this->apply_remark = $row['apply_remark'];
            $this->status_type = $row['status_type'];

            $this->apply_type = $row['apply_type'];
            $this->service_type = $row['service_type'];

            $this->employee_id = $row['employee_id'];
            $this->employee_name = $row['employee_name'];
            $this->update_type = $row['update_type'];
            $this->update_remark = $row['update_remark'];
            $this->audit_remark = $row['audit_remark'];
            $this->length_type = $row['length_type'];
            $this->apply_length = $row['apply_length'];
            $this->tem_str = $row['tem_str'];
            $this->early_date = $row['early_date'];
            $this->early_remark = $row['early_remark'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);
		}
		return true;
	}

    //所有城市
    public function getAllCity(){
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("code,name")->from("security$suffix.sec_city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row['code']] = $row['name'];
            }
        }
        return $arr;
    }

    //獲取支援的員工
    public function getSupportEmployee(){
        $city = empty($this->city)?"HK":$this->city;
        $arr = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("id,code,name")->from("hr_employee")
            ->where("city = '$city'")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $boolList = SupportAuditForm::SupportEmployeeToSates($row['id']);
                $str = $boolList["bool"]?"（已支援）":"（空闲）";
                $arr[$row['id']] = $row['code']." - ".$row['name'].$str;
            }
        }
        return $arr;
    }


    protected function getTableHeaderHtml(){
        $handleHtml="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered table-striped'>";
        $handleHtml.="<thead>";
        $handleHtml.="<tr><th colspan='2' class='text-center' width='70%'>".Yii::t("contract","support employee")."</th><th>".$this->employee_name."</th></tr>";
        $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","apply city")."</th><th>".$this->city_name."</th></tr>";
        //$handleHtml.="</thead>";

        return $handleHtml;
    }

    protected function reviewSearchDiv($setList,$handleHtml){
        $sum = (count($setList['list'])*10)*intval($setList['num_ratio']);
        $footArr = array( //表格底部統計
            'sumNum'=>0,
            'sumSum'=>$sum
        );
        //表格頭部顯示
        $html=$handleHtml;
        $html.="<tr><th width='1%'>".$setList['code']."</th><th>".$setList['name']."</th>";
        $html.="<th>$sum</th></tr>";
        $html.="</thead><tbody>";
        $num =0;
        //表格內容
        foreach ($setList["list"] as $proList) {
            $num++;
            $html.="<tr><td>$num</td>";
            $html.="<td>".$proList["name"]."</td>";
            $proValue = intval($proList["value"])*intval($setList['num_ratio']);
            $this->sumNum+=$proValue;
            $footArr["sumNum"]+=$proValue;
            $html.="<td>$proValue</td>";
            if(key_exists("remark",$proList)){
                $html.="<td class='remark'>";
                $html.="<span class='text-danger'>".$proList["remark"]."</span>";
                $html.="</td>";
            }
            $html.='</tr>';
        }
        $html.="</tbody><tfoot>";
        //表格底部統計
        $html.=$this->returnTableFoot($footArr);

        $html.="</tfoot></table></div></div>";
        return $html;
    }
    protected function returnTableFoot($footArr){
        $sum = ($footArr["sumNum"]/$footArr["sumSum"])*100;
        $footHtml ="<tr><th width='1%'>A</th><th>".Yii::t("contract","Project total score")."</th><th>".$footArr["sumSum"]."</th></tr>";
        $footHtml.="<tr><th>B</th><th>".Yii::t("contract","assessed total score")."</th><th>".$footArr["sumNum"]."</th></tr>";
        $footHtml.="<tr><th>C</th><th>".Yii::t("contract","Percentage score")."（B/A*100）</th><th>".sprintf("%.2f",$sum)."</th></tr>";

        return $footHtml;
    }
    protected function getCountTable($handleHtml){
        $html = $handleHtml."</thead>";
        $html.="<tbody><tr><td colspan='3'>".Yii::t("contract","Evaluate project score")." (100%)</td></tr></tbody>";
        $html.="<tfoot>";
        $html.=$this->returnTableFoot(array('sumNum'=>$this->sumNum,'sumSum'=>($this->tem_sum*10)));
        $html.="</tfoot></table></div></div>";
        return $html;
    }

    public function getTabList($model=''){
        if(empty($model)){
            $model = $this;
        }
        $tabs = array();
        $row = Yii::app()->db->createCommand()->select("tem_sum,apply_city,tem_s_ist,employee_id")->from("hr_apply_support")
            ->where("id=:id",array(":id"=>$model->id))->queryRow();
        if($row){
            $this->tem_s_ist = json_decode($row["tem_s_ist"],true);
            $this->tem_sum = intval($row["tem_sum"]);
            $this->employee_name = YearDayList::getEmployeeNameToId($row["employee_id"]);
            $this->city_name = CGeneral::getCityName($row["apply_city"]);
            $handleHtml=$this->getTableHeaderHtml();
            foreach ($this->tem_s_ist as $set_id => $setList) {
                //$this->pro_str = empty($this->pro_str)?"（".$setList['code']."-":$this->pro_str;
                $content = $this->reviewSearchDiv($setList,$handleHtml);
                $tabs[] = array(
                    'label'=>$setList['code']."（".$setList['name']."）",
                    'content'=>"<p>&nbsp;</p>".$content,
                    'active'=>false,
                );
            }
            $content = "<p>&nbsp;</p>".$this->getCountTable($handleHtml);
            $tabs[] = array(
                'label'=>Yii::t("contract","review sum"),
                'content'=>$content,
                'active'=>true,
            );
        }

        return $tabs;
    }

    public function getHistoryHtml($id){
        $suffix = Yii::app()->params['envSuffix'];
        $html = '<table id="tblFlow" class="table table-bordered table-striped table-hover">';
        $html.= '<thead><tr>';
        $html.='<th>'.Yii::t("contract","Start Time").'</th>';
        $html.='<th>'.Yii::t("contract","End Time").'</th>';
        $html.='<th>'.Yii::t("contract","Operator Time").'</th>';
        $html.='<th>'.Yii::t("contract","Operator User").'</th>';
        $html.='<th>'.Yii::t("contract","Status").'</th>';
        $html.='</tr></thead><tbody>';

        $rows = Yii::app()->db->createCommand()->select("a.start_date,a.end_date,a.status_type,b.disp_name,a.lcd")->from("hr_apply_support_history a")
            ->leftJoin("security$suffix.sec_user b","a.lcu = b.username")
            ->where("a.support_id=:id",array(":id"=>$id))->order("lcd asc")->queryAll();
        if($rows){
            $arr = SupportApplyList::getStatusList();
            foreach ($rows as $row){
                $status = key_exists($row['status_type'],$arr)?$arr[$row['status_type']]:array('status'=>$row['status_type']);
                $html.="<tr>";
                $html.='<td>'.$row['start_date'].'</td>';
                $html.='<td>'.$row['end_date'].'</td>';
                $html.='<td>'.$row['lcd'].'</td>';
                $html.='<td>'.$row['disp_name'].'</td>';
                $html.='<td>'.$status['status'].'</td>';
                $html.="</tr>";
            }
        }
        $html.='</tbody></table>';

        return $html;
    }

    //判断员工是否可以支援
    public function SupportEmployeeToSates($employee_id,$id = 0){
        $sql = !is_numeric($id)?"":" and id !=$id";
        $startDate = empty($this->apply_date)?date("Y/m/d"):date("Y/m/d",strtotime($this->apply_date));
        $endDate = empty($this->apply_end_date)?date("Y/m/d", strtotime("+1 month")):date("Y/m/d",strtotime($this->apply_end_date));
        $row = Yii::app()->db->createCommand()->select("id,support_code")->from("hr_apply_support")
            ->where("status_type not in (1,8) and employee_id=:employee_id and ((date_format(apply_date,'%Y/%m/%d')>'$startDate' and date_format(apply_date,'%Y/%m/%d')<'$endDate') or (date_format(apply_end_date,'%Y/%m/%d')<'$endDate' and date_format(apply_end_date,'%Y/%m/%d')>'$startDate')) $sql",array(":employee_id"=>$employee_id))->queryRow();
        if($row){
            return array("bool"=>true,"code"=>$row["support_code"]);
        }
        return array("bool"=>false);
    }

    //是否只讀
    public function getReadonly(){
        return $this->scenario=='view'||!in_array($this->status_type,array(1,2,3,4));
    }

    //刪除驗證
    public function deleteValidate(){
        $row = Yii::app()->db->createCommand()->select("*")->from("hr_apply_support")
            ->where("id=:id and status_type in (1,8)",array(":id"=>$this->id))->queryRow();
        if($row){
            return true;
        }
        return false;
    }

	public function saveData($serviceStr='edit')
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection,$serviceStr);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection,$serviceStr) {
		$sql = '';
        switch ($serviceStr) {
            case 'reject'://不同意提前結束或續期 8,11
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							reject_remark = :reject_remark, 
							luu = :luu
						where id = :id";
                break;
            case 'renewal'://同意續期 5
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							apply_end_date = :apply_end_date, 
							apply_length = :apply_length, 
							length_type = :length_type, 
							apply_type = 2, 
							luu = :luu
						where id = :id";
                break;
            case 'early'://同意提前結束 7
                $sql = "update hr_apply_support set
							status_type = :status_type, 
							apply_end_date = :apply_end_date, 
							apply_length = :apply_length, 
							length_type = :length_type, 
							luu = :luu
						where id = :id";
                break;
            case 'finish': //完成 7
                $sql = "update hr_apply_support set
							status_type = :status_type , 
							luu = :luu
						where id = :id";
                break;
            case 'undo'://撤回 5
                $sql = "update hr_apply_support set
							status_type = 5 , 
							luu = :luu
						where id = :id";
                break;
            case 'new'://支援 5
                $sql = "insert into hr_apply_support(
							apply_date,apply_remark,apply_end_date,apply_city,apply_lcu,status_type, lcu, luu
							,tem_s_ist,tem_str,tem_sum,employee_id,audit_remark,apply_length,length_type
						) values (
							:apply_date,:apply_remark,:apply_end_date,:apply_city,:apply_lcu,:status_type, :lcu, :luu
							,:tem_s_ist,:tem_str,:tem_sum,:employee_id,:audit_remark,:apply_length,:length_type
						)";
                break;
            case 'edit'://查看、待定、分配 3,4,5
                $sql = "update hr_apply_support set
							apply_date = :apply_date, 
							apply_end_date = :apply_end_date, 
							length_type = :length_type, 
							apply_length = :apply_length, 
							update_type = :update_type, 
							update_remark = :update_remark, 
							audit_remark = :audit_remark, 
							status_type = :status_type, 
							employee_id = :employee_id, 
							tem_str = :tem_str, 
							tem_s_ist = :tem_s_ist, 
							tem_sum = :tem_sum, 
							luu = :luu
						where id = :id";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        //id, city,subject,message,city_id,city_str,staff_id,staff_str,status_type
        if (strpos($sql,':apply_date')!==false)
            $command->bindParam(':apply_date',$this->apply_date,PDO::PARAM_STR);
        if (strpos($sql,':apply_end_date')!==false)
            $command->bindParam(':apply_end_date',$this->apply_end_date,PDO::PARAM_STR);
        if (strpos($sql,':apply_remark')!==false)
            $command->bindParam(':apply_remark',$this->apply_remark,PDO::PARAM_STR);
        if (strpos($sql,':apply_lcu')!==false)
            $command->bindParam(':apply_lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':apply_city')!==false)
            $command->bindParam(':apply_city',$this->apply_city,PDO::PARAM_STR);
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);

        if (strpos($sql,':apply_length')!==false)
            $command->bindParam(':apply_length',$this->apply_length,PDO::PARAM_STR);
        if (strpos($sql,':length_type')!==false)
            $command->bindParam(':length_type',$this->length_type,PDO::PARAM_STR);
        if (strpos($sql,':update_type')!==false)
            $command->bindParam(':update_type',$this->update_type,PDO::PARAM_STR);
        if (strpos($sql,':update_remark')!==false)
            $command->bindParam(':update_remark',$this->update_remark,PDO::PARAM_STR);
        if (strpos($sql,':audit_remark')!==false)
            $command->bindParam(':audit_remark',$this->audit_remark,PDO::PARAM_STR);
        if (strpos($sql,':reject_remark')!==false)
            $command->bindParam(':reject_remark',$this->reject_remark,PDO::PARAM_STR);
        if (strpos($sql,':tem_str')!==false)
            $command->bindParam(':tem_str',$this->tem_str,PDO::PARAM_STR);
        if (strpos($sql,':employee_id')!==false)
            $command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_STR);
        if (strpos($sql,':tem_sum')!==false)
            $command->bindParam(':tem_sum',$this->tem_sum,PDO::PARAM_STR);
        if (strpos($sql,':tem_s_ist')!==false){
            $this->tem_s_ist = json_encode($this->tem_s_ist);
            $command->bindParam(':tem_s_ist',$this->tem_s_ist,PDO::PARAM_STR);
        }

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($serviceStr=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->lenStr();
            Yii::app()->db->createCommand()->update('hr_apply_support', array(
                'support_code'=>$this->support_code
            ), 'id=:id', array(':id'=>$this->id));
        }

        $this->setSupportHistory();//記錄操作并发送邮件
		return true;
	}

	private function setSupportHistory(){
        if (in_array($this->status_type,array(4,5,7,8,11))){
            $this->employee_name = YearDayList::getEmployeeNameToId($this->employee_id);
            $this->city_name = CGeneral::getCityName($this->apply_city);
            $email = new Email();
            $message = "支援编号:".$this->support_code."<br>";
            $message.= "申请城市:".$this->city_name."<br>";
            $message.= "申请时间:".$this->apply_date."<br>";
            $message.= "结束时间:".$this->apply_end_date."<br>";
            $message.= "支援时长:".$this->apply_length.($this->length_type==1?"个月":"天")."<br>";
            $message.= "支援员工:".$this->employee_name."<br>";
            switch ($this->status_type){
                case 4://排隊等候
                    $email->setSubject("支援单（".$this->support_code."） - 排队等候");
                    $status_remark = $this->audit_remark;
                    break;
                case 5://待評分
                    $email->setSubject("支援单（".$this->support_code."） - 待評分");
                    $status_remark = $this->audit_remark;
                    break;
                case 7://已完成
                    $email->setSubject("支援单（".$this->support_code."） - 已完成");
                    $status_remark = $this->audit_remark;
                    break;
                case 8://拒絕提前結束
                    $email->setSubject("支援单（".$this->support_code."） - 拒絕提前結束");
                    $status_remark = $this->reject_remark;
                    break;
                case 11://拒絕續期
                    $email->setSubject("支援单（".$this->support_code."） - 拒絕續期");
                    $status_remark = $this->reject_remark;
                    break;
                default:
                    return false;
            }
            $message.= "审核备注:$status_remark<br>";
            $email->setMessage($message);
            $email->addEmailToPrefixAndOnlyCity("AY01",$this->apply_city);//該城市有申請權限的人收到郵件
            $email->sent();
            Yii::app()->db->createCommand()->insert('hr_apply_support_history', array(
                'support_id'=>$this->id,
                'start_date'=>$this->apply_date,
                'end_date'=>$this->apply_end_date,
                'apply_length'=>$this->apply_length,
                'length_type'=>$this->length_type,
                'status_type'=>$this->status_type,
                'status_remark'=>$status_remark,
                'lcu'=>Yii::app()->user->id,
            ));
        }
    }

    private function lenStr(){
        $code = strval($this->id);
        $this->support_code = "U";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->support_code.="0";
        }
        $this->support_code .= $code;
    }

	protected function sendEmail(){
        //request_dt
        $email = new Email($this->subject,$this->message);
        if(empty($this->city_id)){
            $email->addEmailToAllCity();
        }else{
            $cityList = explode("~",$this->city_id);
            foreach ($cityList as $city){
                $email->addEmailToCity($city);
            }
        }

        if(!empty($this->staff_id)){
            $staffList = explode("~",$this->staff_id);
            foreach ($staffList as $staff){
                $email->addEmailToLcu($staff);
            }
        }
        $email->sent('','',$this->request_dt);
    }
}
