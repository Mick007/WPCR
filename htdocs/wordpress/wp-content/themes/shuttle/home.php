<?php
/*
Template Name: Home
*/
?>
<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
get_header(); ?>

<div>VOTRE CODE</div>
 <?php echo do_shortcode('[allcurrencies]'); ?> 

<?php get_sidebar(); ?>
<?php get_footer(); ?>