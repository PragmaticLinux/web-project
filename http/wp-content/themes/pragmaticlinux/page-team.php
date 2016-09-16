<?php get_header(); ?>

<div class="row header-page text-center">
    <h1>Team</h1> 
</div>
<div class="row">
    <div class="container">
        <div class="col-md-9 col-md-offset-1 " style="padding-top:15px;">
            <p>
                <?php
                the_post();
                the_content();
                ?>
            </p>
        </div>
        <div class="col-md-2" style="padding-top:22px;">
            <a href="/get-involved/" class="btn btn-primary btn-lg text-uppercase">Get Involved</a>
        </div>
        <div class="col-md-12">
            <hr class="pragmatic-hr">
        </div>
        <div class="col-md-12">

            <?php
            $args = array('post_type' => 'team', 'order' => "ASC");
            $member = new WP_Query($args);
            if ($member->have_posts()) {
                while ($member->have_posts()) {
                    $member->the_post();
                    ?>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="col-md-12">
                            <div class="text-center">
                                <h4><?php the_title(); ?></h4>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" class="img-responsive">
                            </div>
                            <div class="col-md-10">
                                <?php the_content(); ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>

                    </div>
                    <?php
                }
            }
            ?>
        </div>















    </div>
</div>
<br><br>
<?php get_footer(); ?>

