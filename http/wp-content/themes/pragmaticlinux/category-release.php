<?php get_header(); ?>

<div class="row header-page text-center">
    <h1>History Release</h1> 
</div>
<div class="row">
    <div class="container"><br><br>
        <ul class="history-logs">
            <?php
            // Get Feature lists
            $args = array('category_name' => 'release');
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) {
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    $year = the_date("Y",null,null,false);
                    ?>
                    <li>
                        <div class="history-logs-date"><?php echo $year; ?> </div>
                        <div class="fa fa-circle fa-lg"></div>
                        <div class="history-logs-title"><?php the_excerpt(); ?></div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div>
<br><br>
<?php get_footer(); ?>

