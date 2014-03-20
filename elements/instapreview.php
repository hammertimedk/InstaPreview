<?php defined('C5_EXECUTE') or die("Access Denied.");
/* InstaPreview
	Markup included in Block add/edit footers
	(c) 2014 Arni Johannesson - (hammerti.me / kramerican.dk)
	MIT License
*/

global $c;

//Get our tools URL
$url = Loader::helper('concrete/urls');
$tunnel_page = $url->getToolsURL('tunnel_page','instapreview');
$version_handler = $url->getToolsURL('version_handler','instapreview');
?>

<!-- InstaPreview Markup -->
<div id="preview_wrap" style="position: fixed; top:0px;left:0px;z-index:2147483647;width:100%;height:100%;background-color:#fff;display:none;"><iframe id="preview_iframe" style="border:none;width:100%;height:100%;" ></iframe><div id="preview_info" style="position: fixed; top: 25px;right:25px;width:235px;min-height:60px;padding:10px;background-color:#000;border-radius:5px;opacity:0.85;color:#fff;cursor:move;display:none;"><div style="font-weight:bold;">This is a live preview.</div>Please do not submit any forms or surveys here. You are otherwise free to interact with the page. Click the button below to return to editing the page.<div class="ccm-ui" style="text-align:center;margin-top:10px;"><a href="javascript:void(0);" class="btn success clearpreview">Dismiss Preview</a></div></div></div>
<script>
 $(function() {

  var app_vars = {
   showing: false,
   previousversion: null,
   finishedrender: false
  }
 
	/* Loads up our fancy overlay that in turn loads in the preview */
  function triggerPreview(keypress) {
   
   //Show "Please Wait" message
   $('#preview_iframe').attr("src","<?php echo $tunnel_page; ?>?method=pleasewait");
   $('#preview_wrap').fadeIn();
   
   //First grab the currently approved version, so we can roll back to it
   $.post("<?php echo $version_handler; ?>", {method: "returnActiveVersion", cID: <?php echo $c->getCollectionId(); ?>}, function(data) {
   
      //Which version we need to revert to
      app_vars.previousversion = data.version;
   
     //Do a vanilla Save POST action for this block, flag that we are InstaPreviewing for Block Controller
     var form_fields = $('#ccm-block-form').serializeArray();
   
     //Workaround for tinyMCE not setting form content untill submit time
	  if ((typeof(tinyMCE) !== "undefined") && (tinyMCE.activeEditor !== null)) {
      if (tinyMCE.activeEditor.getContent()) {
	   for (var obj in form_fields) {
	    if (form_fields[obj].name === "content") {
	  	  form_fields[obj].value = tinyMCE.activeEditor.getContent();
	    }
	   }
	  }
	  } 
	  
	  //Flag to core block controller that this is a preview
	  form_fields.push({name: "instapreview", value: "true"});
	
	  //Make sure cID is always passed through
	  form_fields.push({name: "cID", value: "<?php echo $c->getCollectionId(); ?>"});
	
      $.post($('#ccm-block-form').attr("action"), form_fields, function(data) {
   
	  //Now tunnel the page, give the server 500ms to chill
      window.setTimeout(function() { $('#preview_iframe').attr("src","<?php echo $tunnel_page."?method=tunnelpage&cID=".$c->getCollectionId(); ?>&versionID="+app_vars.previousversion)}, 500);

     //Prep info growl
      if (keypress) {
      //tell them to release key
	 
	   window.setTimeout(function(){ $('#preview_info').fadeIn(); app_vars.finishedrender = true; },1000);
      } else {
      //tell them to hit esc button
	
	  window.setTimeout(function(){ $('#preview_info').fadeIn(); app_vars.finishedrender = true; },1000);
      }
   
   },'json').fail(function() {
    //Show error message and clean up preview version
    $('#preview_iframe').attr("src","<?php echo $tunnel_page ."?method=cleanup&cID=".$c->getCollectionId(); ?>&versionID="+app_vars.previousversion); 
	window.setTimeout(function(){ $('#preview_info').fadeIn(); app_vars.finishedrender = true; },1000);
   });  //Ajax Save
   
   },'json').fail(function() {
    $('#preview_iframe').attr("src","<?php echo $tunnel_page; ?>?method=ajaxerror");
	window.setTimeout(function(){ $('#preview_info').fadeIn(); app_vars.finishedrender = true; },1000);
   }); //Ajax get previous active version
   
  }
  
  /* Clears the preview */
  function clearPreview() {
   //Start fading out
   $('#preview_info').fadeOut();
   $('#preview_wrap').fadeOut();
   
   //Clear frame contents
    $('#preview_iframe').attr("src","");
	
	//Flag that we are no longer in preview mode
    app_vars.showing = false;
	app_vars.finishedrender = false;
  }
  
  /* Binding to clicks and keypresses */
  $('.instapreview').click(function() {
   app_vars.showing = true;
   triggerPreview(false);
  });
  
 
  var delta = 500;
  var lastKeypressTime = 0;
  var keyHandler = function (e) {
   
   //Check for escape key
  if ((app_vars.showing) && (e.which === 27)) {
    e.preventDefault();
	clearPreview();
	return false;
   }
   
   //Check for double keypress
     if (e.which === 17)
     {
        var thisKeypressTime = new Date();
        if ( thisKeypressTime - lastKeypressTime <= delta ) //Double tapping of Ctrl has taken place
         {
		 //Prevent triple press being flagged as a second double press
		  thisKeypressTime = 0;
		  
		  //If we are already showing, hide
		  if (app_vars.showing && app_vars.finishedrender) {
			e.preventDefault();
			clearPreview();
		 } else if (!app_vars.showing && !app_vars.finishedrender) {
		  //Otherwise flag that we are showing and trigger preview
		  app_vars.showing = true;
		  triggerPreview(true);
		 }
        }
        lastKeypressTime = thisKeypressTime;
     }
  }
  
   //Rebind keyhandler - remove old one from global scope first
   $(document).unbind("keyup").keyup(keyHandler);
  
  
  $('.clearpreview').click(function() {
   clearPreview();
  });  
  
  //Init preview info draggable
  $("#preview_info").draggable();
 
 });
</script>