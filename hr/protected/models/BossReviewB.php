<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2020/6/15
 * Time: 13:42
 */
class BossReviewB extends BossReview
{

    protected function setListX(){
        $this->listX = array(
            array('value'=>'two_one','name'=>Yii::t("contract","two_one")),//优化人才评核
            array('value'=>'two_two','name'=>Yii::t("contract","two_two")),//月报表分数
            array('value'=>'two_three','name'=>Yii::t("contract","two_three")),//质检拜访量
            array('value'=>'two_four','name'=>Yii::t("contract","two_four"),'pro_str'=>"%"),//高效客诉解决效率
            array('value'=>'two_five','name'=>Yii::t("contract","two_five")),//总经理回馈次数
            array('value'=>'two_six','name'=>Yii::t("contract","two_six"),'pro_str'=>"%"),//提交销售5步曲数量培训销售部分
            array('value'=>'two_seven','name'=>Yii::t("contract","two_seven"),'pro_str'=>"%")//提交销售5步曲数量培训销售经理部分
        );
    }

    protected function setListY(){
        $this->listY = array(
            array('value'=>'two_1','name'=>($this->audit_year-1).Yii::t("contract","one_1"),'function'=>"getOldYear","width"=>"120px",'pro_str'=>"%"),//2018年度数据
            array('value'=>'two_2','name'=>Yii::t("contract","one_12").$this->audit_year.Yii::t("contract","one_3"),'function'=>"getPlanYear",'validate'=>true,"width"=>"160px",'pro_str'=>"%"),//预计2019年目标数据
            array('value'=>'two_3','name'=>Yii::t("contract","one_5"),'function'=>"getPlanYearCof","width"=>"100px"),//系数
            array('value'=>'two_4','name'=>$this->audit_year.Yii::t("contract","one_6"),'function'=>"getNowYear","width"=>"160px",'pro_str'=>"%"),//2019年实际达成数据
            array('value'=>'two_5','name'=>Yii::t("contract","one_8"),'function'=>"getLadderDiffer","width"=>"100px"),//阶梯落差
            array('value'=>'two_6','name'=>Yii::t("contract","one_9"),'function'=>"getLadderCof","width"=>"100px"),//落差系数
            array('value'=>'two_7','name'=>Yii::t("contract","one_10"),'function'=>"getNowCof","width"=>"100px"),//实际系数
            array('value'=>'two_8','name'=>Yii::t("contract","one_11"),'function'=>"getSumRate","width"=>"100px",'static_str'=>"%"),//占比（%）
            array('value'=>'two_9','name'=>Yii::t("contract","three_four"),'function'=>"getSumNumber","width"=>"100px",'static_str'=>"%"),//得分
            array('value'=>'two_10','name'=>Yii::t("contract","Remark"),'function'=>"getRemark","width"=>"160px")//备注
        );
    }

