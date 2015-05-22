<?php session_start(); get_header(); ?>
<div class="container">
	<div class="row-fluid">
    	<div id="home-left" class="home-div">

        </div>
        <div id="home-right" class="home-div">
        	<div class="loginbox">
				<?php if (isset($_SESSION['loginmessage'])) : ?>
                    <div class="login-message">
                        <?php echo $_SESSION['loginmessage']; ?>
                    </div>
                <?php endif; ?>
                <a href="<?php bloginfo('url'); ?>?nssc-sclogin"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/btn-connect-sc-l.png" title="Login With Soundcloud" alt="Login With Soundcloud" /></a>
            </div>
            <div id="home-copy">	
				<?php $page = get_page_by_path('home'); echo apply_filters('the_content',$page->post_content); ?>
            </div>
            <div id="home-market-nav">
				<?php wp_nav_menu( array( 
                        'theme_location' => 'header-menu-logged-out',
                        'container'       => ' ',
						'menu_class' => 'nav'
                    )); ?> 
            </div>
        </div>
	</div>
</div>

<?php get_footer(); ?>