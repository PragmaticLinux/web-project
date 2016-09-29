<?php get_header(); ?>

<div class="row header-page text-center">
    <h1>For Business</h1> 
</div>
<div class="row">
    <div class="container">
        <br>
        <?php
        // Get Feature lists
        $args = array('post_type' => 'post','category_name'=>'for-business', 'order' => "DESC");
        $feature = new WP_Query($args);
        $odd = false;
        if ($feature->have_posts()) {
            while ($feature->have_posts()) {
                $feature->the_post();
                if ($odd == false) {
                    $odd = true;
                    ?>
                    <div class="col-md-12 feature-list" >
                        <div class="col-md-2 text-center" style="padding-top: 20px;"><img src="<?php the_post_thumbnail_url('thumbnail'); ?>" ></div>
                        <div class="col-md-9">
                            <h2><?php the_title(); ?></h2>
                            <?php echo the_content(); ?>
                        </div>
                    </div>
                    <?php
                } else {
                    $odd = false;
                    ?>
                    <div class="col-md-12 feature-list">
                        <div class="col-md-9">
                            <h2><?php the_title(); ?></h2>
                            <?php echo the_content(); ?>
                        </div>
                        <div class="col-md-2 text-center" style="padding-top: 20px;"><?php the_post_thumbnail('thumbnail'); ?></div>

                    </div> 

                    <?php
                }
            }
        }
        ?>
    </div>
</div>
<br><br>
<?php get_footer(); ?>

