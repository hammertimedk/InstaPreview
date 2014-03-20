<?php defined('C5_EXECUTE') or die("Access Denied.");


class Collection extends Concrete5_Model_Collection {


		/* Updated this function to ensure that we always get a brand new version of a collection for InstaPreview 
		 - Somewhat flaky at the moment - sometimes preview collection is not approved for some reason, must investigate...
		*/
		function getVersionToModify() {
		
		 if ($_REQUEST["instapreview"]) {
		 
		  if (CollectionVersion::get($this, 'ACTIVE')->getVersionComments() == "Preview Version") {
		   //We should not find ourselves here - but our currently active version is already the preview version
		   //This can happen if a block craps out, or does some magic which causes this method to be run twice before a tunnel
		   error_log($this->getVersionObject()->getVersionID()." Preview version Already exists", 0);
		  } else {
		 
		   //error_log("Creating preview version", 0);
			$nc = $this->cloneVersion("Preview Version");
		   //error_log($nc->getVersionObject()->getVersionID()."Approving preview version", 0);
			$nc->getVersionObject()->approve(false); //Approve it immediately
				
			$versionid = CollectionVersion::get($nc, 'ACTIVE')->getVersionID();
		   //error_log($versionid." Is the currently Approved preview version", 0);
			
		  }
			
			return $nc;
		 } else {
		  return parent::getVersionToModify(); //Otherwise call into stock C5 code
		 }
		
		}



}