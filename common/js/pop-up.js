function subPopUp(strURL, blnScrollBars, intW, intH)
{
	var strFeatures;

	var strScrollBars = blnScrollBars? "yes" : "no";
	var intT          = (window.screen.height - intH) / 2;
	var intL          = (window.screen.width  - intW) / 2;

	strFeatures = "";
	strFeatures = strFeatures + "toolbar=no,";
	strFeatures = strFeatures + "location=no,";
	strFeatures = strFeatures + "directories=no,";
	strFeatures = strFeatures + "status=no,";
	strFeatures = strFeatures + "menubar=no,";
	strFeatures = strFeatures + "scrollbars=" + strScrollBars + ",";
	strFeatures = strFeatures + "resizable=no,";
	strFeatures = strFeatures + "width="  + intW + ",";
	strFeatures = strFeatures + "height=" + intH + ",";
	strFeatures = strFeatures + "top="    + intT + ",";
	strFeatures = strFeatures + "left="   + intL;

	window.open (strURL, "PopUp", strFeatures);
}