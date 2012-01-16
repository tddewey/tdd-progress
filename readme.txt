=== TDD Progress Bar ===
Contributors: taylorde
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FEQG5KTTPRRXS
Tags: progress, shortcode
Requires at least: 3.2.1
Tested up to: 3.3.1
Stable tag: 0.3.0

Configure and display any number of percent-complete progress bars.

== Description ==

TDD Progress Bar allows you to track the progress of multiple projects. Actually, as many as you'd like. A cool feature is the ability to "race" multiple projects at once. Progress bars are managed in the admin side and displayed using shortcode. Oh, and did I mention it has a very pretty animation?

== Screenshots ==
1. A Progress Bar "race"
2. Progress Bar management page showing all the colors available
3. A solo Progress Bar

Unfortunately screenshots don't really capture how cool these look while animating...

== Frequently Asked Questions ==

= It doesn't look right.. =
This project uses some cutting-edge CSS3 to make things look cooler like drop shadow, inner-shadow, border-radius, etc. If it doesn't look right in your browser first, consider upgrading, second: let me know what browser you're using to see if I want to consider support. Things should degrade gracefully for non-cutting edge browsers (although IE6 is still going to look like crap no matter what you do). You're welcome to write your own CSS to override what I've already written (or un-check "Default Styles" on the settings page and roll-yer-own).

== Installation ==

Standard plugin installation procedures apply.

== Usage ==
In the menus, you'll see a new menu for "Progress Bars" which is what you'll use to add and manage progress bars. There's also an area here for settings, but honestly if you never touch them, things will work fine.

After configuring a progress bar, insert it anywhere you'd like with shortcode.

* Simple example: `[progress id=32]`
* Race multiple bars: `[progress id=32,35,54]`
* Set the bar's width: `[progress ids=12,33 width="25%"]`
* Align Center: `[progress ids=4 width="50%" class="aligncenter"]`

Note that "id" and "ids" are interchangeable

Or use the widget which accepts a comma separated list of Ids to display.

== Changelog ==

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
The first version