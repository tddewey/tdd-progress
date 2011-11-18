=== TDD Progress Bar ===
Contributors: taylorde
Donate link: http://websitesthatdontsuck.com/contact
Tags: progress, shortcode
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 0.1.2

A progress bar plugin solution.

== Description ==

TDD Progress Bar allows you to track the progress of multiple projects. Actually, as many as you'd like. A cool feature is the ability to "race" multiple projects at once. Progress bars are managed in the admin side and displayed using shortcode. Oh, and did I mention it has a very pretty animation?

== Screenshots ==
1. A Progress Bar "race"
2. Progress Bar entry screen
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

== Changelog ==

= 1.2 =
* Settings page actually works now
* Settings page now references __FILE__ instead of some arbitrary tag.

= 1.1 =
* Left some debugging code for the advanced debug bar in the script. Have to remove it to make this thing work.

= .1 =
The first version

== Upgrade Notice ==

= 1.2 =
* If you'd like the settings page to work, upgrade.

= 1.1 =
* Blocking bugfix. Plugin won't work unless you upgrade

= .1 =
Yes, you should totally upgrade to the first version released...