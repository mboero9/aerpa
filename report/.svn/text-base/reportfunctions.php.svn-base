<?php
require_once("reportconstants.php");

//
// These functions help form a DSC document
// which can be spooled via CUPS
//

//
// Document header, receives pagesize as parameter
//
function ps_doc_header($pagesize) {
    // fijar ancho y alto de pagina
    switch ($pagesize) {
        case RPT_A4:
            $width = (int) (210 / 25.4 * 72);
            $height = (int) (297 / 25.4 * 72);
            break;
        case RPT_LETTER:
            $width = (int) (8.5 * 72);
            $height = (int) (11 * 72);
            break;
        case RPT_LEGAL:
            $width = (int) (8.5 * 72);
            $height = (int) (14 * 72);
            break;
    } // end switch pagesize
    
    $header = str_replace("###site-functions###",
                file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/../report/site-functions.ps"),
                  str_replace("###width###", (string) $width,
                    str_replace("###height###", (string) $height,
                      file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/../report/doc-header.ps"))));
  
  return($header);
} // end function ps_doc_header


//
// Page header, receives page number as parameter
//
function ps_page_header($pagenum) {
    $header = str_replace("###page###", (string) $pagenum,
              file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/../report/page-header.ps"));
  
    return($header);
} // end function ps_page_header

function ps_codebar($num) {
    $header =  str_replace("###code###", (string) $num,
               file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/../report/barcode.ps"));
  
    return($header);
} // end function ps_page_header

//
// Page trailer, placeholder for future functionality
//
function ps_page_trailer() {
    return("showpage\n\n");
} // end function ps_page_header


//
// Document trailer
//
function ps_doc_trailer() {
    return(file_get_contents(dirname($_SERVER["SCRIPT_FILENAME"]) . "/../report/doc-trailer.ps"));
} // end function ps_doc_trailer

//
// Formats a string to allow inclusion in PS document
// (replaces parentheses with escaped parentheses)
//
function ps_text($text) {
    return("(" . str_replace("(","\\(",str_replace(")","\\)",$text)) . ")");
} // end function ps_text

//
// Returns postscript RGB colors from an hexadecimal rgb color string
//
function ps_colors($color) {
    return(sprintf("%f ", hexdec(substr($color, 0, 2)) / 255) .
         sprintf("%f ", hexdec(substr($color, 2, 2)) / 255) .
         sprintf("%f ", hexdec(substr($color, 4, 2)) / 255));
} // end function ps_colors

// Return font name, such as HelveticaLatin-BoldOblique
function ps_font($fname, $fstyle) {
    $f = ($fname == RPT_HELVETICA ? "Helvetica" :
            ($fname == RPT_TIMES ? "Times" : "Courier")) . "Latin";
    if ($fstyle != RPT_NORMAL) {
        $f .= "-" . (($fstyle & RPT_BOLD) == RPT_BOLD ? "Bold" : "")
            . (($fstyle & RPT_ITALIC) == RPT_ITALIC ?
                ($fname == RPT_TIMES ? "Italic" : "Oblique") : "");
    }   // end if style normal
	return($f);
} // end function PSFont
?>