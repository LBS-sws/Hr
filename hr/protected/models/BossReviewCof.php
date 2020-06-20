<?php

/**
 * 等級系數
 * User: 沈超
 * Date: 2020/6/17
 * Time: 13:42
 */
class BossReviewCof
{
    public $class_id='';//

    public function __construct($class_id='')
    {
        $this->class_id=$class_id;
    }

    public function getClassLadder($cofOld,$cofNow,$class_id,$price=''){
        if(!is_numeric($cofOld)||!is_numeric($cofNow)){
            return 0;
        }
        switch ($class_id){
            case "one_one"://年生意额增长目标
                return $this->getLadderToMax($cofOld,$cofNow);
            case "one_two"://年利润额增长目标
                return $this->getLadderToMax($cofOld,$cofNow);
            case "one_three"://年新业务生意额目标
                return $this->getLadderToMin($cofOld,$cofNow);
            case "one_four"://IA服务生意年金额
                return $this->getLadderToMin($cofOld,$cofNow);
            case "one_five"://IB服务生意年金额
                return $this->getLadderToMax($cofOld,$cofNow);
            case "one_six"://收款率(%) -- 有兩個0.2
                return $this->getLadderToMax($cofOld,$cofNow);
            case "one_seven"://服务单的停单比例(%)
                return $this->getLadderToMax($cofOld,$cofNow)*(-1);
            case "one_eight"://技术员每月平均生产力
                return $this->getLadderToMax($cofOld,$cofNow);
            case "two_one"://优化人才评核
                return $this->getLadderToMax($cofOld,$cofNow);
            case "two_two"://月报表分数
                return $this->getLadderToMax($cofOld,$cofNow);
            case "two_three"://质检拜访量
                return $this->getLadderToMaxAndPrice($cofOld,$cofNow,$price);
            case "two_four"://高效客诉解决效率
                return $this->getLadderToMax($cofOld,$cofNow);
            case "two_five"://总经理回馈次数
                return $this->getLadderToMax($cofOld,$cofNow);
            case "two_six"://提交销售5步曲数量培训销售部分
                return $this->getLadderToMax($cofOld,$cofNow);
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                return $this->getLadderToMax($cofOld,$cofNow);
        }
        return 0;
    }

    public function getClassCof($value,$price='',$class_id=''){
        if(!is_numeric($value)){
            return 0;
        }
        if(!in_array($class_id,array("one_six","one_seven","one_eight"))&&empty($price)){
            return 0;
        }
        switch ($class_id){
            case "one_one"://年生意额增长目标
                return $this->getOneCof($value,$price);
            case "one_two"://年利润额增长目标
                return $this->getTwoCof($value,$price);
            case "one_three"://年新业务生意额目标
                return $this->getThreeCof($value,$price);
            case "one_four"://IA服务生意年金额
                return $this->getFourCof($value,$price);
            case "one_five"://IB服务生意年金额
                return $this->getFiveCof($value,$price);
            case "one_six"://收款率(%)
                return $this->getSixCof($value);
            case "one_seven"://服务单的停单比例(%)
                return $this->getSevenCof($value);
            case "one_eight"://技术员每月平均生产力
                return $this->getEightCof($value);
            case "two_one"://优化人才评核
                return $this->getReviewCof($value);
            case "two_two"://月报表分数
                return $this->getMonthCof($value);
            case "two_three"://质检拜访量
                return $this->getNineCof($value,$price);
            case "two_four"://高效客诉解决效率
                return $this->getTenCof($value);
            case "two_five"://总经理回馈次数
                return $this->getManagerCof($value);
            case "two_six"://提交销售5步曲数量培训销售部分
                return $this->getSalesOneCof($value);
            case "two_seven"://提交销售5步曲数量培训销售经理部分
                return $this->getSalesTwoCof($value);
        }
        return 0;
    }

