<?php
require_once("reportconstants.php");

//-------------------
// ReportLabel class
//-------------------
class ReportCodeBar extends ReportObject {
    // Public
    //--------
    var $FieldName;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportCodeBar() {
        parent::ReportObject();
        $this->FieldName = "";
    } // end function ReportLabel() --constructor--
    
    // Generate output
    function Generate(&$Report) {
        $Report->output .=
            ps_codebar($Report->row[$this->FieldName]);	
    } // end function Generate
    
} // end class ReportLabel
?>