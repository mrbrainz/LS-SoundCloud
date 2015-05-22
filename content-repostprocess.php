<?php 	/* Repost Processor */
		
		$repostdata = $_SESSION['repostdata'];
	
	   if (!$repostdata) : ?>
       <div class="repost-error error">
       		<p><strong>ERROR:</strong> No Repost Data found. WTF?</p>
       </div>
       <?php else : ?>
       
	   
	   <?php SCdebugButton('Repost Incoming Data',json_decode(base64_decode($repostdata))); ?>
	   
		<div id="jaxyhole"></div>
	   <script type="text/javascript">	
		(function ($) {
            $(document).ready(function(){
                function smashMyJaxy() {
                    $('#jaxyhole').show().removeClass('error').addClass('working').text('Importing Reposts... Please Wait');
                    
					$.ajax({
                        type	: 'POST',
                        url		: '<?php echo get_stylesheet_directory_uri(); ?>/inc/ajax.php',
                        data	:	{ 
                                        task		: 'doreposts',
                                        _wpnonce	: '<?php echo wp_create_nonce('jaxysmash'); ?>',
										repostdata	: '<?php echo $repostdata; ?>' 
                                    },
                        
                        success : function( data, status, xhr )	{ 
                            data = jQuery.parseJSON(data);
							if (typeof(data.repostcount) != 'undefined') {
                                $('#jaxyhole').text('Successfully imported '+data.repostcount+' reposts. Loading...');
								window.location.href = "<?php bloginfo('url'); ?>?reposts";
                            }
							else if (typeof(data.error) != 'undefined') {
								$('#jaxyhole').addClass('error').removeClass('working').text(data.error);	
								window.location.href = "<?php bloginfo('url'); ?>?reposts";
							}
                        },
                        error : function( xhr, status, error ) {
                            $('#jaxyhole').addClass('error').removeClass('working').text('Something fucked up. Sorry. Try again.');	
                        }
                    });
                }
				smashMyJaxy();
            });
		}(jQuery));
        </script>
        <?php endif; ?>