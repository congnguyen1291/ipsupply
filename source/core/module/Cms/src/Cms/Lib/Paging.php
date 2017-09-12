<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/8/14
 * Time: 4:07 PM
 */
namespace Cms\Lib;
class Paging{
    /** @var int The record number to start dislpaying from */
    var  $m_intLimitStart= null ;
    /** @var int Number of rows to display per page */
    var $m_intLimit 		= null;
    /** @var int Total number of rows */
    var $m_intTotal 		= null;
    /** @var arr Limit record number to display */
    var $urlsearch			=null;
    var $m_arrLimit 		= array(10=>10,15=>15, 20 => 20, 30 => 30,40=>40,50 => 50);
    /**
     * Ham khoi tao cho class
     * Type:      function<br>
     * Name:     PageNav <br>
     * @param: 	int p_intTotal la tong so record
     * @param: 	int p_intPage la trang hien tai
     * @param: 	int p_intLimit la so record cho mot trang
     * @return:
     */
    public function __construct($p_intTotal, $p_intPage, $p_intLimit, $urlsearch)
    {
        $this->m_intTotal 			= (int) $p_intTotal;
        $this->m_intLimit 			= (int) max( $p_intLimit, 1 );

        $this->m_intLimitStart 	= (int) max( ($p_intPage-1)*($this->m_intLimit), 0 );

        if ($this->m_intLimit > $this->m_intTotal)
        {
            $this->m_intLimitStart = 0;
        }
        elseif ($this->m_intLimitStart >= $this->m_intTotal)
        {
            $this->m_intLimitStart = $this-> m_intTotal - ($this-> m_intTotal % $this->m_intLimit)-$this->m_intLimit;
        }

    }

