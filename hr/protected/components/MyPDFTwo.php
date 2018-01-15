<?php

class MyPDFTwo {
	protected $_PDF;

	public function __construct() {
        $phpExcelPath = Yii::getPathOfAlias('ext.TCPDF2');
        spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'tcpdf.php');
		$this->_PDF = new tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 是否显示页眉
        $this->_PDF->setPrintHeader(false);
        // 是否显示页脚
        $this->_PDF->setPrintFooter(false);
        // 设置是否自动分页  距离底部多少距离时分页
        $this->_PDF->SetAutoPageBreak(TRUE, '5');
        // 设置默认等宽字体
        $this->_PDF->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // 设置行高
        $this->_PDF->setCellHeightRatio(1);
        // 设置左、上、右的间距
        $this->_PDF->SetMargins('10', '10', '10');
        // 设置图像比例因子
        $this->_PDF->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->_PDF->setFontSubsetting(true);
	}

	//210mm×297mm
	//190mm×287mm
	public function setPageToWork($arr=array()){
        $this->_PDF->AddPage();
/*        $html = "<table border='1' width='100%'><thead><tr><th>1</th><th>2</th></tr></thead><tbody><tr><td>22</td><td>333</td></tr></tbody></table>";
        $this->_PDF->SetFont('stsongstdlight', '', 14, '', true);
        $this->_PDF->writeHTMLCell(63, 5, 10,55, $html, 0, 1, 0, true, '', true);*/
        //加班申请表
        $this->_PDF->SetFont('stsongstdlight', '', 18, '', true);
        $html = "加班申请表";
        $this->_PDF->writeHTMLCell(190, 10, 10,10, $html, 0, 1, 0, true, 'C', true);
        //单位名称
        $this->_PDF->SetFont('stsongstdlight', '', 14, '', true);
        $html = "公司名称：".$arr["company_name"];
        $this->_PDF->writeHTMLCell(95, 8, 10,20, $html, 0, 1, 0, true, 'L', true);
        //所属部门
        $html = "所属部门：".$arr["dept_name"];
        $this->_PDF->writeHTMLCell(95, 6, 105,20, $html, 0, 1, 0, true, 'L', true);


        //繪製表格
        $this->_PDF->MultiCell(190,13,"",1);
        $this->_PDF->MultiCell(190,50,"",1);
        $this->_PDF->MultiCell(190,22,"",1);
        $this->_PDF->MultiCell(190,22,"",1);
        $this->_PDF->MultiCell(190,22,"",1);
        $this->_PDF->MultiCell(190,33,"",1);
        $this->_PDF->MultiCell(190,22,"",1);
        //竖线
        $this->_PDF->Line(50,26,50,210);
        $this->_PDF->Line(78,26,78,39);
        $this->_PDF->Line(106,26,106,39);
        $this->_PDF->Line(134,26,134,39);
        $this->_PDF->Line(162,26,162,39);
        //文字填充
        $this->_PDF->SetFont('stsongstdlight', '', 14, '', true);
        //员工编号
        $html = "员工编号";
        $this->_PDF->writeHTMLCell(40, 13, 10,31, $html, 0, 1, 0, true, 'C', true);
        $html = $arr["employee_code"];
        $this->_PDF->writeHTMLCell(28, 13, 50,31, $html, 0, 1, 0, true, 'C', true);
        //姓 名
        $html = "姓 名";
        $this->_PDF->writeHTMLCell(28, 13, 78,31, $html, 0, 1, 0, true, 'C', true);
        $html = $arr["employee_name"];
        $this->_PDF->writeHTMLCell(28, 13, 106,31, $html, 0, 1, 0, true, 'C', true);
        //填表日期
        $html = "填表日期";
        $this->_PDF->writeHTMLCell(28, 13, 134,31, $html, 0, 1, 0, true, 'C', true);
        $html = date("Y/m/d",strtotime($arr["lcd"]));
        $this->_PDF->writeHTMLCell(38, 13, 162,31, $html, 0, 1, 0, true, 'C', true);
        //拟定加班时段及时间
        $html = "<p>拟定加班</p><br><p>时段及时间</p>";
        $this->_PDF->writeHTMLCell(40, 20, 10,55, $html, 0, 1, false, true, 'C', true);
        $html = "<p>□工作日：（年/月/日/时/分～年/月/日/时/分）</p>";
        $this->_PDF->writeHTMLCell(150, 8, 51,41, $html, 0, 1, false, true, 'L', true);
        $html = "<p>□周末休息日：（年/月/日/时/分～年/月/日/时/分）</p>";
        $this->_PDF->writeHTMLCell(150, 8, 51,57, $html, 0, 1, false, true, 'L', true);
        $html = "<p>□法定休假日：（年/月/日/时/分～年/月/日/时/分）</p>";
        $this->_PDF->writeHTMLCell(150, 8, 51,73, $html, 0, 1, false, true, 'L', true);
        //加班時間隨動
        switch ($arr["work_type"]){
            case 1:
                $timeHeight = 16;
                break;
            case 2:
                $timeHeight = 32;
                break;
            default:
                $timeHeight = 0;//0:工作日  16：週末休息日   32：法定休息日
        }
        $this->_PDF->SetFont('stsongstdlight', '', 18, '', true);
        $html = "√";
        $this->_PDF->writeHTMLCell(20, 20, 50,40+$timeHeight, $html, 0, 1, false, true, 'L', true);
        $this->_PDF->SetFont('stsongstdlight', '', 14, '', true);
        $html = "<p>".date("Y年m月d日 H时i分",strtotime($arr["start_time"]))."&nbsp;&nbsp;～&nbsp;&nbsp;".date("Y年m月d日 H时i分",strtotime($arr["end_time"]))."</p>";
        $this->_PDF->writeHTMLCell(140, 8, 60,49+$timeHeight, $html, 0, 1, false, true, 'L', true);
        //加班事由
        $html = "加班事由";
        $this->_PDF->writeHTMLCell(40, 10, 10,98, $html, 0, 1, false, true, 'C', true);
        $html = $arr["work_cause"];
        $this->_PDF->writeHTMLCell(148, 15, 51,93, $html, 0, 1, false, true, 'L', true);

        //加班地点及工作内容
        $html = "<p>加班地点</p><br><p>及工作内容</p>";
        $this->_PDF->writeHTMLCell(40, 10, 10,115, $html, 0, 1, false, true, 'C', true);
        $html = $arr["work_address"];
        $this->_PDF->writeHTMLCell(148, 15, 51,115, $html, 0, 1, false, true, 'L', true);

        //部门主管意见
        $html = "<p>部门主管</p><br><p>意见</p>";
        $this->_PDF->writeHTMLCell(40, 10, 10,137, $html, 0, 1, false, true, 'C', true);
        if(!empty($arr["user_lcd"])){
            $html = date("Y年m月d日",strtotime($arr["user_lcd"]));
            $this->_PDF->writeHTMLCell(148, 8, 51,150, $html, 0, 1, false, true, 'R', true);
        }

        //部门主管意见
        $html = "<p>地区总经理/</p><br><p>地区主管</p><br><p>审批</p>";
        $this->_PDF->writeHTMLCell(40, 10, 10,160, $html, 0, 1, false, true, 'C', true);
        if(!empty($arr["area_lcd"])){
            $html = date("Y年m月d日",strtotime($arr["area_lcd"]));
            $this->_PDF->writeHTMLCell(148, 8, 51,183, $html, 0, 1, false, true, 'R', true);
        }

        //部门主管意见
        $html = "<p>法定代表人</p><br><p>审核</p>";
        $this->_PDF->writeHTMLCell(40, 10, 10,192, $html, 0, 1, false, true, 'C', true);
        if(!empty($arr["head_lcd"])){
            $html = date("Y年m月d日",strtotime($arr["head_lcd"]));
            $this->_PDF->writeHTMLCell(148, 8, 51,205, $html, 0, 1, false, true, 'R', true);
        }


        //底部文字
        $this->_PDF->SetFont('stsongstdlight', '', 12, '', true);
        $html = "备注：1. 拟定于工作日加班的，每日不得超过3个小时且每月不得超过36个小时，拟定于周末休息日加班的，由部门主管或部门经理尽量安排补休；2. 请员工在加班日之前，提早至少一个工作日填写此申请表，并递交至所属部门主管、部门经理、地区总经理/地区主管及公司法定代表人签字后方可生效；3. 上表填写完毕后，影印件转交至公司人事部备案，原件交由公司法定代表人安排存档。";
        $this->_PDF->writeHTMLCell(190, 22, 10,213, $html, 0, 1, 0, true, 'L', true);
        //底部表格
        $this->_PDF->MultiCell(190,13,"",1);
        $this->_PDF->MultiCell(190,16,"",1);
        $this->_PDF->MultiCell(190,16,"",1);
        // 34 29
        $this->_PDF->Line(39,248,39,280);
        $this->_PDF->Line(73,248,73,264);
        $this->_PDF->Line(102,248,102,264);
        $this->_PDF->Line(136,248,136,264);
        $this->_PDF->Line(165,248,165,264);
        $this->_PDF->SetFont('stsongstdlight', '', 14, '', true);
        $html = "<p>由大中华发展及支援中心填写以下内容：</p>";
        $this->_PDF->writeHTMLCell(190, 10, 12,240, $html, 0, 1, false, true, 'L', true);
        $html = "<p>所属地区</p>";
        $this->_PDF->writeHTMLCell(29, 10, 10,254, $html, 0, 1, false, true, 'C', true);
/*        $html = "<p>所属地区</p>";
        $this->_PDF->writeHTMLCell(29, 10, 39,254, $html, 0, 1, false, true, 'C', true);*/
        $html = "<p>员工编号</p>";
        $this->_PDF->writeHTMLCell(29, 10, 73,254, $html, 0, 1, false, true, 'C', true);
/*        $html = "<p>员工编号</p>";
        $this->_PDF->writeHTMLCell(29, 10, 102,254, $html, 0, 1, false, true, 'C', true);*/
        $html = "<p>文件编号</p>";
        $this->_PDF->writeHTMLCell(29, 10, 136,254, $html, 0, 1, false, true, 'C', true);
/*        $html = "<p>文件编号</p>";
        $this->_PDF->writeHTMLCell(29, 10, 165,254, $html, 0, 1, false, true, 'C', true);*/
        $html = "<p>备注</p>";
        $this->_PDF->writeHTMLCell(29, 10, 10,270, $html, 0, 1, false, true, 'C', true);
/*        $html = "<p>备注</p>";
        $this->_PDF->writeHTMLCell(150, 10, 40,270, $html, 0, 1, false, true, 'L', true);*/
    }

	public function getOutput($str="docx") {
        $this->_PDF->Output($str.".pdf", 'D');
	}
}
?>