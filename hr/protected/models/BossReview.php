<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/6/15
 * Time: 13:42
 */
class BossReview
{
    public $ready=true;//禁止用戶修改
    public $className="";//表單的name前綴
    public $audit_year=0;//考核年限
    public $city='';//城市
    public $employee_id='';//員工
    public $username='';//賬號
    public $listX=array();
    public $listY=array();
    public $json_text=array();
    public $validate_text=array();//需要驗證的json
    public $cofModel;
    public $scoreSum=0;// 總分數

    protected $countPrice;//年生意額

    protected $searchBool = false;


    public function __construct($model='',$searchBool=false)
    {
        if(!empty($model)){
            $this->username = $model->lcu;
            $this->employee_id = $model->employee_id;
            $this->json_text = $model->json_text;
            $this->audit_year = $model->audit_year;
            $this->city = $model->city;
            $this->ready = $model->getInputBool();
            $this->className = get_class($model);
        }
        $this->searchBool = $searchBool;
        $this->cofModel = new BossReviewCof();
        $this->cofModel->city = $this->city;
        $this->countPrice = $this->value($this->city,$this->audit_year-1,"00002");
        $this->setListX();
        $this->setListY();
    }

    protected function setListX(){
        //array('value'=>'','name'=>'')
        $this->listX = array();
    }

    public function getListX(){
        return $this->listX;
    }

    protected function setListY(){
        $this->listY = array();
    }

    public function validateJson(&$model,$bool=true){
        foreach ($this->listX as $listX) {
            $valueX = $listX["value"];
            foreach ($this->listY as $key => $listY) {
                $valueY = $listY["value"];
                if($bool&&key_exists("validate",$listY)&&$listY["validate"]){
                    if(!isset($model->json_text[$valueX][$valueY])||!is_numeric($model->json_text[$valueX][$valueY])){
                        $message = Yii::t('contract',$valueX)." - ".Yii::t('contract',' can not be empty');
                        $model->addError('json_text',$message);
                        return false;
                    }
                }

                if(key_exists("function",$listY)){
                    call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                }
            }
        }
    }

    //主內容橫向
    public function getTableHtml(){
        $width="170px";
        $html="<p>&nbsp;</p><div class='form-group'><div class='col-lg-12'><div class='table-responsive'><table class='table table-bordered table-hover'>";
        $html.="<thead><tr>";
        $html.="<th width='$width'>".Yii::t("contract","matters")."</th>";
        foreach ($this->listY as $key => $listY){
            if(key_exists("width",$listY)){
                $html.="<th width='".$listY["width"]."'>".$listY["name"]."</th>";
            }else{
                $html.="<th width='170px'>".$listY["name"]."</th>";
            }
        }
        $html.="</tr></thead><tbody>";

        foreach ($this->listX as $listX){
            $html.="<tr>";
            $html.="<td><b>".$listX["name"]."</b></td>";
            foreach ($this->listY as $key => $listY){
                if($this->searchBool){
                    $searchText = !isset($this->json_text[$listX["value"]][$listY["value"]])?0:$this->json_text[$listX["value"]][$listY["value"]];
                    if(isset($listY["static_str"])&&$searchText!=="\\"){
                        $searchText.=$listY["static_str"];
                    }elseif(isset($listY["pro_str"])&&isset($listX["pro_str"])&&$listX["pro_str"]==$listY["pro_str"]){
                        $searchText.=$listY["pro_str"];
                    }
                    $html.="<td>".$searchText."</td>";
                    continue;
                }

                $name = $this->className."[json_text][".$listX["value"]."]"."[".$listY["value"]."]";
                $html.="<td class='".$listY["value"]."'>";
                if(key_exists("function",$listY)){
                    $value = call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                    if(is_array($value)){
                        if (strpos($value['name'],'<input')===false){
                            $html.="<input type='hidden' name='$name' value='".$value['value']."'/>";
                            $html.="<span>".$value['name']."</span>";
                        }else{
                            $html.=$value['name'];
                        }
                    }else{
                        $html.="<input type='hidden' name='$name' value='$value'><span>$value</span>";
                    }
                }elseif(key_exists("text",$listY)){
                    if($this->ready){
                        $html.="<textarea type='text' name='$name' class='form-control' readonly></textarea>";
                    }else{
                        $html.="<textarea type='text' name='$name' class='form-control'></textarea>";
                    }
                }elseif(key_exists("input",$listY)){
                    if(key_exists("ready",$listY)||$this->ready){
                        $html.="<input type='text' name='$name' value='' class='form-control' readonly>";
                    }else{
                        $html.="<input type='text' name='$name' value='' class='form-control'>";
                    }
                }

                $html.="</td>";
            }
            $html.="</tr>";
        }

        $html.="</tbody></table></div></div></div>";
        return $html;
    }

