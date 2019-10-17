<?php

class ReviewAllotForm extends CFormModel
{
	public $employee_id;
	public $employee_name;
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
	public $id_list;
	public $id_s_list;
	public $name_list;

	public $tem_s_ist;//审核权限的序列化
	public $tem_str;
	public $tem_list;
	public $tem_sum;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('contract','Employee Name'),
            'code'=>Yii::t('contract','Employee Code'),
            'phone'=>Yii::t('contract','Employee Phone'),
            'dept_name'=>Yii::t('contract','Position'),
            'company_name'=>Yii::t('contract','Company Name'),
            'contract_id'=>Yii::t('contract','Contract Name'),
            'status_type'=>Yii::t('contract','Status'),
            'city'=>Yii::t('contract','City'),
            'entry_time'=>Yii::t('contract','Entry Time'),
            'year'=>Yii::t('contract','what year'),
            'year_type'=>Yii::t('contract','year type'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('employee_id, name,code,phone,dept_name,company_name,contract_id,status_type,city,entry_time,year,year_type,id_list,
			tem_str,tem_list','safe'),
            array('employee_id','required'),
            array('id_list','required'),
            array('tem_list','required'),
            array('employee_id','validateName'),
            array('id_list','validateIdList'),
            array('tem_list','validateList'),
		);
	}

	public function validateName($attribute, $params){
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select("name")->from("hr_employee")
            ->where("id=:id and city in ($city_allow) AND staff_status = 0",array(":id"=>$this->employee_id))->queryRow();
        if($rows){
            $this->employee_name = $rows["name"];
            $rows = Yii::app()->db->createCommand()->select("id")->from("hr_review")
                ->where("employee_id=:id AND year = :year AND year_type = :year_type",array(":id"=>$this->employee_id,":year"=>$this->year,":year_type"=>$this->year_type))->queryRow();
            if($rows){
                $this->review_id = $rows["id"];
                $this->setScenario("edit");
            }else{
                $this->setScenario("new");
            }
        }else{
            $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
            $this->addError($attribute,$message);
        }
	}

    public function validateList($attribute, $params){
        if(!empty($this->tem_list)){
            $arr = array();
            $tem_s_list = array();
            $this->tem_sum = 0;
            foreach ($this->tem_list as $key => $list){
                if($list=='on'){
                    $rows = Yii::app()->db->createCommand()->select("a.id,a.pro_name,a.set_id,b.set_code,b.set_name")->from("hr_set_pro a")
                        ->leftJoin("hr_set b","b.id = a.set_id")
                        ->where('a.id=:id',array(':id'=>$key))->queryRow();
                    if(!$rows){
                        $message = Yii::t('contract','template name'). Yii::t('contract',' not exist');
                        $this->addError($attribute,$message);
                        break;
                    }else{
                        $this->tem_sum++;
                        $arr[] = $key;
                        $tem_s_list[$rows['set_id']]['code']=$rows['set_code'];
                        $tem_s_list[$rows['set_id']]['name']=$rows['set_name'];
                        $tem_s_list[$rows['set_id']]['list'][$this->tem_sum]['id']=$this->tem_sum;
                        $tem_s_list[$rows['set_id']]['list'][$this->tem_sum]['name']=$rows['pro_name'];
                    }
                }
            }
            if (empty($arr)){
                $message = Yii::t('contract','reviewAllot project'). Yii::t('contract',' can not be empty');
                $this->addError($attribute,$message);
                return false;
            }
            $this->tem_str = implode(",",$arr);
            $this->tem_s_ist = $tem_s_list;
        }
    }

	public function validateIdList($attribute, $params){
	    if(!empty($this->id_list)){ //考核經理驗證
            $sum = 0;
            $this->name_list = array();
            $idList =array();
	        foreach ($this->id_list as &$list){
	            if(in_array($list["employee_id"],$idList)){
                    $message = Yii::t('contract','reviewAllot manager'). Yii::t('contract',' can not repeat');
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    $idList[] = $list["employee_id"];
                }
                $rows = Yii::app()->db->createCommand()->select("name")->from("hr_employee")
                    ->where("id=:id",array(":id"=>$list["employee_id"]))->queryRow();
                if(!$rows){
                    $message = Yii::t('contract','reviewAllot manager'). Yii::t('contract',' not exist');
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    $list["employee_name"] = $rows["name"];
                }
                if(empty($list['num'])||!is_numeric($list['num'])){
                    $message = Yii::t('contract','manager percent').Yii::t('contract',' can not be empty');
                    $this->addError($attribute,$message);
                    return false;
                }
                $this->name_list[] = $rows["name"]."（".$list["num"]."%）";
                $sum+=intval($list["num"]);
            }
            if($sum!=100){
                $message = '經理考核佔比必須為100';
                $this->addError($attribute,$message);
                return false;
            }
            $this->id_s_list = implode(",",$idList);
            $this->name_list = implode(",",$this->name_list);
        }
	}

