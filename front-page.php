<?php
get_header();
$icon = get_site_icon_url( 512, '', get_current_blog_id() );
$img = ! $icon ? '' : "<img class='round' src='$icon'></img>";

echo "
	<div class='splash flex-center'>
		$img
	</div>
	<div id='app'></div>
	<style>
		.round { border-radius: 50%; }
		.splash { height: 100vh; }
		.flex-center { display: flex; align-items: center; justify-content: center; flex-direction: column; } 
	</style>
";

get_footer();
?>