    //主內容橫向
    public function getTableHtmlToEmail(){
        $width="170px";
        $html="";
        $html.="<thead><tr>";
        $html.="<th width='$width'>".Yii::t("contract","matters")."</th>";
        foreach ($this->listY as $key => $listY){
            if(key_exists("emailBool",$listY)&&$listY["emailBool"]){
                if(key_exists("width",$listY)){
                    $html.="<th width='".$listY["width"]."'>".$listY["name"]."</th>";
                }else{
                    $html.="<th width='170px'>".$listY["name"]."</th>";
                }
            }
        }
        $html.="</tr></thead><tbody>";

        foreach ($this->listX as $listX){
            $html.="<tr>";
            $html.="<td><b>".$listX["name"]."</b></td>";
            foreach ($this->listY as $key => $listY){
                if(key_exists("emailBool",$listY)){
                    if(key_exists("function",$listY)){
                        call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                    }
                    if($listY["emailBool"]){
                        $searchText = !isset($this->json_text[$listX["value"]][$listY["value"]])?0:$this->json_text[$listX["value"]][$listY["value"]];
                        $searchText = empty($searchText)?0:$searchText;
                        if(isset($listY["static_str"])&&$searchText!=="\\"){
                            $searchText.=$listY["static_str"];
                        }elseif(isset($listY["pro_str"])&&isset($listX["pro_str"])&&$listX["pro_str"]==$listY["pro_str"]){
                            $searchText.=$listY["pro_str"];
                        }
                        $html.="<td>".$searchText."</td>";
                    }
                }
            }
            $html.="</tr>";
        }

        $html.="</tbody>";
        return $html;
    }

    //主內容豎向（不使用）
    public function getTableHtmlOld(){
        $html="<p>&nbsp;</p><div class='form-group'><div class='col-lg-12'><table class='table table-bordered'>";
        $html.="<thead><tr>";
        $html.="<th>".Yii::t("contract","matters")."</th>";
        foreach ($this->listX as $key => $listX){
            if($key%2 == 0){
                $html.="<th class='info'>".$listX["name"]."</th>";
            }else{
                $html.="<th>".$listX["name"]."</th>";
            }
        }
        $html.="</tr></thead><tbody>";

        foreach ($this->listY as $listY){
            $html.="<tr>";
            $html.="<td><b>".$listY["name"]."</b></td>";
            foreach ($this->listX as $key => $listX){
                $name = $this->className."[json_text][".$listX["value"]."]"."[".$listY["value"]."]";
                if($key%2 == 0){
                    $html.="<td class='info'>";
                }else{
                    $html.="<td>";
                }
                if(key_exists("function",$listY)){
                    $value = call_user_func(array($this,$listY["function"]),$listX["value"],$listY["value"],$listX);
                    if(is_array($value)){
                        if (strpos($value['name'],'<input')===false){
                            $html.="<input type='hidden' name='$name' value='".$value['value']."'>";
                        }
                        $html.="<span>".$value['name']."</span>";
                    }else{
                        $html.="<input type='hidden' name='$name' value='$value'><span>$value</span>";
                    }
                }elseif(key_exists("text",$listY)){
                    if($this->ready){
                        $html.="<textarea type='text' name='$name' class='form-control' readonly></textarea>";
                    }else{
                        $html.="<textarea type='text' name='$name' class='form-control'></textarea>";
                    }
                }elseif(key_exists("input",$listY)){
                    if(key_exists("ready",$listY)||$this->ready){
                        $html.="<input type='text' name='$name' value='' class='form-control' readonly>";
                    }else{
                        $html.="<input type='text' name='$name' value='' class='form-control'>";
                    }
                }

                $html.="</td>";
            }
            $html.="</tr>";
        }

        $html.="</tbody></table></div></div>";
        return $html;
    }

