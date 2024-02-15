<?php
require_once("reportconstants.php");
require_once("reportobject.php");

//---------------------
// ReportGraphic class
//---------------------
class ReportGraphic extends ReportObject {
    // Public
    //--------
    var $Color;
    var $PSFunction;

    // Private
    //---------

    // Methods
    //---------
    //constructor
    function ReportGraphic() {
        parent::ReportObject();
        $this->Color = "000000";
        $this->PSFunction = "";
    } // end function ReportGraphic() --constructor--
    
    // generate output
    function Generate(&$Report) {
        $Report->output .=
            ps_colors($this->Color) . "setrgbcolor\n"
            . ($this->X + $Report->curx) . " mm px "
            . ($this->Y + $Report->cury) . " mm py "
            . ($this->Width) . " mm "
            . ($this->Height) . " mm "
            . $this->PSFunction . "\n\n";
    } // end function Generate
    
} // end class ReportGraphic
?>