	public function retrieveData($index,$year,$year_type) {
        $city_allow = Yii::app()->user->city_allow();
        //,b.status_type,b.year,b.year_type,b.id as review_id
		$row = Yii::app()->db->createCommand()
            ->select("a.id,a.name,a.code,a.phone,a.city,a.entry_time,c.name as company_name,d.name as dept_name")
            ->from("hr_employee a")
            ->leftJoin("hr_company c","a.company_id = c.id")
            ->leftJoin("hr_dept d","a.position = d.id")
            ->where("a.id=:id and a.city in ($city_allow) AND a.staff_status = 0",array(":id"=>$index))->queryRow();
		if ($row) {
            $review = Yii::app()->db->createCommand()
                ->select("status_type,year,id_list,id_s_list,year_type,tem_str,tem_s_ist,id as review_id")
                ->from("hr_review")
                ->where("employee_id=:id and year = :year and year_type = :year_type",
                    array(
                        ":id"=>$row["id"],
                        ":year"=>$year,
                        ":year_type"=>$year_type,
                    )
                )->queryRow();
            if($review){
                $this->status_type = $review['status_type'];
                //$this->status_type = ReviewAllotList::getReviewStatuts($review['status_type'])["status"];
                $this->review_id = $review['review_id'];
                $this->tem_str = $review['tem_str'];
                $this->id_s_list = $review['id_s_list'];
                $this->id_list = json_decode($review['id_list'],true);
                $this->tem_s_ist = json_decode($review['tem_s_ist'],true);
            }
            $this->year = $year;
            $this->year_type = $year_type;
            $this->employee_id = $row['id'];
            $this->name = $row['name'];
            $this->city = $row["city"];
            $this->entry_time = $row['entry_time'];
            $this->company_name = $row['company_name'];
            $this->dept_name = $row['dept_name'];
            $this->code = $row['code'];
            $this->phone = $row['phone'];
            return true;
		}else{
		    return false;
        }
	}

	public function getReadonly(){
        if ($this->getScenario()=='view'||in_array($this->status_type,array(1,2,3))){
            return true;//只读
        }else{
            return false;
        }
    }

    public function getReviewManagerList($city){
	    $arr = array();
        $cityList = Email::getAllCityToMinCity($city);
        $city_allow = implode("','",$cityList);
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.name,a.code")
            ->from("hr_employee a")
            ->leftJoin("hr_dept d","a.position = d.id")
            ->where("a.city in ('$city_allow') AND a.staff_status = 0")->queryAll();
        foreach ($rows as $row){
            $arr[$row["id"]] = $row["code"]." - ".$row["name"];
        }

        return $arr;
    }

    public function getRowOnly($num,$managerList,$bool,$list=array()){
        if(empty($list)){
            $list = array("employee_id"=>"","num"=>100);
        }
        $className = get_class($this);
        $html = "";
        $html .= "<tr>";
        $html.="<td>".TbHtml::dropDownList($className."[id_list][$num][employee_id]",$list["employee_id"],$managerList,array("class"=>"form-control","readonly"=>$bool))."</td>";
        $html.="<td>".TbHtml::numberField($className."[id_list][$num][num]",$list["num"],array("class"=>"form-control changeNum","readonly"=>$bool))."</td>";
        if(!$bool){
            if(empty($num)){
                $html.="<td>&nbsp;</td>";
            }else{
                $html.="<td>".TbHtml::button(Yii::t("misc","Delete"),array("class"=>"btn btn-danger delManager"))."</td>";
            }
        }else{
            $status_type = "";
            if(!empty($this->review_id)&&!empty($list["employee_id"])){
                $rows = Yii::app()->db->createCommand()->select("status_type")->from("hr_review_h")
                    ->where('review_id=:review_id and handle_id=:id',
                        array(':review_id'=>$this->review_id,':id'=>$list["employee_id"]))->queryRow();
                if($rows){//none review
                    $status_type = $rows["status_type"] == 3?Yii::t("contract","success review"):Yii::t("contract","none review");
                }
            }
            $html.="<td class='text-center'><span style='white-space: nowrap;'>$status_type</span></td>";
        }
        $html.="</tr>";

        return $html;
    }

