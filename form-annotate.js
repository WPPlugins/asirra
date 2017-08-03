function HumanCheckComplete(isHuman)
{
	if (isHuman)
	{
		formElt = document.getElementById("commentform");
		formElt.submit();
		return true;	// shouldn't return.
	}
	else
	{
		alert("Please correctly identify the cats.");
		return false;
	}
}

formElt = document.getElementById("commentform");
formElt.onsubmit = function() { return Asirra_CheckIfHuman(HumanCheckComplete); };
