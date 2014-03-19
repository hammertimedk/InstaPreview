<?php defined('C5_EXECUTE') or die("Access Denied.");

/* 	Tool for InstaPreview
	Gets the previously active version of a page (before preview)
	- Used to have more methods, but moved that to tunnel_page
	(c) 2014 Arni Johannesson - Kramerica Industries (kramerican.dk)
	MIT License
*/

	if ($_REQUEST["method"] == "returnActiveVersion") {
	
	 $c = Page::getById($_REQUEST["cID"]);
	 $currentversion = CollectionVersion::get($c, "ACTIVE"); //Gets the current active version
	 $draftversion = CollectionVersion::get($c, "RECENT"); //Gets any draft version
	 
	 
	 echo json_encode(Array("version"=>$currentversion->getVersionId(), "draft"=>$draftversion->getVersionId()));
	
	} 

?>