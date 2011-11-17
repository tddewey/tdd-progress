=== TDD Progress Bar ===
Contributors: taylorde
Donate link: http://websitesthatdontsuck/contact
Tags: progress, github
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: .1

A progress bar plugin solution.

== Description ==

TDD Progress Bar allows you to track the progress of multiple projects. Actually, as many as you'd like. To make this easier, it can query the Github API to calculate a %complete -- or you can put in a manual percentage.

A cool and unique feature is the ability to race multiple projects at once.

== Installation ==

Standard plugin installation procedures apply.

This plugin does use some CSS which is inserted into `<head>`. This action can be disabled by putting the following code into functions.php although it will probably look bad (you're welcome to put the CSS elsewhere though, or just conditionally include it.

`<?php
add_action( 'wp_print_styles', 'my_deregister_tdd_pb_styles', 100 );
function my_deregister_tdd_pb_styles() {
	wp_deregister_style('tdd_pb_styles');
}
?>
`

== Changelog ==

= .1 =
The first version

== Upgrade Notice ==

= .1 =
Yes, you should totally upgrade to the first version released...