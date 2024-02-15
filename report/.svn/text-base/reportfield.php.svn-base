<?php
require_once("reportconstants.php");
require_once("reporttext.php");

//-------------------
// ReportField class
//-------------------
class ReportField extends ReportText {
    // Public
    //--------
    var $FieldName;
    var $Format;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportField() {
        parent::ReportText();
        $this->FieldName = "";
        $this->Format = "";
    } // end function ReportField() --constructor--
    
    // Generate output
    function Generate(&$Report) {
        $this->GenerateText($Report, ($this->Format == "" ? $Report->row[$this->FieldName]
        	: sprintf($this->Format, $Report->row[$this->FieldName])));
    } // end function Generate
    
    // Generate text
    function GenerateText(&$Report, $Text) {
        parent::GenerateText($Report, $Text);
    } // end function GenerateText
    
} // end class ReportField
?>