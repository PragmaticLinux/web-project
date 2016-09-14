<?php get_header(); ?>

<div class="row">
    <div class="container">
        <?php
        // Get Feature lists
        $args = array('post_type' => '*');
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                ?>
                <div class="row list-blog">
                    <h2 style="color:#333;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php the_date(); ?></p>
                    <p><?php the_excerpt(); ?></p>
                    <hr>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
<br><br>
<?php get_footer(); ?>

