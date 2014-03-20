<?php defined('C5_EXECUTE') or die("Access Denied.");
/* 	InstaPreview
	Needed this override as our previews rely on manipulating the versioning system. Some blocks can flag that when they are added or updated, they should be valid for all versions. The reason? For example the Guestbook block wants this as otherwise duplicate() would kill the guestbook entries. We don't really care about that kind of stuff, as we are just creating a temporary version for preview purposes and it gets deleted immediately in anyway. So, we check for the instapreview flag in our request and always return false here in order to force process.php to call getVersionToModify()
	(c) 2014 Arni Johannesson - Kramerica Industries (kramerican.dk)
	MIT License
*/

class BlockTypeList extends Concrete5_Model_BlockTypeList {}
class BlockTypeDB extends Concrete5_Model_BlockTypeDB {}
class BlockType extends Concrete5_Model_BlockType {


		function includeAll() {
		 if ($_REQUEST["instapreview"]) {
		  return false;
		 } else {
		  return $this->btIncludeAll;
		 }
		}

}