<?php
require_once("reportconstants.php");
require_once("reportobject.php");

//------------------
// ReportLine class
//------------------
class ReportLine extends ReportObject {
    // Public
    //--------
    var $Color;
    var $LineWidth;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportLine() {
        parent::ReportObject();
        $this->Color = "000000";
        $this->Width = 0;
        $this->Height = 0;
        $this->LineWidth = .5;
    } // end function ReportLine() --constructor--
    
    // generate output
    function Generate($Report) {
        $Report->output .=
            ps_colors($this->Color) . "setrgbcolor\n"
            . ($this->LineWidth) . " mm setlinewidth\n"
            . "newpath\n"
            . ($this->X + $Report->curx) . " mm px "
            . ($this->Y + $Report->cury) . " mm py "
            . "moveto\n"
            . $this->Width . " mm "
            . $this->Height . " neg mm "
            . "rlineto\n"
            . "closepath\n"
            . "stroke\n\n";
    } // end function Generate
    
} // end class ReportLine
?>