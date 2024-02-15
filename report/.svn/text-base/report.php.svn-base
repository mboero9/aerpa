<?php
require_once("../adodb/adodb.inc.php");

require_once("reportconstants.php");
require_once("reportfunctions.php");

require_once("reportsection.php");
require_once("reportgroup.php");
require_once("reportobject.php");
require_once("reportline.php");
require_once("reportgraphic.php");
require_once("reporttext.php");
require_once("reportlabel.php");
require_once("reportcodebar.php");
require_once("reportsysfield.php");
require_once("reportfield.php");
require_once("reportcalcfield.php");

//--------------
// Report class
//--------------
class Report {
    // Public
    //--------
    var $Name;
    var $PageSize;
    var $Orientation;
    var $MarginTop;
    var $MarginBottom;
    var $MarginLeft;
    var $MarginRight;
    var $ReportHeader;
    var $ReportFooter;
    var $PageHeader;
    var $PageFooter;
    var $Detail;
    var $MainGroup;
    var $Recordset;

    // Private
    //---------
    var $page;    //current page
    var $curx;    //current x cursor
    var $cury;    //current y cursor
    var $mt;      //current top margin
    var $mb;      //current bottom margin
    var $ml;      //current left margin
    var $mr;      //current right margin
    var $row;     //current row as array
    var $output;  //output PS string
    var $abajo;    //para forzar a poner abajo de la hoja
	
	var $depth;   //xml parsing helper
	var $obj;     //xml parsing helper

    // Methods
    //---------
    // Constructor
    function Report() {
        $this->Name = "";
        $this->PageSize = RPT_A4;
        $this->Orientation = RPT_PORTRAIT;
        $this->MarginTop = 20;
        $this->MarginBottom = 20;
        $this->MarginLeft = 10;
        $this->MarginRight = 10;
        $this->page = 1;
        $this->ReportHeader =& new ReportSection();
        $this->ReportFooter =& new ReportSection();
        $this->PageHeader =& new ReportSection();
        $this->PageFooter =& new ReportSection();
        $this->Detail =& new ReportSection();
        $this->MainGroup =& new ReportGroup();
        $this->depth = array();
        $this->obj = array();
    } // end function Report() --constructor--

    // Generates report, stores in $this->output
    function Generate() {
        //alias for class variables
        $out =& $this->output;
        $row =& $this->row;
        $rs =& $this->Recordset;
        $x =& $this->curx;
        $y =& $this->cury;
        $mt =& $this->mt;
        $mb =& $this->mb;
        $ml =& $this->ml;
        $mr =& $this->mr;

        //init private members
        $i = 0;
        $this->page = 1;
        $ml = $this->MarginLeft;
        $mt = $this->MarginTop;
        $mr = ($this->PageSize == RPT_A4 ? 210 : 215.9) - $this->MarginRight;
        $mb = ($this->PageSize == RPT_A4 ? 297 :
               ($this->PageSize == RPT_LEGAL ? 355.6 : 279.4))
              - $this->MarginBottom;

        //start output
        $out = ps_doc_header($this->PageSize);
        $out .= ps_page_header($this->page);
        $x = $ml;
        $y = $mt;

        //report header
        $out .= $this->PageHeader->Generate($this);
        $out .= $this->ReportHeader->Generate($this);

        //loop through records
        $this->MainGroup->Forced = true;
        while ($row = $rs->FetchRow()) {
            $this->MainGroup->CheckGroup($this);

            //generate detail for current row
            $this->checkPage($this->Detail->Height);
            $this->Detail->Generate($this);

            $i++;
        } //end while !eof
        $this->MainGroup->Closing = true;
        $this->MainGroup->CheckGroup($this);

        $x = $ml; $y = $mb - $this->PageFooter->Height;
        $this->PageFooter->Generate($this);
        $out .= ps_page_trailer();
        $out .= ps_doc_trailer();

    } // end function Generate()

    //page bottom check
    function checkPage($sectionHeight) {
        if ($this->cury + $sectionHeight
        		> $this->mb - $this->PageFooter->Height) {
            $this->curx = $this->ml;
            $this->cury = $this->mb - $this->PageFooter->Height;
            $this->output .= $this->PageFooter->Generate($this);
            $this->output .= ps_page_trailer();
            $this->page++;
            $this->output .= ps_page_header($this->page);
            $this->curx = $this->ml;
            $this->cury = $this->mt;
            $this->PageHeader->Generate($this);
        } //end page bottom check
    } // end function checkPage

    // Sends report to temporary file, returns filename
    function SendToFile() {
        $filename = tempnam("/tmp", "rptspl");
        $fp = fopen($filename, "w");
        fwrite($fp, $this->output);
        fclose($fp);
        return($filename);
    } // end function SendToFile()

	//XML parsing helper
	function _startElement($parser, $name, $attrs) {
	    $this->depth[$parser]++;
	    // crear objeto u obtener una referencia al existente
	    $d = $this->depth[$parser];
	    if (($name == "ReportHeader") || ($name == "ReportFooter")
	    	|| ($name == "PageHeader") || ($name == "PageFooter")
	    	|| ($name == "Detail") || ($name == "MainGroup")) {
	    	$o =& $this->$name;
    	} else if (($name == "GroupHeader") || ($name == "GroupFooter") || ($name == "GroupPageFooter")) {
    		$o =& $this->obj[$d - 1]->$name;
    	} else if ($name == "SubGroup") {
    		$o =& new ReportGroup();
    		$this->obj[$d - 1]->SubGroup =& $o;
	    } else if ($name != "Report") {
	    	$o =& new $name;
	    	$this->obj[$d - 1]->Add($o);
	    } else {
	    	$o =& $this;
	    }
	    $this->obj[$d] =& $o;
	    foreach($attrs as $attr => $val) {
	    	$o->$attr =	(defined($val) ? constant($val) : $val);
	    }
	}

	//XML parsing helper
	function _endElement($parser, $name) {
	    $this->depth[$parser]--;
	}

    //Read report structure from XML file
    function LoadFromXML($file) {
		$xml_parser = xml_parser_create("ISO-8859-1");
		xml_set_object($xml_parser, &$this);
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_element_handler($xml_parser, "_startElement", "_endElement");

		if (!($fp = fopen($file, "r"))) {
		    die("could not open XML input");
		}

		while ($data = fread($fp, 4096)) {
		    if (!xml_parse($xml_parser, $data, feof($fp))) {
		        die(sprintf("XML error: %s at line %d",
		                    xml_error_string(xml_get_error_code($xml_parser)),
		                    xml_get_current_line_number($xml_parser)));
		    }
		}

		xml_parser_free($xml_parser);
    }

} // end class Report
?>