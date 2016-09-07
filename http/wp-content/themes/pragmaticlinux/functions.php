<?php

function pragmaticlinux_script_enqueue(){
	wp_enqueue_style('pragmaticlinux-style',get_template_directory_uri()."/pragmaticlinux.css",array(),"1.0.0","all");
}
add_action('wp_enqueue_scripts','pragmaticlinux_script_enqueue');