    //年生意额係數 13
    public function getOneCof($value,$price){
        $value = floatval($value);
        $price = floatval($price);
        if($price<=2400000){
            $arr = array(
                array('min'=>-18,'value'=>0),
                array('min'=>-7,'value'=>0.05),
                array('min'=>5,'value'=>0.125),
                array('min'=>11,'value'=>0.2),
                array('min'=>17,'value'=>0.275),
                array('min'=>23,'value'=>0.35),
                array('min'=>29,'value'=>0.425),
                array('min'=>35,'value'=>0.5),
                array('min'=>41,'value'=>0.575),
                array('min'=>47,'value'=>0.65),
                array('min'=>53,'value'=>0.725),
                array('min'=>59,'value'=>0.8),
                array('min'=>71,'value'=>0.875),
                array('min'=>83,'value'=>0.95),
            );
        }elseif ($price<=7200000){
            $arr = array(
                array('min'=>-24,'value'=>0),
                array('min'=>-15,'value'=>0.05),
                array('min'=>-5,'value'=>0.125),
                array('min'=>0,'value'=>0.2),
                array('min'=>5,'value'=>0.275),
                array('min'=>10,'value'=>0.35),
                array('min'=>15,'value'=>0.425),
                array('min'=>20,'value'=>0.5),
                array('min'=>25,'value'=>0.575),
                array('min'=>30,'value'=>0.65),
                array('min'=>35,'value'=>0.725),
                array('min'=>40,'value'=>0.8),
                array('min'=>50,'value'=>0.875),
                array('min'=>60,'value'=>0.95),
            );
        }elseif ($price<=14400000){
            $arr = array(
                array('min'=>-19,'value'=>0),
                array('min'=>-12,'value'=>0.05),
                array('min'=>-4,'value'=>0.125),
                array('min'=>0,'value'=>0.2),
                array('min'=>4,'value'=>0.275),
                array('min'=>8,'value'=>0.35),
                array('min'=>11,'value'=>0.425),
                array('min'=>15,'value'=>0.5),
                array('min'=>19,'value'=>0.575),
                array('min'=>23,'value'=>0.65),
                array('min'=>27,'value'=>0.725),
                array('min'=>31,'value'=>0.8),
                array('min'=>39,'value'=>0.875),
                array('min'=>47,'value'=>0.95)
            );
        }else{
            $arr = array(
                array('min'=>-7,'value'=>0),
                array('min'=>-4,'value'=>0.05),
                array('min'=>0,'value'=>0.125),
                array('min'=>2,'value'=>0.2),
                array('min'=>4,'value'=>0.275),
                array('min'=>6,'value'=>0.35),
                array('min'=>8,'value'=>0.425),
                array('min'=>10,'value'=>0.5),
                array('min'=>12,'value'=>0.575),
                array('min'=>14,'value'=>0.65),
                array('min'=>16,'value'=>0.725),
                array('min'=>18,'value'=>0.8),
                array('min'=>22,'value'=>0.875),
                array('min'=>26,'value'=>0.95)
            );
        }
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //年利润係數 13
    public function getTwoCof($value,$price){
        $value = floatval($value);
        $price = floatval($price);

        if($price<=2400000){
            $arr = array(
                array('min'=>-22,'value'=>0),
                array('min'=>-11,'value'=>0.05),
                array('min'=>1,'value'=>0.125),
                array('min'=>6,'value'=>0.2),
                array('min'=>12,'value'=>0.275),
                array('min'=>18,'value'=>0.35),
                array('min'=>24,'value'=>0.425),
                array('min'=>30,'value'=>0.5),
                array('min'=>36,'value'=>0.575),
                array('min'=>42,'value'=>0.65),
                array('min'=>48,'value'=>0.725),
                array('min'=>54,'value'=>0.8),
                array('min'=>66,'value'=>0.875),
                array('min'=>78,'value'=>0.95),
            );
        }elseif ($price<=14400000){
            $arr = array(
                array('min'=>-19,'value'=>0),
                array('min'=>-10,'value'=>0.05),
                array('min'=>0,'value'=>0.125),
                array('min'=>5,'value'=>0.2),
                array('min'=>10,'value'=>0.275),
                array('min'=>15,'value'=>0.35),
                array('min'=>20,'value'=>0.425),
                array('min'=>25,'value'=>0.5),
                array('min'=>30,'value'=>0.575),
                array('min'=>35,'value'=>0.65),
                array('min'=>40,'value'=>0.725),
                array('min'=>45,'value'=>0.8),
                array('min'=>50,'value'=>0.875),
                array('min'=>55,'value'=>0.95)
            );
        }else{
            $arr = array(
                array('min'=>-15,'value'=>0),
                array('min'=>-8,'value'=>0.05),
                array('min'=>0,'value'=>0.125),
                array('min'=>4,'value'=>0.2),
                array('min'=>8,'value'=>0.275),
                array('min'=>12,'value'=>0.35),
                array('min'=>16,'value'=>0.425),
                array('min'=>20,'value'=>0.5),
                array('min'=>24,'value'=>0.575),
                array('min'=>28,'value'=>0.65),
                array('min'=>32,'value'=>0.725),
                array('min'=>36,'value'=>0.8),
                array('min'=>44,'value'=>0.875),
                array('min'=>52,'value'=>0.95)
            );
        }
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //年新业务係數 12
    public function getThreeCof($value,$price){
        $value = floatval($value);
        $price = floatval($price);

        if($price<=2400000){
            $arr = array(
                array('min'=>-7,'value'=>0),
                array('min'=>0,'value'=>0.125),
                array('min'=>4,'value'=>0.2),
                array('min'=>8,'value'=>0.275),
                array('min'=>12,'value'=>0.35),
                array('min'=>16,'value'=>0.425),
                array('min'=>20,'value'=>0.5),
                array('min'=>24,'value'=>0.575),
                array('min'=>28,'value'=>0.65),
                array('min'=>32,'value'=>0.725),
                array('min'=>36,'value'=>0.8),
                array('min'=>44,'value'=>0.875),
                array('min'=>52,'value'=>0.95),
            );
        }else{
            $arr = array(
                array('min'=>-11,'value'=>0),
                array('min'=>-4,'value'=>0.125),
                array('min'=>-1,'value'=>0.2),
                array('min'=>3,'value'=>0.275),
                array('min'=>7,'value'=>0.35),
                array('min'=>11,'value'=>0.425),
                array('min'=>15,'value'=>0.5),
                array('min'=>19,'value'=>0.575),
                array('min'=>23,'value'=>0.65),
                array('min'=>27,'value'=>0.725),
                array('min'=>31,'value'=>0.8),
                array('min'=>39,'value'=>0.875),
                array('min'=>47,'value'=>0.95),
            );
        }
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //IA生意额係數 12
    public function getFourCof($value,$price){
        $value = floatval($value);
        $price = floatval($price);
        if($price<=2400000){
            $arr = array(
                array('min'=>-7,'value'=>0),
                array('min'=>0,'value'=>0.125),
                array('min'=>4,'value'=>0.2),
                array('min'=>8,'value'=>0.275),
                array('min'=>12,'value'=>0.35),
                array('min'=>16,'value'=>0.425),
                array('min'=>20,'value'=>0.5),
                array('min'=>24,'value'=>0.575),
                array('min'=>28,'value'=>0.65),
                array('min'=>32,'value'=>0.725),
                array('min'=>36,'value'=>0.8),
                array('min'=>44,'value'=>0.875),
                array('min'=>52,'value'=>0.95),
            );
        }elseif($price<=7200000){
            $arr = array(
                array('min'=>-11,'value'=>0),
                array('min'=>-4,'value'=>0.125),
                array('min'=>-1,'value'=>0.2),
                array('min'=>3,'value'=>0.275),
                array('min'=>7,'value'=>0.35),
                array('min'=>11,'value'=>0.425),
                array('min'=>15,'value'=>0.5),
                array('min'=>19,'value'=>0.575),
                array('min'=>23,'value'=>0.65),
                array('min'=>27,'value'=>0.725),
                array('min'=>31,'value'=>0.8),
                array('min'=>39,'value'=>0.875),
                array('min'=>47,'value'=>0.95),
            );
        }elseif($price<=14400000){
            $arr = array(
                array('min'=>-15,'value'=>0),
                array('min'=>-8,'value'=>0.125),
                array('min'=>-2,'value'=>0.2),
                array('min'=>1,'value'=>0.275),
                array('min'=>4,'value'=>0.35),
                array('min'=>7,'value'=>0.425),
                array('min'=>10,'value'=>0.5),
                array('min'=>13,'value'=>0.575),
                array('min'=>16,'value'=>0.65),
                array('min'=>19,'value'=>0.725),
                array('min'=>22,'value'=>0.8),
                array('min'=>28,'value'=>0.875),
                array('min'=>34,'value'=>0.95),
            );
        }else{
            $arr = array(
                array('min'=>-2,'value'=>0),
                array('min'=>0,'value'=>0.125),
                array('min'=>1,'value'=>0.2),
                array('min'=>2,'value'=>0.275),
                array('min'=>4,'value'=>0.35),
                array('min'=>6,'value'=>0.425),
                array('min'=>8,'value'=>0.5),
                array('min'=>10,'value'=>0.575),
                array('min'=>12,'value'=>0.65),
                array('min'=>14,'value'=>0.725),
                array('min'=>16,'value'=>0.8),
                array('min'=>19,'value'=>0.875),
                array('min'=>22,'value'=>0.95),
            );
        }
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //IB生意额係數 13
    public function getFiveCof($value,$price){
        $value = floatval($value);
        $price = floatval($price);
        if($price<=2400000){
            $arr = array(
                array('min'=>-23,'value'=>0),
                array('min'=>-10,'value'=>0.05),
                array('min'=>4,'value'=>0.125),
                array('min'=>11,'value'=>0.2),
                array('min'=>18,'value'=>0.275),
                array('min'=>25,'value'=>0.35),
                array('min'=>33,'value'=>0.425),
                array('min'=>40,'value'=>0.5),
                array('min'=>47,'value'=>0.575),
                array('min'=>54,'value'=>0.65),
                array('min'=>61,'value'=>0.725),
                array('min'=>68,'value'=>0.8),
                array('min'=>82,'value'=>0.875),
                array('min'=>95,'value'=>0.95),
            );
        }elseif ($price<=7200000){
            $arr = array(
                array('min'=>-13,'value'=>0),
                array('min'=>-4,'value'=>0.05),
                array('min'=>6,'value'=>0.125),
                array('min'=>11,'value'=>0.2),
                array('min'=>16,'value'=>0.275),
                array('min'=>21,'value'=>0.35),
                array('min'=>26,'value'=>0.425),
                array('min'=>30,'value'=>0.5),
                array('min'=>35,'value'=>0.575),
                array('min'=>40,'value'=>0.65),
                array('min'=>44,'value'=>0.725),
                array('min'=>49,'value'=>0.8),
                array('min'=>59,'value'=>0.875),
                array('min'=>69,'value'=>0.95),
            );
        }elseif ($price<=14400000){
            $arr = array(
                array('min'=>-15,'value'=>0),
                array('min'=>-8,'value'=>0.05),
                array('min'=>0,'value'=>0.125),
                array('min'=>4,'value'=>0.2),
                array('min'=>8,'value'=>0.275),
                array('min'=>12,'value'=>0.35),
                array('min'=>16,'value'=>0.425),
                array('min'=>20,'value'=>0.5),
                array('min'=>24,'value'=>0.575),
                array('min'=>28,'value'=>0.65),
                array('min'=>32,'value'=>0.725),
                array('min'=>40,'value'=>0.8),
                array('min'=>48,'value'=>0.875),
                array('min'=>56,'value'=>0.95)
            );
        }else{
            $arr = array(
                array('min'=>-12,'value'=>0),
                array('min'=>-7,'value'=>0.05),
                array('min'=>-1,'value'=>0.125),
                array('min'=>2,'value'=>0.2),
                array('min'=>5,'value'=>0.275),
                array('min'=>8,'value'=>0.35),
                array('min'=>12,'value'=>0.425),
                array('min'=>15,'value'=>0.5),
                array('min'=>18,'value'=>0.575),
                array('min'=>21,'value'=>0.65),
                array('min'=>24,'value'=>0.725),
                array('min'=>30,'value'=>0.8),
                array('min'=>36,'value'=>0.875),
                array('min'=>36,'value'=>0.95)
            );
        }
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //收款率係數 13 (特殊情況-1)
    public function getSixCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>89,'value'=>0),
            array('min'=>90,'value'=>0.125),
            array('min'=>92,'value'=>0.2),
            array('min'=>93,'value'=>0.2),
            array('min'=>95,'value'=>0.275),
            array('min'=>96,'value'=>0.35),
            array('min'=>97,'value'=>0.425),
            array('min'=>98,'value'=>0.5),
            array('min'=>99,'value'=>0.575),
            array('min'=>100,'value'=>0.65),
            array('min'=>101,'value'=>0.725),
            array('min'=>102,'value'=>0.8),
            array('min'=>104,'value'=>0.875),
            array('min'=>106,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //停单比例係數 13 (特殊情況-2)
    public function getSevenCof($value){
        $value = floatval($value);
        $arr = array(
            array('max'=>5.5,'value'=>0),
            array('max'=>5.1,'value'=>0.05),
            array('max'=>4.6,'value'=>0.125),
            array('max'=>4.3,'value'=>0.2),
            array('max'=>4.0,'value'=>0.275),
            array('max'=>3.7,'value'=>0.35),
            array('max'=>3.4,'value'=>0.425),
            array('max'=>3.1,'value'=>0.5),
            array('max'=>2.8,'value'=>0.575),
            array('max'=>2.5,'value'=>0.65),
            array('max'=>2.2,'value'=>0.725),
            array('max'=>1.9,'value'=>0.8),
            array('max'=>1.4,'value'=>0.875),
            array('max'=>0.9,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value>=$list['max']){
                return $list['value'];
            }
        }
        return 1;
    }

    //平均生产力係數 13
    public function getEightCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>13000,'value'=>0),
            array('min'=>17000,'value'=>0.05),
            array('min'=>21000,'value'=>0.125),
            array('min'=>23000,'value'=>0.2),
            array('min'=>25000,'value'=>0.275),
            array('min'=>27000,'value'=>0.35),
            array('min'=>29000,'value'=>0.425),
            array('min'=>31000,'value'=>0.5),
            array('min'=>33000,'value'=>0.575),
            array('min'=>35000,'value'=>0.65),
            array('min'=>37000,'value'=>0.725),
            array('min'=>39000,'value'=>0.8),
            array('min'=>43000,'value'=>0.875),
            array('min'=>47000,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //人才評核係數 13
    public function getReviewCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>50,'value'=>0),
            array('min'=>55,'value'=>0.05),
            array('min'=>58,'value'=>0.125),
            array('min'=>60,'value'=>0.2),
            array('min'=>62,'value'=>0.275),
            array('min'=>64,'value'=>0.35),
            array('min'=>66,'value'=>0.425),
            array('min'=>68,'value'=>0.5),
            array('min'=>70,'value'=>0.575),
            array('min'=>72,'value'=>0.65),
            array('min'=>74,'value'=>0.725),
            array('min'=>76,'value'=>0.8),
            array('min'=>79,'value'=>0.875),
            array('min'=>81,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //月报表係數 13
    public function getMonthCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>50,'value'=>0),
            array('min'=>52,'value'=>0.05),
            array('min'=>55,'value'=>0.125),
            array('min'=>57,'value'=>0.2),
            array('min'=>59,'value'=>0.275),
            array('min'=>61,'value'=>0.35),
            array('min'=>63,'value'=>0.425),
            array('min'=>65,'value'=>0.5),
            array('min'=>67,'value'=>0.575),
            array('min'=>69,'value'=>0.65),
            array('min'=>71,'value'=>0.725),
            array('min'=>73,'value'=>0.8),
            array('min'=>76,'value'=>0.875),
            array('min'=>79,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //質檢係數 12或13 (特殊情況-3)
    public function getNineCof($value,$price){
        $value = floatval($value);
        $price = floatval($price);
        if($price<=2400000){
            $arr = array(
                array('min'=>10,'value'=>0),
                array('min'=>15,'value'=>0.125),
                array('min'=>20,'value'=>0.2),
                array('min'=>30,'value'=>0.275),
                array('min'=>40,'value'=>0.35),
                array('min'=>50,'value'=>0.425),
                array('min'=>100,'value'=>0.5),
                array('min'=>150,'value'=>0.575),
                array('min'=>200,'value'=>0.65),
                array('min'=>250,'value'=>0.725),
                array('min'=>300,'value'=>0.8),
                array('min'=>400,'value'=>0.875),
                array('min'=>500,'value'=>0.95),
            );
        }elseif ($price<=7200000){
            $arr = array(
                array('min'=>10,'value'=>0),
                array('min'=>15,'value'=>0.05),
                array('min'=>20,'value'=>0.125),
                array('min'=>30,'value'=>0.2),
                array('min'=>40,'value'=>0.275),
                array('min'=>50,'value'=>0.35),
                array('min'=>100,'value'=>0.425),
                array('min'=>150,'value'=>0.5),
                array('min'=>200,'value'=>0.575),
                array('min'=>250,'value'=>0.65),
                array('min'=>300,'value'=>0.725),
                array('min'=>400,'value'=>0.8),
                array('min'=>500,'value'=>0.875),
                array('min'=>750,'value'=>0.95),
            );
        }elseif ($price<=14400000){
            $arr = array(
                array('min'=>70,'value'=>0),
                array('min'=>50,'value'=>0.05),
                array('min'=>100,'value'=>0.125),
                array('min'=>150,'value'=>0.2),
                array('min'=>200,'value'=>0.275),
                array('min'=>250,'value'=>0.35),
                array('min'=>300,'value'=>0.425),
                array('min'=>400,'value'=>0.5),
                array('min'=>500,'value'=>0.575),
                array('min'=>750,'value'=>0.65),
                array('min'=>1000,'value'=>0.725),
                array('min'=>1250,'value'=>0.8),
                array('min'=>2000,'value'=>0.875),
                array('min'=>2250,'value'=>0.95),
            );
        }elseif ($price<=24000000){
            $arr = array(
                array('min'=>150,'value'=>0),
                array('min'=>200,'value'=>0.05),
                array('min'=>250,'value'=>0.125),
                array('min'=>300,'value'=>0.2),
                array('min'=>400,'value'=>0.275),
                array('min'=>500,'value'=>0.35),
                array('min'=>750,'value'=>0.425),
                array('min'=>1000,'value'=>0.5),
                array('min'=>1250,'value'=>0.575),
                array('min'=>1750,'value'=>0.65),
                array('min'=>2000,'value'=>0.725),
                array('min'=>2250,'value'=>0.8),
                array('min'=>2500,'value'=>0.875),
                array('min'=>2750,'value'=>0.95),
            );
        }else{
            $arr = array(
                array('min'=>200,'value'=>0),
                array('min'=>250,'value'=>0.05),
                array('min'=>300,'value'=>0.125),
                array('min'=>400,'value'=>0.2),
                array('min'=>500,'value'=>0.275),
                array('min'=>750,'value'=>0.35),
                array('min'=>1000,'value'=>0.425),
                array('min'=>1250,'value'=>0.5),
                array('min'=>1750,'value'=>0.575),
                array('min'=>2000,'value'=>0.65),
                array('min'=>2250,'value'=>0.725),
                array('min'=>2500,'value'=>0.8),
                array('min'=>2750,'value'=>0.875),
                array('min'=>3000,'value'=>0.95),
            );
        }
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //解决率係數 13
    public function getTenCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>54,'value'=>0),
            array('min'=>58,'value'=>0.05),
            array('min'=>63,'value'=>0.125),
            array('min'=>67,'value'=>0.2),
            array('min'=>70,'value'=>0.275),
            array('min'=>73,'value'=>0.35),
            array('min'=>76,'value'=>0.425),
            array('min'=>79,'value'=>0.5),
            array('min'=>82,'value'=>0.575),
            array('min'=>85,'value'=>0.65),
            array('min'=>87,'value'=>0.725),
            array('min'=>90,'value'=>0.8),
            array('min'=>93,'value'=>0.875),
            array('min'=>98,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //回馈次数係數 13
    public function getManagerCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>40,'value'=>0),
            array('min'=>70,'value'=>0.05),
            array('min'=>101,'value'=>0.125),
            array('min'=>117,'value'=>0.2),
            array('min'=>133,'value'=>0.275),
            array('min'=>149,'value'=>0.35),
            array('min'=>165,'value'=>0.425),
            array('min'=>181,'value'=>0.5),
            array('min'=>197,'value'=>0.575),
            array('min'=>213,'value'=>0.65),
            array('min'=>229,'value'=>0.725),
            array('min'=>245,'value'=>0.8),
            array('min'=>276,'value'=>0.875),
            array('min'=>307,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //销售5步曲係數 (员工部分) 13
    public function getSalesOneCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>6,'value'=>0),
            array('min'=>15,'value'=>0.05),
            array('min'=>25,'value'=>0.125),
            array('min'=>30,'value'=>0.2),
            array('min'=>35,'value'=>0.275),
            array('min'=>40,'value'=>0.35),
            array('min'=>45,'value'=>0.425),
            array('min'=>50,'value'=>0.5),
            array('min'=>55,'value'=>0.575),
            array('min'=>60,'value'=>0.65),
            array('min'=>65,'value'=>0.725),
            array('min'=>70,'value'=>0.8),
            array('min'=>80,'value'=>0.875),
            array('min'=>90,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //销售5步曲係數 (经理部分) 13
    public function getSalesTwoCof($value){
        $value = floatval($value);
        $arr = array(
            array('min'=>6,'value'=>0),
            array('min'=>15,'value'=>0.05),
            array('min'=>25,'value'=>0.125),
            array('min'=>30,'value'=>0.2),
            array('min'=>35,'value'=>0.275),
            array('min'=>40,'value'=>0.35),
            array('min'=>45,'value'=>0.425),
            array('min'=>50,'value'=>0.5),
            array('min'=>55,'value'=>0.575),
            array('min'=>60,'value'=>0.65),
            array('min'=>65,'value'=>0.725),
            array('min'=>70,'value'=>0.8),
            array('min'=>80,'value'=>0.875),
            array('min'=>90,'value'=>0.95),
        );
        foreach ($arr as $list){
            if($value<=$list['min']){
                return $list['value'];
            }
        }
        return 1;
    }

    //獲取系數落差 13
    public function getLadderToMax($cofOld,$cofNow){
        $cofOld = "".$cofOld;
        $cofNow = "".$cofNow;
        $arr = array(
            "0"=>0,
            "0.05"=>1,
            "0.125"=>2,
            "0.2"=>3,
            "0.275"=>4,
            "0.35"=>5,
            "0.425"=>6,
            "0.5"=>7,
            "0.575"=>8,
            "0.65"=>9,
            "0.725"=>10,
            "0.8"=>11,
            "0.875"=>12,
            "0.95"=>13,
            "1"=>14,
        );
        $cofOld = key_exists($cofOld,$arr)?$arr[$cofOld]:0;
        $cofNow = key_exists($cofNow,$arr)?$arr[$cofNow]:0;
        return $cofNow-$cofOld;
    }

    //獲取系數落差(質檢專用) 13
    public function getLadderToMaxAndPrice($cofOld,$cofNow,$price){
        $price = is_numeric($price)?floatval($price):0;
        if($price<=2400000){
            return $this->getLadderToMin($cofOld,$cofNow);
        }else{
            return $this->getLadderToMax($cofOld,$cofNow);
        }
    }

    //獲取系數落差 情況1 （暫時失效）
    public function getLadderOne($cofOld,$cofNow){
        $cofOld = "".$cofOld;
        $cofNow = "".$cofNow;
        $arr = array(
            "0"=>0,
            "0.05"=>1,
            "0.125"=>2,
            "0.2"=>3,
            "0.275"=>4,
            "0.35"=>5,
            "0.425"=>6,
            "0.5"=>7,
            "0.575"=>8,
            "0.65"=>9,
            "0.725"=>10,
            "0.8"=>11,
            "0.875"=>12,
            "0.95"=>13,
            "1"=>14,
        );
        $cofOld = key_exists($cofOld,$arr)?$arr[$cofOld]:0;
        $cofNow = key_exists($cofNow,$arr)?$arr[$cofNow]:0;
        return $cofNow-$cofOld;
    }

    //獲取系數落差 12
    public function getLadderToMin($cofOld,$cofNow){
        $cofOld = "".$cofOld;
        $cofNow = "".$cofNow;
        $arr = array(
            "0"=>1,
            "0.125"=>2,
            "0.2"=>3,
            "0.275"=>4,
            "0.35"=>5,
            "0.425"=>6,
            "0.5"=>7,
            "0.575"=>8,
            "0.65"=>9,
            "0.725"=>10,
            "0.8"=>11,
            "0.875"=>12,
            "0.95"=>13,
            "1"=>14,
        );
        $cofOld = key_exists($cofOld,$arr)?$arr[$cofOld]:0;
        $cofNow = key_exists($cofNow,$arr)?$arr[$cofNow]:0;
        return $cofNow-$cofOld;
    }
}