    /**
     * Ham tao combobox gioi han record trong mot trang
     * Type:     function<br>
     * Name:     getLimitBox <br>
     * @param:
     * @return:  string HTLM
     */
    public function getLimitBox()
    {	$limit = array();
        foreach ($this->m_arrLimit as $k =>$v){
            $limit[]=$this->makeOption($k);
        }
        // build the html select list
        $strHtml = $this->selectList($limit,'display', 'size="1" onchange="document.adminForm.submit()"','value', 'text',$this->m_intLimit);
        return $strHtml;
    }
    /**
     * Writes the html limit # input box
     */
    public function writeLimitBox () {
        echo Paging::getLimitBox();
    }
    /**
     * Writes total page
     */
    public function writePagesCounter() {
        echo $this->getPagesCounter();
    }
    /**
     * @return string The html for the pages counter, eg, Results 1-10 of x
     */
    public function getPagesCounter() {
        $strHtml = '';
        $intFromResult = $this -> m_intLimitStart + 1;
        if ($this -> m_intLimitStart + $this -> m_intLimit < $this -> m_intTotal)
        {
            $intToResult = $this -> m_intLimitStart + $this -> m_intLimit;
        }
        else
        {
            $intToResult = $this -> m_intTotal;
        }
        if ($this -> m_intTotal > 0) {
            $strHtml .= "Results ". $intFromResult . " - " . $intToResult . " of ". $this -> m_intTotal;
        }
        else
        {
            $strHtml .= "NoResults";
        }
        return $strHtml;
    }
    /**
     * Writes the html for the pages counter, eg, Results 1-10 of x
     */
    public function writePagesLinks()
    {
        echo $this -> getPagesLinks();
    }
    /**
     * @return string The html links for pages, eg, previous, next, 1 2 3 ... x
     */
    public function getPageFooter()
    {

    }
    public function getPagesLinks($searchtitle="")
    {
        $strHtml 			= '';
        $intDispPages 		= 5;
        // tong so trang co duoc
        $intTotalPages 		= ceil( $this -> m_intTotal / $this -> m_intLimit );
        // trang hien tai dang xem
        $intPageCurrent 	= ceil(($this -> m_intLimitStart + 1) / $this -> m_intLimit );
        // trang duoc hien thi dau tien
        $intStartLoop 		= 1;
        if ( $intPageCurrent > ceil($intDispPages/2)){
            $intStartLoop = $intPageCurrent - ceil($intDispPages/2)+1 ;
        }// trang duoc hien thi cuoi cung
        $intStopLoop = $intStartLoop + $intDispPages - 1;
        $strHtml.="<input type='hidden' name='page' id='page' value='$intPageCurrent' />";
        if ($intStopLoop >= $intTotalPages)
        {
            if($intTotalPages - $intStartLoop < $intDispPages )
                $intStartLoop = max(($intTotalPages - $intDispPages +1),1) ;
            $intStopLoop = $intTotalPages;
        }
        if ($intPageCurrent > 1)
        {
            $pre=$intPageCurrent-1;
            $strHtml .= "<li class=\"last\"><a href=\"?page=1".$searchtitle."\" title=\"First page\"><<</a></li>";
            $strHtml .= "<li><a href=\"?page=$pre".$searchtitle."\" title=\"Pre page\"><</a></li>";
        }else{
            $strHtml .= "<li class='disabled'><a href=\"#\"><<</a></li>";
            $strHtml .= "<li class='disabled'><a href=\"#\"><</a></li>";
        }
        for ($i=$intStartLoop; $i <= $intStopLoop; $i++)
        {
            if ($i == $intPageCurrent)
            {
                $strHtml .= "<li class=\"active\"><a href=\"#\">{$i}</a> </li>";
            }else{
                $strHtml .= "<li><a href=\"?page=$i".$searchtitle."\">$i</a></li>";
            }
        }
        if ($intPageCurrent < $intTotalPages)
        {
            $intRowEnd 		= ($intTotalPages);
            $pre=$intPageCurrent+1;
            $strHtml .= "<li><a href=\"?page=$pre".$searchtitle."\" title=\"Next page\">></a></li>";
            $strHtml .= "<li class=\"next\"><a href=\"?page=$intRowEnd".$searchtitle."\" title=\"Last page\">>></a></li>";
        }else{
            $strHtml .= "<li class=\"disabled\"><a href=\"#\">></a></li>";
            $strHtml .= "<li class=\"disabled\"><a href=\"#\">>></a></li>";
        }

        return $strHtml;
    }
    public function getListFooter($searchtitle) {
        $strHtml ='';
        $intTotalPages 		= ceil( $this -> m_intTotal / $this -> m_intLimit );
        if($intTotalPages > 1)
        {
            $strHtml= $this->getPagesLinks($searchtitle);
        }
        return $strHtml;
    }
    //////////// Show pages2
    public function getPagesLinksContent()
    {
        $strHtml 			= '';
        $intDispPages 		= 5;
        // tong so trang co duoc
        $intTotalPages 		= ceil( $this -> m_intTotal / $this -> m_intLimit );
        // trang hien tai dang xem
        $intPageCurrent 	= ceil(($this -> m_intLimitStart + 1) / $this -> m_intLimit );
        // trang duoc hien thi dau tien
        $intStartLoop 		= 1;
        if ( $intPageCurrent > ceil($intDispPages/2)){
            $intStartLoop = $intPageCurrent - ceil($intDispPages/2)+1 ;
        }// trang duoc hien thi cuoi cung
        $intStopLoop = $intStartLoop + $intDispPages - 1;
        $strHtml.="<input type='hidden' name='page' id='page' value='$intPageCurrent' />";
        if ($intStopLoop >= $intTotalPages)
        {
            if($intTotalPages - $intStartLoop < $intDispPages )
                $intStartLoop = max(($intTotalPages - $intDispPages +1),1) ;
            $intStopLoop = $intTotalPages;
        }
        if ($intPageCurrent > 1)
        {
            $pre=$intPageCurrent-1;
            $strHtml .= "<a href=\"?page=1".$urlsearch."\" title=\"First page\">&lt;&lt;&nbsp;</a>";
            $strHtml .= "<a href=\"?page=$pre".$urlsearch."\" title=\"Pre page\">&lt;&nbsp;</a>";
        }
        else
        {
            $strHtml .= "<a>Trang &#272;&#7847;u&lt;&lt;&nbsp;</a>";
            $strHtml .= "<a>&lt;&nbsp;</a>";
        }
        for ($i=$intStartLoop; $i <= $intStopLoop; $i++)
        {
            if ($i == $intPageCurrent)
            {
                $strHtml .= "<a class=\"selected\"> $i </a>";
            }
            else
            {
                $strHtml .= "<a href=\"?page=$i".$urlsearch."\"><strong>$i</strong></a>";
            }
        }
        if ($intPageCurrent < $intTotalPages)
        {
            $intRowEnd 		= ($intTotalPages);
            $pre=$intPageCurrent+1;
            $strHtml .= "<a href=\"?page=$pre".$urlsearch."\" title=\"Next page theo\">&nbsp;&gt;</a>";
            $strHtml .= "<a href=\"?page=$intRowEnd".$urlsearch."\" title=\"Last page\"> &nbsp;&gt;&gt;</a>";
        }

        return $strHtml;
    }
    public function getListFooterContent() {

        $strHtml ='';
        $intTotalPages 		= ceil( $this -> m_intTotal / $this -> m_intLimit );
        if($intTotalPages > 1)
        {
            $strHtml .= "<div width='95%' align='right' style='padding-right:10px;'>".$this->getPagesLinksContent()."</div>";
        }
        return $strHtml;
    }
    /**
     * @param int The row index
     * @return int
     */
    public function rowNumber( $i ) {

        return $i + 1 + $this->m_intLimitStart;
    }
    public function setLimitRecord($p_arrLimit)
    {
        $this -> m_arrLimit = $p_arrLimit;
    }
    public function selectList( &$arr, $tag_name, $tag_attribs, $key, $text, $selected=NULL ) {
        reset( $arr );
        $html = "<select name=\"$tag_name\" $tag_attribs>";
        for ($i=0, $n=count( $arr ); $i < $n; $i++ ) {
            $k = $arr[$i]->$key;
            $t = $arr[$i]->$text;
            $id = @$arr[$i]->id;

            $extra = '';
            $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
            if (is_array( $selected )) {
                foreach ($selected as $obj) {
                    $k2 = $obj->$key;
                    if ($k == $k2) {
                        $extra .= " selected=\"selected\"";
                        break;
                    }
                }
            } else {
                $extra .= ($k == $selected ? " selected=\"selected\"" : '');
            }
            $html .= "\t<option value=\"".$k."\"$extra>" . $t . "</option>";
        }
        $html .= "</select>";
        return $html;
    }

    public function makeOption( $value, $text='' ) {
        $obj = new stdClass;
        $obj->value = $value;
        $obj->text = trim( $text ) ? $text : $value;
        return $obj;
    }
}
