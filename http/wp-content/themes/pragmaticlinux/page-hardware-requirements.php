<?php get_header(); ?>

<div class="row header-page text-center">
    <h1>Hardware Requirments</h1> 
</div>
<div class="row">
    <div class="container">
        <div class="col-md-12">
            <br>
            <p>This page details the hardware required to run Pragmatic Linux and its derivative versions.
            </p>
            <p>
                Most people will want to install a desktop system Pragmatic Linux. A desktop system is typically used for personal computing tasks and has a graphical user interface (GUI), while a server system typically has a command-line interface (CLI) - Pragmatic Linux Server (PLS).
            </p>
        </div>
        <?php
        // Get Feature lists
        $args = array('post_type' => 'post', 'category_name' => 'hardware-requirements', 'order' => "DESC");
        $feature = new WP_Query($args);
        $odd = false;
        if ($feature->have_posts()) {
            while ($feature->have_posts()) {
                $feature->the_post();
                    ?>
                    <div class="col-md-10 col-md-offest-2 text-center">
                        <hr>
                        <h2><?php the_title(); ?></h2>
                    </div>
                    <div class="col-md-12 feature-list" >
                        <div class="col-md-2 text-center" style="padding-top: 20px;"><img src="<?php the_post_thumbnail_url('thumbnail'); ?>" ></div>
                        <div class="col-md-9">
                            <?php echo the_content(); ?>
                        </div>
                    </div>
                    <?php
            }
        }
        ?>
    </div>
</div>
<br><br>
<?php get_footer(); ?>