    public function returnManager(){
        $bool = $this->getReadonly();
        $managerList = $this->getReviewManagerList($this->city);
	    $html ="<table class='table table-bordered table-striped' id='managerTable'><thead><tr><th width='50%'>".Yii::t("contract","reviewAllot manager")."</th><th width='40%'>".Yii::t("contract","manager percent")."</th>";
        $html.='<th>&nbsp;</th>';
        $num = count($this->id_list);
	    $html.="</tr></thead><tbody data-num='$num'>";
        if (empty($this->id_list)){
            $html .= $this->getRowOnly($num,$managerList,$bool);
        }else{
            $i = 0;
            foreach ($this->id_list as $list){
                $html .= $this->getRowOnly($i,$managerList,$bool,$list);
                $i++;
            }
        }
        $html .= "</tbody>";
        if(!$bool){
            $html.="<tfoot><tr><td colspan='2'></td>";
            $html.="<td>".TbHtml::button(Yii::t("misc","Add"),array("class"=>"btn btn-primary","id"=>"addManager"))."</td>";
            $html.="</tr></tfoot>";
        }
        $html.="</table>";
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
            case 'new':
                $connection->createCommand()->insert("hr_review", array(
                    'employee_id'=>$this->employee_id,
                    'year'=>$this->year,
                    'year_type'=>$this->year_type,
                    'id_list'=>json_encode($this->id_list),
                    'id_s_list'=>$this->id_s_list,
                    'name_list'=>$this->name_list,
                    'tem_s_ist'=>json_encode($this->tem_s_ist),
                    'status_type'=>$this->status_type,
                    'tem_str'=>$this->tem_str,
                    'lcu'=>$uid,
                ));
                $this->review_id = Yii::app()->db->getLastInsertID();
                $this->scenario = "edit";
                break;
            case 'edit':
                $connection->createCommand()->update('hr_review', array(
                    'id_list'=>json_encode($this->id_list),
                    'id_s_list'=>$this->id_s_list,
                    'name_list'=>$this->name_list,
                    'tem_s_ist'=>json_encode($this->tem_s_ist),
                    'status_type'=>$this->status_type,
                    'tem_str'=>$this->tem_str,
                    'luu'=>$uid,
                ), 'id=:id', array(':id'=>$this->review_id));
                break;
        }

        $this->sendReview($connection);
		return true;
	}

	protected function sendReview($connection){
        if($this->status_type == 1){ //已發送，需要考核
            $email = new Email();
            $description="新的人才優化評核 - ".$this->employee_name."(".$this->year.ReviewAllotList::getYearTypeList($this->year_type).")";
            $subject=$description;
            $message="<p>员工编号：".$this->code."</p>";
            $message.="<p>员工姓名：".$this->name."</p>";
            $message.="<p>入职时间：".$this->entry_time."</p>";
            $message.="<p>员工职位：".$this->dept_name."</p>";
            $message.="<p>公司名字：".$this->company_name."</p>";
            $message.="<p>考核经理：".$this->name_list."</p>";
            $email->setDescription($description);
            $email->setMessage($message);
            $email->setSubject($subject);
            $email->addEmailToStaffId($this->employee_id);//添加備考人郵箱
            foreach ($this->id_list as $list){ //給考核人分別添加考核表
                $email->addEmailToStaffId($list["employee_id"]);//添加主管郵箱
                $connection->createCommand()->insert("hr_review_h", array(
                    'review_id'=>$this->review_id,
                    'handle_id'=>$list["employee_id"],
                    'handle_name'=>$list["employee_name"],
                    'handle_per'=>$list["num"],
                    'tem_s_ist'=>json_encode($this->tem_s_ist),
                    'tem_sum'=>$this->tem_sum,
                    'lcu'=>Yii::app()->user->id,
                ));
            }
            $email->sent();
        }
    }
}
