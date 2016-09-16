<?php get_header(); ?>
<div class="row front-image-pragmatic" style=" min-height:350px; background:url('<?php
echo
get_template_directory_uri();
?>/assets/img/dc6596b4-7560-11e6-98f8-3c6e445c294a.png') center no-repeat #000000; ">

</div>

<div class="row blocks">
    <?php
    // Get Post Left block
    $args = array('category_name' => 'left-block');
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
            ?>
            <div class="col-md-6 left-block">
                <h2 class="text-center"><?php the_title(); ?></h2>
                <p class="text-justify pharagraph"><br>
                    <?php echo $the_query->posts[0]->post_content; ?>
                </p>
            </div>
            <?php
        }
    }
    ?>
    <?php
    // Get Post Right block
    $args = array('category_name' => 'right-block');
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
            ?>
            <div class="col-md-6 right-block">
                <h2 class="text-center"><?php the_title(); ?></h2>
                <p class="text-justify pharagraph"><br>
                    <?php echo $the_query->posts[0]->post_content; ?>
                </p>
            </div>
            <?php
        }
    }
    ?>
</div>

<div class="col-md-12"><br>
    <div class="container">
        <br>
        <?php
        // Get Feature lists
        $args = array('posts_per_page' => 4, 'post_type' => 'features');
        $the_query = new WP_Query($args);
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                ?>
                <div class="col-md-3 text-center feature-list">
                    <?php the_post_thumbnail('thumbnail'); ?>
                    <h2 style="font-weight: 600;"><?php the_title(); ?></h2>
                    <?php the_excerpt(); ?>
                </div>
                <?php
            }
        }
        ?>

        <div class="col-md-12 text-center">
            <a href="/features" class="btn btn-primary" style="margin-top:20px;" >
                <show class="normal" >MORE FEATURES </show>
                <onhover class="onhover">MORE FEATURES  ></onhover>
            </a>
        </div>
        <div class="col-md-12 text-center">
            <h2 class="header-block">Who is using Pragmatic Linux Server & Pragmatic Linux</h2>
        </div>
        <div class="col-md-12 text-center" style="padding-bottom: 70px;">

            <?php
            // Get Feature lists
            $args = array('posts_per_page' => 10, 'post_type' => 'partner_post');
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    ?>
                    <div class="col-md-2 text-center" style="padding-top:15px; padding-bottom: 15px;">
                        <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" class="img-responsive" style=" position: relative; top: 50%; transform: translateY(-50%); -webkit-transform: translateY(-50%); -ms-transform: translateY(-50%); transform: translateY(-50%); margin-left:auto; margin-right:auto;" >

                    </div>
                    <?php
                }
            }
            ?>

        </div>
    </div>


    <?php get_footer(); ?>

