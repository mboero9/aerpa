// JavaScript Document
function comp_cpa( cpa1 , cpa2 ) {
	if( cpa1.length != cpa2.length ) {
		cpa1 = cpa1.replace(/\D/g,"");
		cpa2 = cpa2.replace(/\D/g,"");
	}
	if( cpa1 == cpa2 ) return true;
	return false;
}