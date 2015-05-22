<?php if (is_user_logged_in()) { session_start(); } ?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="en-GB" prefix="og: http://ogp.me/ns#">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="en-GB" prefix="og: http://ogp.me/ns#">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html>
<!--<![endif]-->
<head>	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">	
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
    <script type="text/javascript">
    var iuli = <?php echo (is_user_logged_in()) ? 'true' : 'false'; ?>;
    
    var ssd = '<?php echo get_stylesheet_directory_uri(); ?>';
    var jsn = '<?php echo wp_create_nonce('jaxysmash'); ?>';
   
    <?php $bodyclasses = (!is_user_logged_in() && is_home()) ? 'marketing' : false; ?>
    <?php if ($bodyclasses) : ?>
    jQuery(window).resize(function() {
        jQuery('body.marketing .home-div').height(jQuery(window).height());
    });
    
    jQuery(document).ready(function() {
        jQuery('body.marketing .home-div').height(jQuery(window).height());	
    });
    <?php endif; ?>
    </script>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body  <?php body_class($bodyclasses); ?>>
<?php if (is_user_logged_in() || is_page('about')) : ?>
<header class="the-head">
	<div class="container">
		<div class="row-fluid about_space">
			<div class="span12">
				<div class="span3 headlogo">
					<h2 class="nssc-logo-head"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('title');?></a> <span class="head-b">BETA</span></h2>
				</div>
				<div class="span9">
                	<?php if (is_user_logged_in()) :?>
                    <div class="navbar">
                    	<div class="navbar-inner">
                            <a data-target=".navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            </a>
                            <div class="nav-collapse collapse navbar-responsive-collapse ">
                                <ul class="nav">
                                        <?php wp_nav_menu( array( 
                                                'theme_location' => 'header-menu-logged-in',
                                                'menu_class' => false,
                                                'container'       => ' ',
                                                'items_wrap'      => '%3$s'
                                            )); ?> 
                                <li class="logoutlink"><a href="<?php echo wp_logout_url(); ?>" class="logout">Log Out</a></li>
                                </ul>
                            </div><!-- /.nav-collapse -->
                        </div><!-- /navbar-inner -->
                    </div>
                    <?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</header>
<div class="notice notice-info span8" style="margin: 20px auto 10px;float: none;">
<p><strong>Stuff's a bit busted at the moment.</strong> Soundcloud changed some stuff and busted the stream. I'm created a quick fix but there's some glitches. Fixes soon.<br /><br />Shalom, Brainz</p>
</div>
<?php endif; ?>