<?php

class ReviewHandleForm extends CFormModel
{
	public $id;
	public $employee_id;
	public $city;
	public $name;
	public $entry_time;
	public $company_name;
	public $dept_name;
	public $status_type;
	public $year_type;
	public $review_id;
	public $code;
	public $phone;
	public $year;

    public $employee_remark;
    public $review_remark;
    public $strengths;
    public $target;
    public $improve;

    public $handle_id;
    public $handle_name;
    public $handle_per;
    public $review_sum;

	public $tem_s_ist;//审核权限的序列化
	public $tem_sum;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'dept_name'=>Yii::t('contract','Position'),
            'company_name'=>Yii::t('contract','Company Name'),
            'status_type'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'year'=>Yii::t('contract','what year'),
            'year_type'=>Yii::t('contract','year type'),
            'handle_name'=>Yii::t('contract','reviewAllot manager'),
            'handle_per'=>Yii::t('contract','manager percent'),

            'employee_remark'=>Yii::t('contract','employee remark'),
            'review_remark'=>Yii::t('contract','review remark'),
            'strengths'=>Yii::t('contract','employee strengths'),
            'target'=>Yii::t('contract','employee target'),
            'improve'=>Yii::t('contract','employee improve'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,employee_id, name,code,phone,dept_name,company_name,handle_per,status_type,city,entry_time,year,year_type,handle_id,
			handle_name,tem_s_ist,employee_remark,review_remark,strengths,target,improve','safe'),
            array('id','required'),
            array('tem_s_ist','required'),
            array('id','validateID'),
            array('tem_s_ist','validateList'),
		);
	}

	public function validateID($attribute, $params){
	    if($this->validateEmployee()){
            $rows = Yii::app()->db->createCommand()->select("id,handle_per,tem_sum,review_id")->from("hr_review_h")
                ->where("id=:id and handle_id=:handle_id",array(":id"=>$this->id,":handle_id"=>$this->handle_id))->queryRow();
            if(!$rows){
                $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }else{
                $this->review_id = $rows["review_id"];
                $this->handle_per = $rows["handle_per"];
                $this->tem_sum = $rows["tem_sum"];
            }
        }else{
            $message = Yii::t("contract",'The account has no binding staff, please contact the administrator');
            $this->addError($attribute,$message);
        }
	}

    public function validateList($attribute, $params){
        if(!empty($this->tem_s_ist)){
            $rows = Yii::app()->db->createCommand()->select("tem_s_ist")->from("hr_review")
                ->where("id=:id",array(":id"=>$this->review_id))->queryRow();
            if($rows){
                $this->review_sum = 0;
                $rows = json_decode($rows["tem_s_ist"],true);
                foreach ($rows as $key => &$row){
                    foreach ($row["list"] as &$item){
                        if(isset($this->tem_s_ist[$key]['list'][$item['id']])){
                            $item = $this->tem_s_ist[$key]['list'][$item['id']];
                        }else{
                            $message = $item['name'].Yii::t("contract"," can not be empty");
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if (!is_numeric($item["value"])){
                            $message = Yii::t('contract','review score'). Yii::t('contract',' Must be Numbers');
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if($item["value"]>10 || $item["value"]<0){
                            $message = Yii::t('contract','review score').'必须在0至10之间';
                            $this->addError($attribute,$message);
                            return false;
                        }
                        if(!$this->scoringOk($item["value"])){
                            if(!isset($item["remark"])||empty($item["remark"])){
                                $message = Yii::t('contract','Scoring remark')."（".$item['name']."）".Yii::t("contract"," can not be empty");
                                $this->addError($attribute,$message);
                                return false;
                            }
                        }
                        $this->review_sum+=intval($item["value"]);
                    }
                }
                $this->tem_s_ist = $rows;
            }else{
                $message = Yii::t('contract','reviewAllot project').Yii::t("contract"," can not be empty");
                $this->addError($attribute,$message);
                return false;
            }
        }
    }

    //評分安全數字範圍
    public function scoringOk($num){
        if(is_numeric($num)){
            $num = intval($num);
            if(in_array($num,array(6,7,8))){
                return true;
            }
        }
        return false;
    }

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows){
            $this->handle_id = $rows["employee_id"];
            return $rows["employee_id"];
        }
        return false;
    }

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        //,b.status_type,b.year,b.year_type,b.id as review_id
		$row = Yii::app()->db->createCommand()
            ->select("a.review_remark,a.strengths,a.target,a.improve,b.employee_remark,a.review_sum,a.handle_name,a.handle_per,a.tem_s_ist,a.review_id,b.employee_id,c.name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,a.status_type,b.year,b.year_type,a.id")
            ->from("hr_review_h a")
            ->leftJoin("hr_review b","a.review_id = b.id")
            ->leftJoin("hr_employee c","c.id = b.employee_id")
            ->leftJoin("hr_company d","c.company_id = d.id")
            ->leftJoin("hr_dept e","c.position = e.id")
            ->where("a.id=:id and a.handle_id = :handle_id and a.status_type in (1,4)",array(":id"=>$index,":handle_id"=>$this->handle_id))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->status_type = $row['status_type'];
            //$this->status_type = ReviewAllotList::getReviewStatuts($review['status_type'])["status"];
            $this->handle_name = $row['handle_name'];
            $this->handle_per = $row['handle_per'];
            $this->review_id = $row['review_id'];
            $this->tem_s_ist = json_decode($row['tem_s_ist'],true);
            $this->year = $row['year'];
            $this->year_type = $row['year_type'];
            $this->employee_id = $row['employee_id'];
            $this->name = $row['name'];
            $this->city = $row["city"];
            $this->entry_time = $row['entry_time'];
            $this->company_name = $row['company_name'];
            $this->dept_name = $row['dept_name'];
            $this->code = $row['code'];
            $this->phone = $row['phone'];
            $this->review_sum = $row['review_sum'];

            $this->employee_remark = $row['employee_remark'];
            $this->review_remark = $row['review_remark'];
            $this->strengths = $row['strengths'];
            $this->target = $row['target'];
            $this->improve = $row['improve'];
            return true;
		}else{
		    return false;
        }
	}

	public function getReadonly(){
        if ($this->getScenario()=='view'){
            return true;//只读
        }else{
            return false;
        }
    }

    public function getReviewNumList(){
        return array(
            0=>Yii::t("contract","zero"),
            1=>Yii::t("contract","Behave extremely badly"),
            2=>Yii::t("contract","Bad performance"),
            3=>Yii::t("contract","Behave badly"),
            4=>Yii::t("contract","Poor performance"),
            5=>Yii::t("contract","Performance can be"),
            6=>Yii::t("contract","Performance standards"),
            7=>Yii::t("contract","Stable performance"),
            8=>Yii::t("contract","perform well"),
            9=>Yii::t("contract","Good performance"),
            10=>Yii::t("contract","Excellent performance"),
        );
    }

    public function reviewHandleDiv(){
        $bool = $this->getReadonly();
        $className = get_class($this);
        $html = '';
        if(is_array($this->tem_s_ist)){
            foreach ($this->tem_s_ist as $set_id =>$items){
                $html.=TbHtml::hiddenField($className."[tem_s_ist][$set_id][code]",$items['code']);
                $html.=TbHtml::hiddenField($className."[tem_s_ist][$set_id][name]",$items['name']);
                $html.="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered table-striped'>";
                $html.="<thead><tr><th colspan='2'>".$items['code']."（".$items['name']."）</th></tr></thead><tbody>";

                foreach ($items['list'] as $key =>$item){
                    if(!is_array($item)){
                        return '';
                    }//
                    $item['value'] = isset($item['value'])?$item['value']:6;
                    $num = $key;
                    $name = $className."[tem_s_ist][$set_id][list][$num]";
                    $html.="<tr data-name='$name'><td width='50%'>".$item['name']."</td>";
                    $html.="<td>";
                    $html.=TbHtml::hiddenField($name."[name]",$item['name']);
                    $html.=TbHtml::dropDownList($name."[value]",$item['value'],$this->getReviewNumList(),array('class'=>'form-control changeSelect'));
                    $html.="</td>";
                    if(!$this->scoringOk($item['value'])){
                        $item['remark'] = isset($item['remark'])?$item['remark']:'';
                        $html.="<td class='remark'>";
                        $html.=TbHtml::textArea($name."[remark]",$item['remark'],array('rows'=>1));
                        $html.="</td>";
                    }
                    $html.="</tr>";
                }

                $html.="</tbody></table></div></div>";
            }
        }
        return $html;
    }

    //刪除驗證
    public function deleteValidate(){
        return false;
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		//$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			//$transaction->commit();
		}
		catch(Exception $e) {
			//$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
        $uid = Yii::app()->user->id;
        switch ($this->scenario) {
            case 'edit':
                $connection->createCommand()->update('hr_review_h', array(
                    'tem_s_ist'=>json_encode($this->tem_s_ist),
                    'review_sum'=>$this->review_sum,
                    'status_type'=>$this->status_type,
                    'review_remark'=>$this->review_remark,
                    'strengths'=>$this->strengths,
                    'target'=>$this->target,
                    'improve'=>$this->improve,
                    'luu'=>$uid,
                ), 'id=:id', array(':id'=>$this->id));
                break;
        }

        $this->sendReview($connection);
		return true;
	}

	protected function sendReview($connection){
        if($this->status_type == 3){ //考核完成
            $status_type = 3;//2:部分考核完成（兩個經理時，有一個已經完成）
            $review_sum = $this->sumReview($this->review_sum,$this->handle_per,$this->tem_sum);
            $rows = Yii::app()->db->createCommand()->select("*")->from("hr_review_h")
                ->where('review_id=:review_id',
                    array(':review_id'=>$this->review_id))->queryAll();
            if($rows){
                foreach ($rows as $row){
                    if ($row['id']!=$this->id){ //不計算自己
                        if($row['status_type']!=3){
                            $status_type = 2;
                        }
                        $row["review_sum"] = empty($row["review_sum"])?0:intval($row["review_sum"]);
                        //$review_sum+=$row["review_sum"];
                        $review_sum+=$this->sumReview($row["review_sum"],$row["handle_per"],$row["tem_sum"]);
                    }
                }
            }
            $connection->createCommand()->update("hr_review", array(
                'status_type'=>$status_type,
                'review_sum'=>$status_type==3?$review_sum:null
            ), 'id=:id', array(':id'=>$this->review_id));
            if($status_type == 3){
                $this->sendEmail($review_sum,$rows);
            }
        }
    }

    protected function sendEmail($review_sum,$rows){
        $email = new Email();
        $description="人才優化評核完成 - ".$this->name."(".$this->year.ReviewAllotList::getYearTypeList($this->year_type).")";
        $subject=$description;
        $colspan = count($rows)+1;
        $width = intval(50/$colspan);
        $message="<p>员工编号：".$this->code."</p>";
        $message.="<p>员工姓名：".$this->name."</p>";
        $message.="<p>入职时间：".$this->entry_time."</p>";
        $message.="<p>员工职位：".$this->dept_name."</p>";
        $message.="<p>评核总得分：$review_sum</p>";
        $message.="<table width='600px' border='1px'>";
        $message.="<thead><tr><th colspan='2' width='50%'>表现因素</th>";
        $footArr = array( //表格底部統計
            'sumNum'=>0,
            'sumList'=>array(),
            'preList'=>array()
        );
        $handleNameHtml = "";
        foreach ($rows as $row){
            $email->addEmailToStaffId($row['handle_id']);//添加考核人郵箱
            $message.="<th width='$width%'>".$row['handle_per']."(%)</th>";
            $handleNameHtml.="<th>".$row['handle_name']."</th>";
            $footArr['sumNum'] = $row['tem_sum']*10;
            $footArr['sumList'][] = $row['review_sum'];
            $footArr['preList'][] = $row['handle_per'];
        }
        $message.="<th width='$width%'>&nbsp;</th></tr>";
        $message.="<tr><th colspan='2'>被评核员工</th><th colspan='$colspan'>".$this->name."</th></tr>";
        $message.="<tr><th colspan='2'>做出评核之员工</th>$handleNameHtml<th>总分</th></tr>";
        $message.="</thead><tbody><tr><td colspan='".($colspan+2)."'>季度评核得分 (100%)</td></tr></tbody>";
        $message.="<tfoot>";
        $message.=ReviewSearchForm::returnTableFoot($footArr);
        $message.="</tfoot>";
        $message.="</table>";
        $email->setDescription($description);
        $email->setMessage($message);
        $email->setSubject($subject);
        $email->addEmailToStaffId($this->employee_id);//添加備考人郵箱
        $email->sent();
    }

    public function sumReview($sum,$pro,$num){
        $sum = intval($sum);
        $pro = intval($pro);
        $num = intval($num)*10;
        return sprintf("%.2f",($sum/$num)*$pro);
    }
}
