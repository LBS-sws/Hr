<?php

class ReviewSearchForm extends CFormModel
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
	public $name_list;
	public $login_id;

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
	public $table_foot;
	public $pro_str='';//統計表格的字符串.例如：（甲-乙）

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
            'name_list'=>Yii::t('contract','reviewAllot manager'),
            'review_sum'=>Yii::t('contract','review sum'),

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
			handle_name,name_list,tem_s_ist','safe'),
            array('id','required'),
            array('employee_remark','required'),
            array('id','validateID'),
		);
	}

	public function validateID($attribute, $params){
	    if($this->validateEmployee()){
            $rows = Yii::app()->db->createCommand()->select("id")->from("hr_review")
                ->where("id=:id and employee_id=:employee_id",array(":id"=>$this->id,":employee_id"=>$this->login_id))->queryRow();
            if(!$rows){
                $message = Yii::t('contract','Employee Name'). Yii::t('contract',' not exist');
                $this->addError($attribute,$message);
            }
        }else{
            $message = Yii::t("contract",'The account has no binding staff, please contact the administrator');
            $this->addError($attribute,$message);
        }
	}

    //驗證賬號是否綁定員工
    public function validateEmployee(){
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("employee_id,employee_name")->from("hr_binding")
            ->where('user_id=:user_id',
                array(':user_id'=>$uid))->queryRow();
        if ($rows||Yii::app()->user->validFunction('ZR09')){
            $this->login_id = isset($rows["employee_id"])?$rows["employee_id"]:"";
            return true;
        }
        return false;
    }

	public function retrieveData($index) {
        $city_allow = Yii::app()->user->city_allow();
        //,b.status_type,b.year,b.year_type,b.id as review_id
        $expr_sql = '';
        if(!Yii::app()->user->validFunction('ZR09')){//沒有所有權限
            $expr_sql.=" and (FIND_IN_SET('$this->login_id',b.id_s_list) or b.employee_id = '$this->login_id' or b.lcu = '$this->login_id')";
        }
		$row = Yii::app()->db->createCommand()
            ->select("b.employee_remark,b.review_remark,b.strengths,b.target,b.improve,b.tem_s_ist,b.review_sum,b.name_list,,b.employee_id,c.name,c.code,c.phone,c.city,c.entry_time,d.name as company_name,e.name as dept_name,b.status_type,b.year,b.year_type,b.id")
            ->from("hr_review b")
            ->leftJoin("hr_employee c","c.id = b.employee_id")
            ->leftJoin("hr_company d","c.company_id = d.id")
            ->leftJoin("hr_dept e","c.position = e.id")
            ->where("b.id=:id and b.status_type in (1,2,3) $expr_sql",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->status_type = $row['status_type'];
            //$this->status_type = ReviewAllotList::getReviewStatuts($review['status_type'])["status"];
            $this->name_list = $row['name_list'];
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
//&#10;
            $this->employee_remark = $row['employee_remark'];
            $this->review_remark = '';
            $this->strengths = '';
            $this->target = '';
            $this->improve = '';
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
//員工評語
    protected function resetRemarkList($row){
        $bool = $row['status_type']==3&&($row['handle_id']==$this->login_id||$this->employee_id==$this->login_id||$this->status_type == 3);
        if($bool&&!empty($row["review_remark"])){
            $this->review_remark .=$row["handle_name"].":\r\n".$row["review_remark"]."\r\n";
        }
        if($bool&&!empty($row["strengths"])){
            $this->strengths .=$row["handle_name"].":\r\n".$row["strengths"]."\r\n";
        }
        if($bool&&!empty($row["target"])){
            $this->target .=$row["handle_name"].":\r\n".$row["target"]."\r\n";
        }
        if($bool&&!empty($row["improve"])){
            $this->improve .=$row["handle_name"].":\r\n".$row["improve"]."\r\n";
        }
    }

    public function getTabList(){
        $tabs = array();
        $rows = Yii::app()->db->createCommand()->select("*")->from("hr_review_h")
            ->where("review_id=:review_id",array(":review_id"=>$this->id))->queryAll();
        if(is_array($this->tem_s_ist)&&$rows){
            $this->table_foot = array( //表格底部統計
                'sumNum'=>0,
                'sumList'=>array(),
                'preList'=>array()
            );
            $colspan = count($rows)+1;
            $width = intval(50/$colspan);
            $handleNameHtml = '';
            $handleHtml="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered table-striped'>";
            $handleHtml.="<thead><tr><th colspan='2' width='50%' class='text-center'>".Yii::t("contract","Performance factors")."</th>";
            foreach ($rows as &$row){
                $this->resetRemarkList($row);//員工評語
                $row['list'] = json_decode($row["tem_s_ist"],true);
                $handleHtml.="<th width='$width%'>".$row['handle_per']."(%)</th>";
                $handleNameHtml.="<th>".$row['handle_name']."</th>";
                $this->table_foot["sumList"][] = 0;
                $this->table_foot["preList"][] = $row["handle_per"];
            }
            $handleHtml.="<th width='$width%'>&nbsp;</th></tr>";
            $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","Employee evaluated")."</th><th colspan='$colspan'>".$this->name."</th></tr>";
            $handleHtml.="<tr><th colspan='2' class='text-center'>".Yii::t("contract","Assessment person")."</th>$handleNameHtml<th>".Yii::t("contract","review number")."</th></tr>";
            foreach ($this->tem_s_ist as $set_id => $setList) {
                $this->pro_str = empty($this->pro_str)?"（".$setList['code']."-":$this->pro_str;
                $content = $this->reviewSearchDiv($set_id,$setList,$rows,$handleHtml);
                $tabs[] = array(
                    'label'=>$setList['code']."（".$setList['name']."）",
                    'content'=>"<p>&nbsp;</p>".$content,
                    'active'=>false,
                );
            }
            if (isset($setList['code'])){
                $this->pro_str .= $setList['code']."）";
            }
            $content = $this->getCountTable($handleHtml);
            $tabs[] = array(
                'label'=>Yii::t("contract","review sum"),
                'content'=>"<p>&nbsp;</p>".$content,
                'active'=>true,
            );
        }
        return $tabs;
    }

    protected function reviewSearchDiv($set_id,$setList,$rows,$handleHtml){
        $sum = (count($setList['list'])*10);
        $footArr = array( //表格底部統計
            'sumNum'=>$sum,
            'sumList'=>array(),
            'preList'=>array()
        );
        //表格頭部顯示
        $html=$handleHtml;
        $html.="<tr><th width='1%'>".$setList['code']."</th><th>".$setList['name']."</th>";
        for ($i=0;$i<count($rows);$i++){
            if(!isset($footArr["sumList"][$i])){
                $footArr["sumList"][$i] = 0;
                $footArr["preList"][$i] = $rows[$i]["handle_per"];
            }
            $html.="<th>$sum</th>";
        }
        $html.="<th>".($sum*count($rows))."</th></tr>";
        $html.="</thead><tbody>";
        $num =0;
        //表格內容
        foreach ($setList["list"] as $proList) {
            $num++;
            $this->table_foot["sumNum"]++;
            $html.="<tr><td>$num</td>";
            $html.="<td>".$proList["name"]."</td>";
            $proSum = 0;
            $proArr= array();
            for ($i=0;$i<count($rows);$i++){
                $bool = ($rows[$i]['handle_id']!=$this->login_id&&$this->employee_id!=$this->login_id);
                $bool = in_array($rows[$i]['status_type'],array(1,4))||($bool&&$this->status_type != 3);
                if(!isset($rows[$i]["list"][$set_id]["list"][$proList["id"]]["value"])||$bool){
                    $proSum = '-';
                    $proValue = "-";
                }else{
                    $proValue = $rows[$i]["list"][$set_id]["list"][$proList["id"]]["value"];
                    $footArr["sumList"][$i]+=$proValue;
                    $this->table_foot["sumList"][$i]+=$proValue;
                    $proSum = $proSum+$proValue;
                }
                if(!ReviewHandleForm::scoringOk($proValue)&&isset($rows[$i]["list"][$set_id]["list"][$proList["id"]]["remark"])){
                    $colorList = array("text-danger","text-info","text-warning","text-primary");
                    $key = in_array($i,array(0,1,2,3))?$i:0;
                    $proArr[]=array('color'=>$colorList[$key],'name'=>$rows[$i]['handle_name'],'remark'=>htmlspecialchars($rows[$i]["list"][$set_id]["list"][$proList["id"]]["remark"]));
                }
                $html.="<td>$proValue</td>";
            }
            $html.="<td>$proSum</td>";
            if(!empty($proArr)){
                $html.="<td class='remark'>";
                if(count($proArr)>1){
                    foreach ($proArr as $remarkArr){
                        $html.="<span class='".$remarkArr['color']."'>".$remarkArr["name"]."：".$remarkArr["remark"]."</span><br>";
                    }
                }else{
                    $html.="<span class='".$proArr[0]['color']."'>".$proArr[0]["remark"]."</span>";
                }
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

    public function returnTableFoot($footArr,$str=""){
        $footList = array(
            array("code"=>"A","name"=>Yii::t("contract","Project total score").$str,"list"=>array()),
            array("code"=>"B","name"=>Yii::t("contract","assessed total score"),"list"=>array()),
            array("code"=>"C","name"=>Yii::t("contract","Percentage score")."（B/A*100）","list"=>array()),
            array("code"=>"D","name"=>Yii::t("contract","Scoring ratio"),"list"=>array()),
            array("code"=>"E","name"=>Yii::t("contract","Percentage Sum")."（C*D）","list"=>array()),
        );
        $html = '';
        foreach ($footArr["sumList"] as $key => $sum){
            $footList[0]['list'][$key] = $footArr["sumNum"];
            $footList[1]['list'][$key] = $sum;
            $footList[2]['list'][$key] = sprintf("%.2f",($sum/$footArr["sumNum"])*100);
            $footList[3]['list'][$key] = $footArr["preList"][$key];
            $footList[4]['list'][$key] = sprintf("%.2f",($sum/$footArr["sumNum"])*$footArr["preList"][$key]);
        }
        foreach ($footList as $list){
            $html.="<tr>";
            $html.="<th>".$list["code"]."</th>";
            $html.="<th>".$list["name"]."</th>";
            $sum = 0;
            foreach($list["list"] as $item){
                $html.="<th>".$item."</th>";
                $sum+=$item;
            }
            $html.="<th>$sum</th>";
            $html.="</tr>";
        }
        if(!empty($str)){
            if($this->status_type != 3){
                $reviewLevel = '';
            }else{
                $reviewLevel =$this->getReviewLevelToSum($sum);
            }
            $num = count($footList[0]['list'])+2;
            $html.="<tr><th class='text-right' colspan='$num'>".Yii::t("contract","review grade")."</th><th>$reviewLevel</th></tr>";
        }

        return $html;
    }

    public function getReviewLevelToSum($sum){
        if(!is_numeric($sum)){
            return '';
        }elseif ($sum<50){
            return 'V';
        }elseif ($sum<=59){
            return 'IV';
        }elseif ($sum<=69){
            return 'III';
        }elseif ($sum<=79){
            return 'II';
        }elseif ($sum<=100){
            return 'I';
        }else{
            return $sum;
        }
    }

    protected function getCountTable($html){
        $this->table_foot["sumNum"] = $this->table_foot["sumNum"]*10;
        $sum = 3+count($this->table_foot["sumList"]);
        $html.="</thead><tbody>";
        $html.="<tr><td colspan='$sum'>".Yii::t("contract","Quarterly assessment score")." (100%)</td></tr>";
        $html.="</tbody><tfoot>";
        $html.=$this->returnTableFoot($this->table_foot,$this->pro_str);
        $html.="</tfoot></table></div>";
        //評分級別規則
        $html.="<div class='col-sm-3 col-sm-offset-1'><table class='table table-bordered table-striped'>";
        $html.="<thead><tr><th class='text-center'>评分级别标准</th><th class='text-center'>排 名</th><th class='text-center'>评 级</th></tr></thead>";
        $html.="<tbody>";
        $html.="<tr><th class='text-center'>80 - 100</th><th class='text-center'>Top 20%</th><th class='text-center'>I</th></tr>";
        $html.="<tr><th class='text-center'>70 - 79</th><th class='text-center'>21 - 40%</th><th class='text-center'>II</th></tr>";
        $html.="<tr><th class='text-center'>60 - 69</th><th class='text-center'>41 - 70%</th><th class='text-center'>III</th></tr>";
        $html.="<tr><th class='text-center'>50 - 59</th><th class='text-center'>71 - 90%</th><th class='text-center'>IV</th></tr>";
        $html.="<tr><th class='text-center'>50分以下</th><th class='text-center'>Bottom 10%</th><th class='text-center'>V</th></tr>";
        $html.="</tbody></table></div>";
        $html.="</div>";
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
        $connection->createCommand()->update('hr_review', array(
            'employee_remark'=>$this->employee_remark,
            'luu'=>$uid,
        ), 'id=:id', array(':id'=>$this->id));

		return true;
	}
}
