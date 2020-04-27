<?php

class SalesReviewForm extends CFormModel
{
	public $id;
	public $city;
	public $year;
	public $year_type;
    public $year_list;
    public $staff_list;
    public $form_list;
	protected $group_list;

	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('contract','ID'),
            'year'=>Yii::t('contract','Time'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city','safe'),
		);
	}

	public function retrieveData($index,$year,$year_type) {
        $suffix = Yii::app()->params['envSuffix'];
        $this->form_list=array();
	    $this->year = !is_numeric($year)?2020:$year;
	    $this->year_type = !is_numeric($year_type)?1:$year_type;
        $this->group_list = SalesGroupForm::getGroupListToId($index);
        $this->resetYearList();//重置年份區間
        $this->getGroupStaff($index);//獲取組內的員工
        $staffSql = " and b.username = ''";
        if(!empty($this->staff_list)){
            $staffSql = " and b.username in ('".implode("','",array_keys($this->staff_list))."') ";
        }
        $minYear = current($this->year_list);
        $maxYear = end($this->year_list);
        $svcList = array("svc_A7","svc_B6","svc_C7","svc_D6","svc_E7","svc_F4","svc_G3");
        $svcSql = implode("','",$svcList);
        $visitObjSql = "";
        //$visitObjSql = " and sales$suffix.VisitObjDesc(b.visit_obj) like '%签单%'";
        $rows = Yii::app()->db->createCommand()->select("a.field_value,a.field_id,b.visit_dt,b.username,")->from("sales$suffix.sal_visit_info a")
            ->leftJoin("sales$suffix.sal_visit b","b.id=a.visit_id")
            ->where("a.field_id in('$svcSql') and (a.field_value+0)>0 and date_format(b.visit_dt,'%Y/%m')>='$minYear' and date_format(b.visit_dt,'%Y/%m')<='$maxYear' $staffSql $visitObjSql",array(":id"=>$index))->queryAll();
		if ($rows) {
		    foreach ($rows as $row){
                $year = date("Y/m",strtotime($row["visit_dt"]));
                $username = $row["username"];
                if(!key_exists($year,$this->form_list)){
                    $this->form_list[$year] = array('sum'=>0,'count'=>0,'item'=>array());
                }
                if(!key_exists($username,$this->form_list[$year]['item'])){
                    $this->form_list[$year]['item'][$username] = array('sales_sum'=>0,'sales_count'=>0);
                }
                $this->form_list[$year]['sum']+=floatval($row["field_value"]);
                $this->form_list[$year]['count']++;
                $this->form_list[$year]['item'][$username]['sales_sum']+=floatval($row["field_value"]);
                $this->form_list[$year]['item'][$username]['sales_count']++;
            }
		}
		return true;
	}

	public function getTableHeader($year){
	    $html = "";
        $html.="<legend>".$year."</legend>";
        $html.="<div class='form-group'><div class='col-sm-10 col-sm-offset-1'><table class='table table-bordered table-striped'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","bill sum")."</th>";
        $html.="<th>".Yii::t("contract","average")."</th>";
        $html.="<th>".Yii::t("contract","deviation")."</th>";
        $html.="<th>".Yii::t("contract","review score")."</th>";
        $html.="<th>&nbsp;</th>";
        $html.="<th>".Yii::t("contract","bill count")."</th>";
        $html.="<th>".Yii::t("contract","average")."</th>";
        $html.="<th>".Yii::t("contract","deviation")."</th>";
        $html.="<th>".Yii::t("contract","review score")."</th>";
        $html.="<th>".Yii::t("contract","review number")."</th>";
        $html.="</tr></thead>";
        //$html.="</table></div></div>";

        return $html;
    }

	public function getTableBody($year){
	    $html = "<tbody>";
	    $count = count($this->staff_list);
	    foreach ($this->staff_list as &$staff){
            $sum = isset($this->form_list[$year]["item"][$staff["user_id"]])?$this->form_list[$year]["item"][$staff["user_id"]]["sales_sum"]:0;
            $allSum = isset($this->form_list[$year]["sum"])?$this->form_list[$year]["sum"]:0;
            $allSum = floatval(sprintf("%.2f",$allSum/$count));
            $num = isset($this->form_list[$year]["item"][$staff["user_id"]])?$this->form_list[$year]["item"][$staff["user_id"]]["sales_count"]:0;
            $allNum = isset($this->form_list[$year]["count"])?$this->form_list[$year]["count"]:0;
            $allNum = floatval(sprintf("%.2f",$allNum/$count));

            if(!key_exists("ranking",$staff)){
                $staff["ranking"]=0;
            }
            $html.="<tr>";
            $html.="<td>".$staff["code"]."</td>";
            $html.="<td>".$staff["name"]."</td>";
            $html.="<td>".$sum."</td>";
            $html.="<td>$allSum</td>";
            $rankingOne = empty($allSum)?0:($sum/$allSum)*100;
            $rankingOne = round($rankingOne);
            $html.="<td>".$rankingOne."%</td>";
            $rankingOne = $this->getRankingToNum($rankingOne);
            $html.="<td>".$rankingOne."</td>";
            $html.="<td>&nbsp;</td>";
            $html.="<td>".$num."</td>";
            $html.="<td>$allNum</td>";
            $rankingTwo = empty($allNum)?0:($num/$allNum)*100;
            $rankingTwo = round($rankingTwo);
            $html.="<td>".$rankingTwo."%</td>";
            $rankingTwo = $this->getRankingToNum($rankingTwo);
            $html.="<td>".$rankingTwo."</td>";
            $rankingSum = ($rankingTwo+$rankingOne)/2;
            $html.="<td>$rankingSum</td>";
            $html.="</tr>";
            $staff["ranking"]+=$rankingSum;
        }

        return $html."</tbody>";
    }

    public function getRankingToNum($num){
        $num = floatval($num);
        if($num>200){
            return 10;
        }elseif($num>150){
            return 9;
        }elseif($num>130){
            return 8;
        }elseif($num>110){
            return 7;
        }elseif($num>=100){
            return 6;
        }elseif($num>90){
            return 5;
        }elseif($num>75){
            return 4;
        }elseif($num>60){
            return 3;
        }elseif($num>45){
            return 2;
        }elseif($num>30){
            return 1;
        }else{
            return 0;
        }
    }

	public function getTabList(){
        $tabs = array();
        foreach ($this->year_list as $year){
            $content = $this->getTableHeader($year);
            $content.=$this->getTableBody($year);
            $content.="</table></div></div>";
            $tabs[] = array(
                'label'=>$year,
                'content'=>"<p>&nbsp;</p>".$content,
                'active'=>false,
            );
        }
        $tabs[] = array(
            'label'=>Yii::t("contract","review number"),
            'content'=>"<p>&nbsp;</p>".$this->getAllSumTable(),
            'active'=>true,
        );
        return $tabs;
    }

    public function getAllSumTable(){
        $html = "";
        $html.="<div class='form-group'><div class='col-sm-5 col-sm-offset-2'><table class='table table-bordered table-striped'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","review number")."</th>";
        $html.="</tr></thead><tbody>";
        foreach ($this->staff_list as $staff){
            $html.="<tr>";
            $html.="<td>".$staff["code"]."</td>";
            $html.="<td>".$staff["name"]."</td>";
            $html.="<td>".sprintf("%.2f",($staff["ranking"]/6))."</td>";
            $html.="</tr>";
        }
        $html.="</tbody></table></div></div>";

	    return $html;
    }

    protected function resetYearList(){
	    $year = $this->year;
	    if($this->year_type == 1){
	        $this->year_list = array("$year"."/04","$year"."/05","$year"."/06","$year"."/07","$year"."/08","$year"."/09");
        }else{
            $this->year_list = array("$year"."/10","$year"."/11","$year"."/12");
            $year++;
            $this->year_list = array_merge($this->year_list,array("$year"."/01","$year"."/02","$year"."/03"));
        }
    }

    protected function getGroupStaff($index){
        $rows = Yii::app()->db->createCommand()->select("b.code,b.name,b.id,c.user_id")->from("hr_sales_staff a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("hr_binding c","c.employee_id = b.id")
            ->where('a.group_id=:group_id',array(':group_id'=>$index))->queryAll();
        if($rows){
            $arr = array();
            $key = 0;
            foreach ($rows as $row){
                $key++;
                $row["user_id"] = empty($row["user_id"])?"null".$key:$row["user_id"];
                $arr[$row["user_id"]] = array("id"=>$row["id"],"code"=>$row["code"],"name"=>$row["name"],"user_id"=>$row["user_id"]);
            }
            $this->staff_list = $arr;
        }else{
            $this->staff_list = array();
        }
    }

    public function getGroupListStr($str){
        if(key_exists($str,$this->group_list)){
            return $this->group_list[$str];
        }else{
            return $str;
        }
    }
}