    //提取月报表数据
    public function value($city,$year,$data_field){
        //$data_field 00002:生意額增長 00067:利潤增長 00021:收款率 00017:停單比例 00018:技术员每月平均生产力
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city AND b.year_no = :year AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":field"=>$data_field)
            )->queryScalar();
        return empty($sum)||$sum==null?0:$sum;
    }

    //提取营业报告表数据
    public function valueToOp($city,$year){
        //
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
            ->from("operation$suffix.opr_monthly_dtl a")
            ->leftJoin("operation$suffix.opr_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city 
            AND b.year_no = :year 
            AND a.data_field in ('10005','10004') 
            AND workflow$suffix.RequestStatus('OPRPT',b.id,b.lcd)='ED'",
                array(":city"=>$city,":year"=>$year)
            )->queryScalar();
        return empty($sum)||$sum==null?0:$sum;
    }

    //平均值
    public function valueAverage($city,$year,$data_field='00018'){
        //00018:技术员每月平均生产力
        $suffix = Yii::app()->params['envSuffix'];
        $sum = 0;
        $rows = Yii::app()->db->createCommand()->select("a.data_value")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city AND b.year_no = :year AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":field"=>$data_field)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $sum+=floatval($row["data_value"]);
            }
            $sum=$sum/count($rows);
        }
        return floatval(sprintf("%.2f",$sum));
    }

    //提取月报表数据 (精確到月份)
    public function valueAndMonth($city,$year,$month,$data_field){
        //$data_field 00002:生意額增長 00067:利潤增長 00021:收款率 00017:停單比例 00018:技术员每月平均生产力
        $suffix = Yii::app()->params['envSuffix'];
        $sum = Yii::app()->db->createCommand()->select("SUM(convert(a.data_value,decimal(18,2)))")
            ->from("swoper$suffix.swo_monthly_dtl a")
            ->leftJoin("swoper$suffix.swo_monthly_hdr b","b.id = a.hdr_id")
            ->where("b.city = :city AND b.year_no = :year AND b.month_no = :month AND a.data_field=:field",
                array(":city"=>$city,":year"=>$year,":month"=>$month,":field"=>$data_field)
            )->queryScalar();
        return empty($sum)?0:$sum;
    }

    //服务单的停单比例
    public function valueStopToRate($city,$year,$arr = array("00017","00002")){
        //00017:今月停單生意額   00002:今月生意額  rate = 今月停單/今月的生意額 * 100
        $rows = $this->getValueListToArr($arr,$city,$year);
        $sum = 0;
        if($rows){
            foreach ($rows as $row){
                $row["valueOne"] = floatval($row["valueOne"]);
                $row["valueTwo"] = floatval($row["valueTwo"]);
                $count = empty($row["valueTwo"])||$row["valueTwo"]==0?0:$row["valueOne"]/$row["valueTwo"];
                $count*=100;
                $sum+=$count;
            }
            $sum = $sum/count($rows);
        }
        return floatval(sprintf("%.2f",$sum));
    }

    //收款比例
    public function valueOnToRate($city,$year){
        //00021:今月收款额   00002:今月生意額  rate = 今月收款额/上月的生意額 * 100
        $rows = $this->getValueListToArr(array("00021","00002"),$city,$year);
        $sum = 0;
        if($rows){
            if($rows[0]["month_no"] == 1){
                $valueTwo = $this->valueAndMonth($city,$year-1,12,"00002"); //上一年12月份的生意額
            }else{
                $valueTwo = 0; //上月份的生意額
            }
            foreach ($rows as $key =>$row){
                $row["valueOne"] = floatval($row["valueOne"]);
                $valueTwo = $key==0?$valueTwo:floatval($rows[$key-1]["valueTwo"]);
                $count = empty($valueTwo)||$valueTwo==0?0:$row["valueOne"]/$valueTwo;
                $count*=100;
                $sum+=$count;
            }
            $sum = $sum/count($rows);
        }
        return floatval(sprintf("%.2f",$sum));
    }

    //員工考核分數
    public function valueStaffReview($employee_id,$year){
        $sum = 0;
        $rows = Yii::app()->db->createCommand()->select("review_sum")->from("hr_review")
            ->where("status_type=3 and employee_id=:employee_id and year=:year",array(":year"=>$year,":employee_id"=>$employee_id))
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $sum+=floatval($row["review_sum"]);
            }
            $sum = $sum/count($rows);
        }
        return $sum;
    }

    //总经理回馈次数
    public function valueFeedback($city,$year){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("count(*)")->from("swoper$suffix.swo_mgr_feedback")
            ->where("date_format(request_dt,'%Y')=:year AND city=:city AND status='Y' AND (DATEDIFF(feedback_dt,request_dt)=0 OR DATEDIFF(feedback_dt,request_dt)=1)",
                array(":year"=>$year,":city"=>$city)
            )
            ->queryScalar();
        return empty($row)?0:$row;
    }

    //销售5步曲 - 销售部分
    public function valueSalesOne($year,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $count = 0;
        $rows = Yii::app()->db->createCommand()->select("d.user_id,a.entry_time")->from("hr_binding d")
            ->leftJoin("hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security$suffix.sec_user f","f.username=d.user_id")
            ->where("CONVERT(a.entry_time, SIGNED)=:year AND b.manager_type=1 AND f.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $datetime = date("Y/m/d",strtotime($row["entry_time"]." + 2 month"));
                $bool = Yii::app()->db->createCommand()->select("username")->from("sales$suffix.sal_fivestep")
                    ->where("step in ('1','2','3') and username=:username and date_format(rec_dt,'%Y/%m/%d') <=:datetime",
                        array(":username"=>$row["user_id"],":datetime"=>$datetime)
                    )->queryRow();
                if($bool){
                    $count++;
                }
            }
            $count = ($count/count($rows))*100;
        }
        return floatval(sprintf("%.2f",$count));
    }

    //销售5步曲 - 销售经理部分
    public function valueSalesTwo($year,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $count = 0;
        $rows = Yii::app()->db->createCommand()->select("d.user_id,a.entry_time")->from("hr_binding d")
            ->leftJoin("hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security$suffix.sec_user f","f.username=d.user_id")
            ->where("CONVERT(a.entry_time, SIGNED)=:year AND b.manager_type in (2,3) AND f.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryAll();
        if($rows){
            foreach ($rows as $row){
                $datetime = date("Y/m/d",strtotime($row["entry_time"]." + 2 month"));
                $bool = Yii::app()->db->createCommand()->select("username")->from("sales$suffix.sal_fivestep")
                    ->where("step in ('4','5') and username=:username and date_format(rec_dt,'%Y/%m/%d') <=:datetime",
                        array(":username"=>$row["user_id"],":datetime"=>$datetime)
                    )->queryRow();
                if($bool){
                    $count++;
                }
            }
            $count = ($count/count($rows))*100;
        }
        return floatval(sprintf("%.2f",$count));
    }

    //是否有銷售經理  true：是  false：否
    public function validateSalesBoos($year,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("d.user_id")->from("hr_binding d")
            ->leftJoin("hr_employee a","d.employee_id=a.id")
            ->leftJoin("hr_dept b","a.position=b.id")
            ->leftJoin("security$suffix.sec_user f","f.username=d.user_id")
            ->where("CONVERT(a.entry_time, SIGNED)=:year AND b.manager_type in (2,3) AND f.city=:city",
                array(":year"=>$year,":city"=>$city)
            )->queryRow();
        if($row){
            return true;
        }else{
            return false;
        }
    }

    //將某兩行轉換成兩列 （用於當月的兩個值相除）
    private function getValueListToArr($arr,$city,$year){
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "SELECT b.year_no,b.month_no,
                    SUM(CASE a.data_field WHEN '".$arr[0]."' THEN convert(a.data_value,decimal(18,2)) ELSE 0 END) as valueOne,
                    SUM(CASE a.data_field WHEN '".$arr[1]."' THEN convert(a.data_value,decimal(18,2)) ELSE 0 END) as valueTwo 
                FROM swoper$suffix.swo_monthly_dtl a
                LEFT JOIN swoper$suffix.swo_monthly_hdr b ON a.hdr_id = b.id
                WHERE b.city='$city' AND a.data_field in('".implode("','",$arr)."') AND b.year_no = '$year' 
                GROUP BY b.year_no,b.month_no ORDER BY b.month_no ASC";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        return $rows;
    }

    //年生意額淨增長(不需要)
    public static function sumAmountNetGrowth($year, $city) {
        $suffix = Yii::app()->params['envSuffix'];
        $rtn = 0;
        $sql = "select a.city, a.status, 
					sum(case a.paid_type
							when 'Y' then a.amt_paid
							when 'M' then a.amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.amt_paid
						end
					) as sum_amount,
					sum(case a.b4_paid_type
							when 'Y' then a.b4_amt_paid
							when 'M' then a.b4_amt_paid * 
								(case when a.ctrt_period < 12 then a.ctrt_period else 12 end)
							else a.b4_amt_paid
						end
					) as b4_sum_amount
				from swoper$suffix.swo_service a, swoper$suffix.swo_customer_type b 
				where ((year(a.first_dt)=$year and a.status in ('N'))  
				or (year(a.status_dt)=$year and a.status in ('T','A')))
				and a.cust_type=b.id and b.rpt_cat <> 'INV' 
				AND a.city='$city' 
				group by a.city, a.status
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            $amt_n = 0;
            $amt_a = 0;
            $amt_r = 0;
            $amt_s = 0;
            $amt_t = 0;
            foreach ($rows as $row) {
                switch ($row['status']) {
                    case 'N': $amt_n = $row['sum_amount']; break;
                    case 'A': $amt_a = $row['sum_amount']-$row['b4_sum_amount']; break;
                    case 'R': $amt_r = $row['sum_amount']; break;
                    case 'S': $amt_s = $row['sum_amount']; break;
                    case 'T': $amt_t = $row['sum_amount']; break;
                }
            }
            $rtn = number_format($amt_n+$amt_a+$amt_r-$amt_s-$amt_t,2,'.','');
        }
        return $rtn;
    }

}
