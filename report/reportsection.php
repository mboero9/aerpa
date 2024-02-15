<?php
require_once("reportconstants.php");

//---------------------
// ReportSection class
//---------------------
class ReportSection {
    // Public
    //--------
    var $Name;
    var $Height;
    
    // Private
    //---------
    var $objects;
    
    // Methods
    //---------
    //constructor
    function ReportSection() {
        $this->Name = "";
        $this->Height = 10;
        $this->objects = array();
    } // end function ReportSection() --constructor--

    // add an object to the section
    function Add(&$obj) {
        $this->objects[] =& $obj;
    } // end function Add
    
    // generate output for the section
    function Generate(&$Report) {
		if (isset($this->abajo)&&$this->abajo) {
			$Report->cury = $Report->mb - $Report->PageFooter->Height - $this->Height;	
		}
        //save coordinates
        $x = $Report->curx;
        $y = $Report->cury;
        foreach($this->objects as $obj) {
            //restore coordinates for next object
            $Report->curx = $x;
            $Report->cury = $y;
            //append object output to report output
            $obj->Generate($Report);
        } //end foreach object
        $Report->curx = $Report->ml;
        $Report->cury = $y + $this->Height;
        //leave correct coordinates in report
    } // end function Generate
    
} // end class ReportSection
?>