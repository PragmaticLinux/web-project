<?php get_header(); ?>

<div class="row header-page text-center">
    <h1><?php the_title(); ?></h1> 
</div>
<div class="row">
    <div class="container">
        <?php 
        the_post();
        the_content(); ?>
    </div>
</div>
<br><br>
<?php get_footer(); ?>

