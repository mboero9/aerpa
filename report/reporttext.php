<?php
require_once("reportconstants.php");
require_once("reportobject.php");

//------------------
// ReportText class
//------------------
class ReportText extends ReportObject {
    // Public
    //--------
    var $FontName;
    var $FontSize;
    var $FontStyle;
    var $Align;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportText() {
        parent::ReportObject();
        $this->FontName = RPT_HELVETICA;
        $this->FontSize = 10;
        $this->FontStyle = RPT_NORMAL;
        $this->Align = RPT_ALIGN_LEFT;
    } // end function ReportText() --constructor--
    
    // Generate output (virtual)
    function Generate(&$Report) {
    } // end function Generate
    
    // Print text passed by parameter
    function GenerateText(&$Report, $Text) {
        $Report->output .=
            "/" . ps_font($this->FontName, $this->FontStyle)
            . " " . $this->FontSize . " selectfont\n"
            . ($this->X + $Report->curx) . " mm px "
            . ($this->Y + $Report->cury) . " mm py "
            . $this->Width . " mm "
            . $this->Height . " mm "
            /* . "4 copy neg rectstroke " */
            . "true " . $this->Align . " "
            . ps_text($Text) . " "
            . "boxedtext\n";
    } // end function GenerateText
    
} // end class ReportText
?>