    //2018年度数据 - two_1
    public function getOldYear($type,$str){
        switch ($type){
            case "two_one"://优化人才评核
                $this->json_text[$type][$str] = $this->valueStaffReview($this->employee_id,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_two"://月报表分数
                $this->json_text[$type][$str] = MonthList::getSumAverageByYear($this->audit_year-1,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_three"://质检拜访量
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year-1,"00042");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_four"://高效客诉解决效率 00038  00036
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year-1,array("00038","00036"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
                break;
            case "two_five"://总经理回馈次数
                $this->json_text[$type][$str] = $this->valueFeedback($this->username,$this->audit_year-1);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
                break;
            case "two_six"://提交销售5步曲数量培训销售部分
                $this->json_text[$type][$str] = $this->valueSalesOne($this->audit_year-1,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                $this->json_text[$type][$str] = $this->valueSalesTwo($this->audit_year-1,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
        }
        $this->json_text[$type][$str] = 0;
        return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
    }
    //预计2019年目标数据 - two_2
    public function getPlanYear($type,$str){
        $value = isset($this->json_text[$type][$str])?$this->json_text[$type][$str]:"";
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $ready = $this->ready?"readonly":"";
        if($value === ""){
            $html ="<input type='number' name='$name' value='' data-name='$type' $ready class='form-control planYearB'/>";
        }else{
            $html ="<input type='number' name='$name' value='$value' data-name='$type' $ready class='form-control planYearB'/>";
        }

        if(in_array($type,array("two_four","two_six","two_seven"))){
            $html="<div class='input-group'>$html<span class='input-group-addon'>%</span></div>";
        }
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //系数 - two_3
    public function getPlanYearCof($type,$str){
        $value = $this->json_text[$type]["two_2"];
        $value = $this->cofModel->getClassCof($value,$this->countPrice,$type);//系數
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $html ="<input readonly type='text' name='$name' value='$value' class='form-control planYearBCof'/>";

        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //2019年实际达成数据 - two_4
    public function getNowYear($type,$str){
        switch ($type){
            case "two_one"://优化人才评核
                $this->json_text[$type][$str] = $this->valueStaffReview($this->employee_id,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_two"://月报表分数
                $this->json_text[$type][$str] = MonthList::getSumAverageByYear($this->audit_year,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_three"://质检拜访量
                $this->json_text[$type][$str] = $this->value($this->city,$this->audit_year,"00042");
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_four"://高效客诉解决效率 00038  00036
                $this->json_text[$type][$str] = $this->valueStopToRate($this->city,$this->audit_year,array("00038","00036"));
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
                break;
            case "two_five"://总经理回馈次数
                $this->json_text[$type][$str] = $this->valueFeedback($this->username,$this->audit_year);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
                break;
            case "two_six"://提交销售5步曲数量培训销售部分
                $this->json_text[$type][$str] = $this->valueSalesOne($this->audit_year,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                $this->json_text[$type][$str] = $this->valueSalesTwo($this->audit_year,$this->city);
                return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
        }
        $this->json_text[$type][$str] = 0;
        return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]);
    }
    //阶梯落差 - two_5
    public function getLadderDiffer($type,$str){
        $cofNow = $this->cofModel->getClassCof($this->json_text[$type]["two_4"],$this->countPrice,$type);
        $value = $this->cofModel->getClassLadder($this->json_text[$type]["two_3"],$cofNow,$type,$this->countPrice);
        $name1 = $this->className."[json_text][".$type."]"."[".$str."]";
        $name2 = $this->className."[json_text][".$type."]"."[cofNow]";
        $html = "<input type='hidden' name='$name1' value='$value'><input type='hidden' name='$name2' value='$cofNow'>"."<span>".abs($value)."</span>";
        $this->json_text[$type]["cofNow"] = $cofNow;
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
    //落差系数 - two_6
    public function getLadderCof($type,$str){
        $value = $this->json_text[$type]["two_5"];
        $value = $value>0?$value*0.03:$value*0.08;
        $value += $this->json_text[$type]["two_3"];
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value);
    }
    //实际系数 - two_7
    public function getNowCof($type,$str){
        $value = $this->json_text[$type]["two_6"]+$this->json_text[$type]["two_3"];
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value);
    }
    //占比（%） - two_8
    public function getSumRate($type,$str){
        $value = 0;
        switch ($type){
            case "two_one"://优化人才评核
                $value = 35;
                break;
            case "two_two"://月报表分数
                $value = 35;
                break;
            case "two_three"://质检拜访量
                $value = 10;
                break;
            case "two_four"://高效客诉解决效率
                $value = 5;
                break;
            case "two_five"://总经理回馈次数
                $value = 5;
                break;
            case "two_six"://提交销售5步曲数量培训销售部分
                $value = $this->validateSalesBoos($this->audit_year,$this->city)?5:10;
                break;
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                $value = 10-$this->json_text["two_six"][$str];
                break;
        }
        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$this->json_text[$type][$str]."%");
    }
    //得分 - two_9
    public function getSumNumber($type,$str){
        $value = $this->json_text[$type]["two_7"]*$this->json_text[$type]["two_8"];
        $value = floatval(sprintf("%.3f",$value));
        $this->json_text[$type][$str] = $value;
        $this->scoreSum +=$value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$value."%");
    }
    //备注 - two_10
    public function getRemark($type,$str){
        $value = isset($this->json_text[$type][$str])?$this->json_text[$type][$str]:"";
        $name = $this->className."[json_text][".$type."]"."[".$str."]";
        $ready = $this->ready?"readonly":"";
        $html ="<textarea name='$name' class='form-control' $ready >$value</textarea><input type='hidden'>";

        $this->json_text[$type][$str] = $value;
        return array('value'=>$this->json_text[$type][$str],'name'=>$html);
    }
}