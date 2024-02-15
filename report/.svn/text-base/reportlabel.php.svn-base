<?php
require_once("reportconstants.php");
require_once("reporttext.php");

//-------------------
// ReportLabel class
//-------------------
class ReportLabel extends ReportText {
    // Public
    //--------
    var $Text;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportLabel() {
        parent::ReportText();
        $this->Text = "";
    } // end function ReportLabel() --constructor--
    
    // Generate output
    function Generate(&$Report) {
        parent::GenerateText($Report, $this->Text);
    } // end function Generate
    
} // end class ReportLabel
?>