=== TDD Progress Bar ===
Contributors: taylorde
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FEQG5KTTPRRXS
Tags: progress, shortcode, widget
Requires at least: 3.3
Tested up to: 3.3.1
Stable tag: 0.5.2

Configure and display any number of percent-complete progress bars.

== Description ==

TDD Progress Bar allows you to track the progress of multiple projects. Actually, as many as you'd like. A cool feature is the ability to "race" multiple projects at once. Progress bars are managed in the admin side and displayed using shortcode. Oh, and did I mention it has a very pretty animation?

== Screenshots ==
1. A Progress Bar "race"
2. Progress Bar management page showing all the colors available
3. A solo Progress Bar
4. Bar Options

Unfortunately screenshots don't really capture how cool these look while animating...

== Frequently Asked Questions ==

= It doesn't look right.. =
This project uses some cutting-edge CSS3 to make things look cooler like drop shadow, inner-shadow, border-radius, etc. If it doesn't look right in your browser first, consider upgrading, second: let me know what browser you're using to see if I want to consider support. Things should degrade gracefully for non-cutting edge browsers (although IE6 is still going to look like crap no matter what you do). You're welcome to write your own CSS to override what I've already written (or un-check "Default Styles" on the settings page and roll-yer-own). Of course, if it's supposed to look right and it isn't, then there may be issue you should let me know about...

= How do I use the filter hook to modify a percentage? =
Add this to your functions.php file, or your own plugin.
`	<?php
		add_filter( 'tdd_pb_calculated_percentage', 'change_percentage', 10, 2 );
		function change_percentage($percentage, $id){
			//Only apply to post (bar) ID 120, if it isn't 120, just return
			if ($id != 120)
				return $percentage;

			$newpercentage = $percentage + 10; // add 10% for effort.
			return $newpercentage;
		}
	?>
`
You can use this to add 10%, like the example above, or do something cool like pull in data from an external API. If you come up with something fun and clever, I'd love to feature it!

== Installation ==

Standard plugin installation procedures apply.

== Usage ==
In the menus, you'll see a new menu for "Progress Bars" which is what you'll use to add and manage progress bars. There's also an area here for settings, but honestly if you never touch them, things will work fine.

After configuring a progress bar, insert it anywhere you'd like with shortcode.

* Simple example: `[progress id=32]`
* Race multiple bars: `[progress id=32,35,54]`
* Set the bar's width: `[progress ids=12,33 width="25%"]`
* Set the height of the bar `[progress ids=12 height="50px"]
* Align Center: `[progress ids=4 width="50%" class="aligncenter"]`

Note that "id" and "ids" are interchangeable

Or use the widget which allows you to pick bars to display

== Changelog ==

= 0.5.2 =
* FIX: An issue with the minified CSS resulted in ugly progress bars unless WP_DEBUG was set to true. Thanks to @maximus85018 for pointing it out.

= 0.5.1 =
* Fixed an issue where all the shortcodes in the Progress Bar Administration Panel were displaying the same.
* Now allows for two digit precision floating numbers on percentages inputed, calculated, and the start/end values.

= 0.5 =
* UPGRADE NOTICE: Because of the change to how bars in the widget are selected and stored your widgets will not display properly until you re-configure them. Sorry for any inconvenience, but I thought the new interface was totally worth it.
* Much easier to select bars to display in the widget thanks to Chosen.js.
* Custom Colors! Now possible to set custom colors for each bar using the very nice miniColors color picker
* Lots of sanitization behind the scenes to make the plugin more secure
* Height options. It's now possible to set the height of the progress bar(s) in the shortcode by passing a CSS height value. See "Usage" for an example

= 0.4 =
* Progress bars can now be defined using a percentage or two numbers ( x of y ) and let the plugin do the calculation
* The text display on the bar itself can be nothing, the percentage, the two numbers, or both. The global setting to turn this on and off still exists.
* Changed the checkbox on the admin page to make it clear it is a global "off" switch for bar text, not just percentage.
* Added a filter hook for the percentage being shown. This provides the ability to hook in your own method of calculating percentages. The hook name is `tdd_pb_calculated_percentage`

= 0.3.5 =
* Fixed: The malformed CSS classes were still present in the minified file causing errors. This has been fixed.

= 0.3.4 =
* SVN issue means anyone downloading 0.3.3 may have only received a subset of the files needed. This is a version bump to get around that, no other changes.

= 0.3.3 =
* Fixed: Some CSS classes were malformed causing the wrong color progress bar to display.

= 0.3.2 =
* Added a description textbox to the progress bar widget. Displays below the progress bar.
* Modified the title tag of the progress bar to include the percentage.
* Added role="progressbar" and aria hints to the progress bar container
* Fixed an issue where the title of the widget instance wasn't be included in the widget admin form container header. (confusing, I know). Basically, it's easier to see what the widgets are when they are all collapsed.
* Animation Javascript is now loaded minimized when WP_DEBUG == false
* Props to user ablereach for providing the input for much of this update, specifically the input on accessibility.

= 0.3.1 =
* Now loads a minimized and optimized version of the stylesheet when WP_DEBUG == false
* Stylesheet run through prefixr which may broaden browser support for the advanced CSS3 bling I use.

= 0.3 =
* New Colors! Added Black and Silver to the mix.
* Updates the CSS classes to all be prefixed by tdd_pb. This may break any custom CSS you were using, but it eliminates conflicts with other themes and plugins.
* Adds basic widget functionality making it much easier to add progress bars into widgetized areas.

= 0.2.1 =
* fixes an issue where the get_text_domain() function was referencing a non-existant directory and throwing an error

= 0.2 =
* Fixes an issue where the "quick edit" link (and others) would not show up on the Manage Posts screen.
* Also a version bump to 0.2 (which should have happened instead of 0.1.3)

= 0.1.3 =
* Given the major change to the CSS, the javascript to make things animate also had to change pretty extensively. One side effect is that the animations on a page will eventually slow to a standstill at the rate of about 1-2 pixels every 15 seconds or so. The animation is still a work in progress...
* Major bug fixes for the CSS to show up in, well, pretty much everything but Chrome. Didn't realize I was using an unsupported style.
* Small CSS fix for the "Strawberry" color - now shows up correctly
* Quick Edit mode on the "Manage Progress Bars" page now has the ability to change percentage and bar color. No need to specifically edit the progress bar now.
* Added the function tdd_pb_get_bars() function, which could be used in templates, but I'm going to leave undocumented for the moment so If it needs to change, it can. It's mostly designed for internal use.
* Added [tdd_pb] as a shortcode alias for [progress]. I think most people will use [progress], but I wanted to use [tdd_pb] since it's all over the code
* The Default CSS and Javascript are now added to the progress bar pages in wp-admin.
* Progress bars are now displayed in wp-admin on the "Manage Progress Bars" and the "Edit Progress Bar" screens

= 0.1.2 =
* Settings page actually works now
* Settings page now references __FILE__ instead of some arbitrary tag.

= 0.1.1 =
* Left some debugging code for the advanced debug bar in the script. Have to remove it to make this thing work.

= .1 =
The first version admin side as well), and remove the action that adds the styles to the public side.

= 0.1.2 =
* If you'd like the settings page to work, upgrade.

= 0.1.1 =
* Blocking bugfix. Plugin won't work unless you upgrade

= .1 =
Yes, you should totally upgrade to the first version released...