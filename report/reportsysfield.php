<?php
require_once("reportconstants.php");
require_once("reporttext.php");

//----------------------
// ReportSysField class
//----------------------
class ReportSysField extends ReportText {
    // Public
    //--------
    var $Type;
    var $Format;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportSysField() {
        parent::ReportText();
        $this->Type = RPT_SYSPAGENO;
        $this->Format = "%u";
    } // end function ReportSysField() --constructor--
    
    // Generate output
    function Generate(&$Report) {
        switch ($this->Type) {
            case RPT_SYSPAGENO:
                $txt = sprintf($this->Format, $Report->page);
                break;
            case RPT_SYSDATE;
                $txt = date($this->Format);
                break;
        } // end switch type
        parent::GenerateText($Report, $txt);
    } // end function Generate
    
} // end class ReportSysField
?>