InstaPreview
============

Instant Preview of blocks for Concrete5 - Warning: This is experimental

By: Arni Johannesson, 2014, Hammertime (www.hammerti.me / arni@hammerti.me)

See it in action: https://www.youtube.com/watch?v=SsVfuPEssNc

Installation
============

This is a run-of-the-mill Concrete5 package. Place the instapreview folder in your packages/ directory and install the package in the Dashboard. 

Usage
============

**After installation, you will now see a Preview button next to the Save button when adding or editing any block.** 

Preview can be dismissed by clicking the Dismiss Preview button in the top right corner growl. Keyboard hotkeys are somewhat supported*:

* Double press Ctrl to show the preview, double press again to return to edit mode

* When showing the Preview, Esc works as a panic button. Please note that this closes the Add Block window as well.

*Hotkey events are sometimes eaten by blocks. Previewing using keypress while editing a regular content block will not work, for example, as tinyMCE is embedded in an iFrame and the keypress doesn't bubble up to the host document.

Background
============

Late 2012 I saw this: http://www.youtube.com/watch?v=UK42Hont3to from here: http://blog.codinghorror.com/what-you-cant-see-you-cant-get/ and thought it pretty damn cool. It got me thinking about trying something similar for Concrete5, but never got around to it. Especially after seeing the upcoming 5.7 release previews, I dismissed the idea as being potentially irrellevant. My main gripe with regards to C5 content editing has always been the tinyMCE implementation and Redactor seemed to solve most of my pet peeves. 

A year later a Heureka moment hit me with regards to how to actually implement this and I decided to revisit the idea. With the not immediately impending release of v5.7, then I thought this might still be relevant as a concept also considering that the rather winding "add block"->"modal window"->"edit/save"->"publish page"->"Visit page from front end to make absolutely sure everything renders the way I want it" workflow will still exist.

**In short, this package**

* Gives the user a 100% accurate rendering of how their page will look when published
* Provides this functionality exactly where the user needs it, shortening the typical workflow by several steps (hopefully) resulting in fewer errors and less time wasted
* User can interact with elements on the Preview page in order to test out videos, slideshows or other components
* Eliminates the need for in-block preview functionality (e.g. form block, page list block)
* Achieves this effect by using the built-in Versioning system in a relatively responsible manner
* Works with all stock out-of-the-box Concrete5 blocks, and should work well with blocks that adhere to C5 standards.


How does it work
============

* This package places itself in the Add/Edit Block modal window footer. To achieve this the render method in the block view library had to be overridden in order to load the footer elements files from the package directory
* The footer elements files are lightly modified to include instapreview.php and markup for the preview button is added in the appropriate place.

When the user triggers a preview, the following steps are executed:

1. Full screen iFrame is shown with a loading message
2. We do an AJAX post in order to grab the currently active and approved version (if any) of the current page
3. We serialize #ccm-block-form which will contain the Block data and check for tinyMCE weirdness and add some required values (flagging in our request that this is an instapreview and making sure cID is passed)
4. We now submit the block form as usual. This gets processed and when C5 calls into the getVersionToModify method in the Collection model, our override is run and we force the generation of a new version of the current page and approve it immediately
5. The src of the full screen iframe is changed to our tunnelling script which streams the page back to the client via. the iFrame as if they were viewing it in the front-end
6. Preview version of the page is immediatly deleted and we revert back to any previously approved version
7. When user wishes to dismiss the preview, we simply hide it and clear the iFrame src (I thought this might optimize things a bit). Nothing further to do, as the page should be in a clean state at this point. 


Drawbacks, problems
============

* Key handlers do not always work as events may get eaten by the block the user is interacting with

* User might see inconsistent results using Blocks that modify Block form fields on submit using Javascript. (See workaround for tinyMCE) 
 
* User should preferably not interact with anything on the preview page that posts a form as the block/page they are viewing in fact no longer exists (possibly just disable any interaction with the preview with a transparent overlay, but I'm not sure if the drawbacks are worth it)

* The docs say anything in “elements” is easily overridden - in the case of block includes, this is not the case - in core/libraries/block_view.php the header and footer are included statically by eg.:
```
$footer = DIR_FILES_ELEMENTS_CORE . '/block_footer_edit.php';

later …

include($footer);
```
This seems to break with C5 philosophy (probably just code that hasn't been touched in a long while) and forced me to override the block_view.php library in a dirty way (I feel)
