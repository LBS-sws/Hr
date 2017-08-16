<?php
/**
 * PHPWord
 *
 * Copyright (c) 2011 PHPWord
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 010 PHPWord
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    Beta 0.6.3, 08.07.2011
 */


/**
 * PHPWord_DocumentProperties
 *
 * @category   PHPWord
 * @package    PHPWord
 * @copyright  Copyright (c) 2009 - 2011 PHPWord (http://www.codeplex.com/PHPWord)
 */
class Template {
    
    /**
     * ZipArchive
     * 
     * @var ZipArchive
     */
    private $_objZip;
    
    /**
     * Temporary Filename
     * 
     * @var string
     */
    private $_tempFileName;
    
    /**
     * Document XML
     * 
     * @var string
     */
    private $_documentXML="";

    private $_path;
    
    /**
     * Create a new Template Object
     * 
     * @param string $strFilename
     */
    public function __construct($arr,$strike_bool=true) {
        $path = Yii::app()->basePath."/../upload/staff/";
        if (!file_exists($path)){
            mkdir ($path);
        }
        $city = Yii::app()->user->city();
        $path = $path.$city."/";
        if (!file_exists($path)){
            mkdir ($path);
        }
        $this->_tempFileName = $path.time().".docx";
        $this->_path = $path;

        copy(Yii::app()->basePath."/../".$arr[0], $this->_tempFileName); // Copy the source File to the temp File

        $this->_objZip = new ZipArchive();
        $this->_objZip->open($this->_tempFileName);
        $this->_documentXML = $this->_objZip->getFromName('word/document.xml');
        $this->_documentXML = explode('<w:body>',$this->_documentXML,2)[0];
        $this->_documentXML.="<w:body>";
        foreach ($arr as $key => $value){
            $bool = false;
            $objZip = new ZipArchive();
            $xml = new DomDocument();
            $url = Yii::app()->basePath."/../".$value;

            $objZip->open($url);
            $documentXML = $objZip->getFromName('word/document.xml');
//（以上二删一）
            $xmlObj = $xml->loadXML($documentXML);
            $timedom = $xml->getElementsByTagName("body");
            $timedom = $timedom->item(0);
            if (strpos($documentXML,'（以上二删一）')!==false){
                $tdList = $timedom->getElementsByTagName("tc");
                for($i = 0;$i<$tdList->length;$i++){
                    $td = $tdList->item($i);
                    if(strpos($td->textContent,'无试用期。')!==false){
                        $pList = $td->getElementsByTagName("p");
                        for($j= 0;$j<$pList->length;$j++){
                            $pObj = $pList->item($j);
                            if(strpos($pObj->textContent,'无试用期。')!==false){
                                /*添加下滑線*/
                                if(!$strike_bool){
                                    $pObj = $pList->item($j-1);
                                }
                                foreach ($pObj->childNodes as $rList){
                                    if($rList->tagName == "w:r"){
                                        $newel=$xml->createElement('w:strike');
                                        $rList->firstChild->appendChild($newel);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($key != 0){
                $this->_documentXML.='<w:br w:type="page"></w:br>';
            }
            foreach ($timedom->childNodes as $dom){
                if($dom->tagName!="w:sectPr"){
                    $this->_documentXML.=$dom->ownerDocument->saveXML($dom);
                }
            }
            $objZip->close();
        }
        $this->_documentXML.="</w:body></w:document>";
        //rsidRDefault
    }


    /**
     * Set a Template value
     * 
     * @param mixed $search
     * @param mixed $replace
     */
    public function setValue($search, $replace) {
        $search = '${'.$search.'}';

        $this->_documentXML = str_replace($search, $replace, $this->_documentXML);
    }

    /**
     * Set a Template value
     *
     * @param mixed $search
     * @param mixed $replace
     */
    public function getXMLString() {
        return $this->_documentXML;
    }
    
    /**
     * Save Template
     * 
     * @param string $strFilename
     */
    public function save($strFilename) {
        $strFilename = $this->_path.$strFilename.".docx";
        if(file_exists($strFilename)) {
            unlink($strFilename);
        }

        $this->_objZip->addFromString('word/document.xml', $this->_documentXML);
        
        // Close zip file
        if($this->_objZip->close() === false) {
            throw new Exception('Could not close zip file.');
        }
        
        rename($this->_tempFileName, $strFilename);
    }
}
?>