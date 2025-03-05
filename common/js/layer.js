function subShowLayer(strId)
{
	var objLayer = document.getElementById (strId);

	objLayer.style.visibility = "visible";
	objLayer.style.display	  = "block";
}

function subHideLayer(strId)
{
	var objLayer = document.getElementById (strId);

	objLayer.style.visibility = "hidden";
	objLayer.style.display    = "none";
}