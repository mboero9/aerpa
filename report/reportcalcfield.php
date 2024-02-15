<?php
require_once("reportconstants.php");
require_once("reportfield.php");

//-----------------------
// ReportCalcField class
//-----------------------
class ReportCalcField extends ReportField {
    // Public
    //--------
    var $Operation;

    // Private
    //---------
    var $acum;
    var $count;
    var $min;
    var $max;

    // Methods
    //---------
    //constructor
    function ReportCalcField() {
        parent::ReportField();
        $this->Operation = RPT_COUNT;
        $this->Clean();
    } // end function ReportCalcField() --constructor--
    
    // Update values
    function Update(&$Report) {
		if( isset( $this->Omit_if_field_is_null ) 
			&& $this->Omit_if_field_is_null > 0 
			&& is_null($Report->row[$this->FieldName])
			) {
			return;
		}
        $val = $Report->row[$this->FieldName];
        $this->acum += $val;
        $this->count++;
        $this->min = (is_null($this->min) ? $val :
        	($val < $this->min ? $val : $this->min));
        $this->max = (is_null($this->max) ? $val :
        	($val > $this->max ? $val : $this->max));
    }
    
    // Clean values
    function Clean() {
        $this->acum = $this->count = 0;
        $this->min = $this->max = NULL;
    }
    
    // Generate output
    function Generate(&$Report) {
    	$txt = ($this->Operation == RPT_COUNT ? $this->count :
    			($this->Operation == RPT_SUM ? $this->acum :
    			 ($this->Operation == RPT_MAX ? $this->max :
    			  ($this->Operation == RPT_MIN ? $this->min :
    			   ($this->count == 0 ? 0 : $this->acum / $this->count)))));
    	$txt = ($this->Format == "" ? $txt : sprintf($this->Format, $txt));
    	parent::GenerateText($Report, $txt);
    } // end function Generate
    
} // end class ReportCalcField
?>