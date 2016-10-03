<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Pragmatic Linux - Web Development</title>
<!--        <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
<!--        <meta name="viewport" content="width=device-width, initial-scale=1"> -->
        <?php wp_head(); ?>
    </head>

    <body>
        <nav class="navbar navbar-default" style="height:85px;">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a href="<?php echo home_url(); ?>"><img src="/pragmaticlinux/resource/images/logo/PragramaticLinuxLogoInvert.png" class="img-responsive " style='max-height:85px;margin-left:10%;' alt="<?php echo wp_title();?>" /></a>
                        </div>

                    </div>
                    <div class="col-md-8">
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse " style="float: right;  margin-right: 5%;" >

                            <?php
                            $downloadButton = '<li class="download-button"><a href="'.  get_home_url().'/download" class="btn btn-primary">DOWNLOAD</a></li> </ul>';
                            $menu = wp_nav_menu(
                                    array('theme_location' => 'primary',
                                        'menu_class' => 'nav navbar-nav',
                                        'container_id' => 'menu-navigation',
                                        'echo' => false,)
                            );
                            $menu = str_replace("</ul></div>", $downloadButton, $menu);
                            echo $menu;
                            ?>
                        </div><!-- /.navbar-collapse -->

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </nav>


