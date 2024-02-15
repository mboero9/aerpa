<?php
require_once("reportconstants.php");

//---------------------
// ReportGroup class
//---------------------
class ReportGroup {
    // Public
    //--------
    var $GroupHeader;
    var $GroupFooter;
    var $GroupPageFooter;	
    var $Field;
    var $SubGroup;
    var $Forced;
    var $Closing;

    // Private
    //---------
    var $first;
    var $lastvalue;
    var $changed;

    // Methods
    //---------
    //constructor
    function ReportGroup() {
    	$this->Field = $this->SubGroup = $this->lastvalue = NULL;
        $this->GroupHeader =& new ReportSection();
        $this->GroupFooter =& new ReportSection();
        $this->GroupPageFooter =& new ReportSection();		
        $this->Forced = false;
        $this->first = true;
        $this->changed = false;
    } // end function ReportGroup() --constructor--

    //Clean up
	function CleanGroup(&$Report) {
    	if (!is_null($this->SubGroup)) {
			$this->SubGroup->CleanGroup($Report);
		}
		$this->lastvalue = NULL;
		for ($i = 0; $i < count($this->GroupFooter->objects); $i++) {
			$obj =& $this->GroupFooter->objects[$i];
			if (is_a($obj, "ReportCalcField")) {
				$obj->Clean();
			} // end if calcfield
		} // end for obj
		for ($i = 0; $i < count($this->GroupPageFooter->objects); $i++) {
			$obj =& $this->GroupPageFooter->objects[$i];
			if (is_a($obj, "ReportCalcField")) {
				$obj->Clean();
			} // end if calcfield
		} // end for obj		
	}

    //Check for changes
    function CheckGroup(&$Report) {
    	$closing = $this->Closing;
        $this->CheckGroupEnd($Report);
        if (!$closing) {
	        $this->CheckGroupStart($Report);
	    }
    }

    //Check for changes
    function CheckGroupEnd(&$Report) {
    	//Check for group change
    	if (!is_null($this->Field)) {
    		if ($this->CalcGroupHash($Report) != $this->lastvalue) {
	    		$this->changed = true;
	    	}
    	}
    	//If there's a subgroup, check it; force change if parent changes
    	if (!is_null($this->SubGroup)) {
    		$this->SubGroup->Closing = $this->Closing;
    		if ($this->changed) {
	    		$this->SubGroup->changed = true;
	    	} // end if changed
    		$this->SubGroup->CheckGroupEnd($Report);
    	} // end if isnull subgroup
    	if ($this->Closing || $this->changed) {
			//If closing, show footer
    		if ($this->Closing) {
	    		$Report->checkPage($this->GroupFooter->Height);
	    		$this->GroupFooter->Generate($Report);
	    		$Report->checkPage($this->GroupPageFooter->Height);
	    		$this->GroupPageFooter->Generate($Report);				
    		} else {
    			//Normal group change
		    	if ($this->first) {
		    		$this->first = false;
		    	} else {
		    		$Report->checkPage($this->GroupFooter->Height);
		    		$this->GroupFooter->Generate($Report);
		    		$Report->checkPage($this->GroupPageFooter->Height);
		    		$this->GroupPageFooter->Generate($Report);
		    	} // end if first
	    	} // end if Closing
	    	//Clean calculated fields
	    	for ($i = 0; $i < count($this->GroupFooter->objects); $i++) {
	    		$obj =& $this->GroupFooter->objects[$i];
	    		if (is_a($obj, "ReportCalcField")) {
	    			$obj->Clean();
	    		} // end if calcfield
	    	} // end for obj
	    	for ($i = 0; $i < count($this->GroupPageFooter->objects); $i++) {
	    		$obj =& $this->GroupPageFooter->objects[$i];
	    		if (is_a($obj, "ReportCalcField")) {
	    			$obj->Clean();
	    		} // end if calcfield
	    	} // end for obj
    	} // end if closing || changed
    	//Calc fields for current record
    	$this->Closing = $this->changed = false;
    	for ($i = 0; $i < count($this->GroupFooter->objects); $i++) {
    		$obj =& $this->GroupFooter->objects[$i];
    		if (is_a($obj, "ReportCalcField")) {
    			$obj->Update($Report);
    		} // end if calcfield
    	} // end for obj
    	for ($i = 0; $i < count($this->GroupPageFooter->objects); $i++) {
    		$obj =& $this->GroupPageFooter->objects[$i];
    		if (is_a($obj, "ReportCalcField")) {
    			$obj->Update($Report);
    		} // end if calcfield
    	} // end for obj
    } // end function CheckGroupEnd

    //Check for changes
    function CheckGroupStart(&$Report) {
    	if (!is_null($this->Field)) {
    		if ($changed = ($this->CalcGroupHash($Report) != $this->lastvalue)) {
    			$this->lastvalue = $this->CalcGroupHash($Report);
    		}
    	} else {
    		$changed = false;
    	}
    	if ($this->Forced || $changed) {
	    	$Report->checkPage($this->GroupHeader->Height);
	    	$this->GroupHeader->Generate($Report);
    	} // end if forced || changed
    	if (!is_null($this->SubGroup)) {
    		$this->SubGroup->Forced = $this->Forced || $changed;
    		$this->SubGroup->CheckGroupStart($Report);
    	} // end if isnull subgroup
    	$this->Forced = false;
    } // end function CheckGroupStart

    function CalcGroupHash(&$Report) {
    	$flds = explode("+", $this->Field);
    	$hash = "";
    	foreach($flds as $fld) {
    		$hash .= $Report->row[$fld] . "|";
    	} // end foreach
    	return $hash;
    } // end function CalcGroupHash
} // end class ReportGroup
?>