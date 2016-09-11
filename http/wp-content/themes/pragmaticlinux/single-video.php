<?php get_header(); ?>

<div class="row header-page text-center">
    <h1>Video</h1> 
</div>
<div class="row">
    <div class="container">
        <?php
        $currentVideo = get_the_ID();
        $args = array('post_type' => 'video', 'order' => "DESC");
        $video = new WP_Query($args);
        ?>
        <div class="col-md-3">
            <div class="list-group">
                <?php
                if ($video->have_posts()) {
                    while ($video->have_posts()) {
                        $video->the_post();
                        if (get_the_ID() == $currentVideo) {
                            $active = "active";
                        } else {
                            $active = "";
                        }
                        ?>
                        <a href="<?php the_permalink(); ?>" class="<?php echo $active; ?> list-group-item"><?php the_title(); ?></a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="col-md-9">
            <?php
            if (have_posts()) {
                the_post();
                ?>
                <div class="col-md-12 feature-list">
                    <div class="col-md-12">
                        <h2><?php the_title(); ?></h2>
                        <div class="embed-responsive embed-responsive-16by9">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

    </div>
</div>
<br><br>
<?php get_footer(); ?>

