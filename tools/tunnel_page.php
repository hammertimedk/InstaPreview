<?php defined('C5_EXECUTE') or die("Access Denied.");

/* 	Tool for InstaPreview
	Gets called via. Ajax and tunnels a page to the user (so we can render the page as if we weren't logged in)
	(c) 2014 Arni Johannesson - Kramerica Industries (kramerican.dk)
	MIT License
*/
	
	if ($_REQUEST["method"] == "tunnelpage") {
	
	 echo file_get_contents(BASE_URL."/index.php?cID=".$_REQUEST["cID"]);
	 
	 //Done tunneling, safe to revert to previous version
	 $c = Page::getById($_REQUEST["cID"]);
	 
	 //Gets current active version (preview)
	 $currentversion = CollectionVersion::get($c, "ACTIVE"); //Gets the current active version
	 
	 //Approve previous version
	 if ($_REQUEST["versionID"]) {
	  $oldversion = CollectionVersion::get($c, $_REQUEST["versionID"]);
	  $oldversion->approve(false);
	 }
	 
	 //Delete preview version
	  $currentversion->delete();

	 
	 exit();
	 
	 }
	
	//If not tunnelling, this tool will display minimal loading UI
	$html = Loader::helper("html");
?>
<html style="display: none;">
<head>
 <title>Loading Preview ...</title>
 <?php echo $html->css("csspinner.min.css","instapreview"); ?>
</head>
<body>

<div class="wrapper">

<?php if ($_REQUEST["method"] == "pleasewait") { ?>

 <div class="spinner"><div class="csspinner double-up"></div></div>
 <h1 class="header">Loading Preview, Please Wait ...</h1>
 
<?php } else if ($_REQUEST["method"] == "ajaxerror") {  ?>

 <div class="error_icon"></div>
 <h1 class="header">Oww... Don't let Grumpy Cat get you down ...</h1>
 <div class="errortext">Something went wrong while we tried to load the preview. This block might not work with InstaPreview, but you're welcome to try again.</div>

<?php } ?>

</div>

<script>
 window.onload = function() {
  document.documentElement.removeAttribute("style");
 }
</script>
</body>
</html>

