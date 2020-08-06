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

	public function retrieveData($index,$year,$year_type,$city='') {
        $suffix = Yii::app()->params['envSuffix'];
        $this->form_list=array();
	    $this->year = !is_numeric($year)?2020:$year;
	    $this->year_type = (!is_numeric($year_type)||$this->year == 2020)?1:$year_type;
        $this->group_list = SalesGroupForm::getGroupListToId($index);
        $this->resetYearList();//重置年份區間
        $this->getGroupStaff($index,$city);//獲取組內的員工
        $staffKeyList = array_keys($this->staff_list);
        $staffSql = " and b.username = ''";
        if(!empty($this->staff_list)){
            $staffSql = " and b.username in ('".implode("','",$staffKeyList)."') ";
        }
        $minYear = current($this->year_list);
        $maxYear = end($this->year_list);
        $svcList = array("svc_A7","svc_B6","svc_C7","svc_D6","svc_E7","svc_F4","svc_G3");//查詢該屬性的所有金額
        $notList = array("svc_F4","svc_G3");//只計算次數，不計算金額
        $svcSql = implode("','",$svcList);
        $visitObjSql = " and sales$suffix.VisitObjDesc(b.visit_obj) like '%签单%'";
        $rows = Yii::app()->db->createCommand()->select("a.field_value,a.field_id,b.visit_dt,b.username")->from("sales$suffix.sal_visit_info a")
            ->leftJoin("sales$suffix.sal_visit b","b.id=a.visit_id")
            ->where("b.id is not null and a.field_id in('$svcSql') and (a.field_value+0)>0 and date_format(b.visit_dt,'%Y/%m')>='$minYear' and date_format(b.visit_dt,'%Y/%m')<='$maxYear' $staffSql $visitObjSql",array(":id"=>$index))->queryAll();
		if ($rows) {
		    foreach ($rows as $row){
                $year = date("Y/m",strtotime($row["visit_dt"]));
                $username = $row["username"];
                if(in_array($username,$staffKeyList)){
                    $startTime = $this->staff_list[$username]['start_time'];
                    $endTime = $this->staff_list[$username]['end_time'];
                    if($year<$startTime||$year>$endTime){//超出員工的參與時間
                        continue;
                    }
                }else{//員工不存在，不計算
                    continue;
                }
                if(!key_exists($year,$this->form_list)){
                    $this->form_list[$year] = array('sum'=>0,'count'=>0,'item'=>array());
                }
                if(!key_exists($username,$this->form_list[$year]['item'])){
                    $this->form_list[$year]['item'][$username] = array('sales_sum'=>0,'sales_count'=>0);
                }
                $this->form_list[$year]['count']++;
                $this->form_list[$year]['item'][$username]['sales_count']++;
                if(!in_array($row["field_id"],$notList)){
                    $this->form_list[$year]['sum']+=floatval($row["field_value"]);
                    $this->form_list[$year]['item'][$username]['sales_sum']+=floatval($row["field_value"]);
                }
            }
		}
		return true;
	}

	public function getTableHeader($year){
	    $html = "";
        $html.="<legend>".$year."</legend>";
        $html.="<div class='form-group'><div class='col-sm-12'><table class='table table-bordered table-striped'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","Employee Code")."</th>";
        $html.="<th>".Yii::t("contract","Employee Name")."</th>";
        $html.="<th>".Yii::t("contract","bill sum")."</th>";
        $html.="<th>".Yii::t("contract","average num")."</th>";
        $html.="<th>".Yii::t("contract","deviation")."</th>";
        $html.="<th class='text-danger'>".Yii::t("contract","review score")."</th>";
        $html.="<th>".Yii::t("contract","bill count")."</th>";
        $html.="<th>".Yii::t("contract","average num")."</th>";
        $html.="<th>".Yii::t("contract","deviation")."</th>";
        $html.="<th class='text-danger'>".Yii::t("contract","review score")."</th>";
        $html.="<th class='text-danger'>".Yii::t("contract","review number")."</th>";
        $html.="</tr></thead>";
        //$html.="</table></div></div>";

        return $html;
    }

    public function getInstructionsList(){
	    $html="";
        $arr = array(
            array('deviation'=>Yii::t("fete","30% Below"),
                'instruction'=>Yii::t("fete","Performance is not worth any score (dereliction of duty)"),
                'score'=>"0"
            ),
            array('deviation'=>"31%-45%",
                'instruction'=>Yii::t("fete","Extremely poor performance (extremely disappointing performance)"),
                'score'=>"1"
            ),
            array('deviation'=>"46%-60%",
                'instruction'=>Yii::t("fete","Poor performance (disappointing performance)"),
                'score'=>"2"
            ),
            array('deviation'=>"61%-75%",
                'instruction'=>Yii::t("fete","Poor performance (poor performance, far from the company's standards)"),
                'score'=>"3"
            ),
            array('deviation'=>"76%-90%",
                'instruction'=>Yii::t("fete","Poor performance (poor performance, unsatisfactory overall performance)"),
                'score'=>"4"
            ),
            array('deviation'=>"91%-99%",
                'instruction'=>Yii::t("fete","Fair performance (effort, but not up to company standards)"),
                'score'=>"5"
            ),
            array('deviation'=>"100%-110%",
                'instruction'=>Yii::t("fete","Standard performance (performance up to company standards only)"),
                'score'=>"6"
            ),
            array('deviation'=>"111%-130%",
                'instruction'=>Yii::t("fete","Stable performance (excellent performance, overall performance is satisfactory)"),
                'score'=>"7"
            ),
            array('deviation'=>"131%-150%",
                'instruction'=>Yii::t("fete","Perform well (perform competently and meet the company's expectations)"),
                'score'=>"8"
            ),
            array('deviation'=>"151%-200%",
                'instruction'=>Yii::t("fete","Perform well (perform well, exceed the company's expectations)"),
                'score'=>"9"
            ),
            array('deviation'=>Yii::t("fete","Over 200%"),
                'instruction'=>Yii::t("fete","Exceptional performance (performance that is beyond the company's expectations)"),
                'score'=>"10"
            ),
        );
        foreach ($arr as $item){
            $html.="<tr>";
            $html.="<td>".$item["deviation"]."</td>";
            $html.="<td>".$item["instruction"]."</td>";
            $html.="<td>".$item["score"]."</td>";
            $html.="</tr>";
        }
        return $html;
    }

	public function getTableBody($year){
	    $html = "<tbody>";
	    $count = 0;
	    foreach ($this->staff_list as &$staff){//計算該年份有多少個員工
            if($year>=$staff["start_time"]&&$year<=$staff["end_time"]){
                $count++;
            }
        }
	    foreach ($this->staff_list as &$staff){
            if($year>=$staff["start_time"]&&$year<=$staff["end_time"]){
                if(!key_exists("rankingCount",$staff)){
                    $staff["rankingCount"]=0;
                }
                $staff["rankingCount"]++;
            }else{
                continue;
            }
            $sum = isset($this->form_list[$year]["item"][$staff["user_id"]])?$this->form_list[$year]["item"][$staff["user_id"]]["sales_sum"]:0;
            $allSum = isset($this->form_list[$year]["sum"])?$this->form_list[$year]["sum"]:0;
            $allSum = empty($count)?0:floatval(sprintf("%.2f",$allSum/$count));
            $num = isset($this->form_list[$year]["item"][$staff["user_id"]])?$this->form_list[$year]["item"][$staff["user_id"]]["sales_count"]:0;
            $allNum = isset($this->form_list[$year]["count"])?$this->form_list[$year]["count"]:0;
            $allNum = empty($count)?0:floatval(sprintf("%.2f",$allNum/$count));

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
            $html.="<td class='text-danger'><b>".$rankingOne."</b></td>";
            //$html.="<td>&nbsp;</td>";
            $html.="<td>".$num."</td>";
            $html.="<td>$allNum</td>";
            $rankingTwo = empty($allNum)?0:($num/$allNum)*100;
            $rankingTwo = round($rankingTwo);
            $html.="<td>".$rankingTwo."%</td>";
            $rankingTwo = $this->getRankingToNum($rankingTwo);
            $html.="<td class='text-danger'><b>".$rankingTwo."</b></td>";
            $rankingSum = ($rankingTwo+$rankingOne)/2;
            $html.="<td class='text-danger'><b>$rankingSum</b></td>";
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
            $ranking = empty($staff["rankingCount"])?0:$staff["ranking"]/$staff["rankingCount"];
            $html.="<td>".sprintf("%.2f",$ranking)."</td>";
            $html.="</tr>";
        }
        $html.="</tbody></table></div></div>";

	    return $html;
    }

    protected function resetYearList(){
	    $year = $this->year;
	    if($this->year_type == 1){
            if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){//非台灣版
                if($year<2020){
                    $this->year_list = array("$year"."/04","$year"."/05","$year"."/06","$year"."/07","$year"."/08","$year"."/09");
                }elseif ($year==2020){
                    $this->year_list = array("$year"."/04","$year"."/05","$year"."/06","$year"."/07","$year"."/08","$year"."/09","$year"."/10","$year"."/11","$year"."/12");
                }else{
                    $this->year_list = array("$year"."/01","$year"."/02","$year"."/03","$year"."/04","$year"."/05","$year"."/06");
                }
            }else{
                $this->year_list = array("$year"."/01","$year"."/02","$year"."/03","$year"."/04","$year"."/05","$year"."/06");
            }
        }else{
            if(Yii::app()->params['retire']||!isset(Yii::app()->params['retire'])){//非台灣版
                if($year<2020){
                    $this->year_list = array("$year"."/10","$year"."/11","$year"."/12");
                    $year++;
                    $this->year_list = array_merge($this->year_list,array("$year"."/01","$year"."/02","$year"."/03"));
                }else{
                    $this->year_list = array("$year"."/07","$year"."/08","$year"."/09","$year"."/10","$year"."/11","$year"."/12");
                }
            }else{
                $this->year_list = array("$year"."/07","$year"."/08","$year"."/09","$year"."/10","$year"."/11","$year"."/12");
            }
        }
    }

    protected function getGroupStaff($index,$city=''){
        if(empty($city)){
            $city = Yii::app()->user->city();
        }
        $rows = Yii::app()->db->createCommand()->select("b.code,b.name,b.id,c.user_id,a.start_time,a.end_time")->from("hr_sales_staff a")
            ->leftJoin("hr_employee b","a.employee_id = b.id")
            ->leftJoin("hr_binding c","c.employee_id = b.id")
            ->where('a.group_id=:group_id and b.city=:city',array(':group_id'=>$index,':city'=>$city))->queryAll();
        if($rows){
            $arr = array();
            $key = 0;
            foreach ($rows as $row){
                $startTime = $row['start_time'];
                $endTime = $row['end_time'];
                $startTime = empty($startTime) ? "0000/00" : date("Y/m", strtotime($startTime));
                $endTime = empty($endTime) ? "9999/99" : date("Y/m", strtotime($endTime));
                $key++;
                $row["user_id"] = empty($row["user_id"])?"null".$key:$row["user_id"];
                $arr[$row["user_id"]] = array("id"=>$row["id"],"code"=>$row["code"],"name"=>$row["name"],"user_id"=>$row["user_id"],"start_time"=>$startTime,"end_time"=>$endTime,"rankingCount"=>0);
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
