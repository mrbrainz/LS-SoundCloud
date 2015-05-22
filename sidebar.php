<div class="span2 appo_sidebar" id="sidebar">
	<?php if (is_page(array('stream','reposts','favourites'))) : ?>
  	<?php 	
	global $sc;
	if (!$sc) {
		$sc = sc_getuser();	
	}
	
			$me = $sc->get('me');
				if (is_array($me) && $me['error'] == 1) {
					scErrorHandle($me);
					$me = false;
				} else {
					$me = json_decode($me);
				} ?>
	<?php if ($me): ?>
    <div id="profile-panel">
    	<div class="battsvatar">
        	<a href="<?php echo esc_html($me->permalink_url); ?>" target="_blank"><img src="<?php echo str_replace('large','t300x300',$me->avatar_url);?>" alt="<?php echo esc_html($me->username);?>" title="<?php echo esc_html($me->username);?>" /></a>
        </div>
        <div class="fass-deets">
        	<h4 class="dude"><a href="<?php echo esc_html($me->permalink_url); ?>" target="_blank"><?php echo esc_html($me->username);?></a></h4>
            <small class="dudeisacamelspenis"><a href="<?php echo esc_html($me->permalink_url); ?>" target="_blank"><?php echo esc_html($me->permalink);?></a></small>
			<p class="dondeeres"><?php echo ($me->city) ? esc_html($me->city).', ' : false; echo esc_html($me->country);?></p>
            <div class="zombiecount">
            	<p class="brains"><strong>Followers:</strong> <?php echo $me->followers_count; ?></p>
                <p class="iwatchherthroughbinoculars"><strong>Following:</strong> <?php echo $me->followings_count; ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
  
  	<div id="settings-panel">
  	<a href="javascript:void(0)" onclick="scSettingPanel()" class="awesome nssc settingstoggle"><i class="fa fa-cog"></i></a>
  		<div class="settingscont">
  			<h2 class="set-head">Settings</h2>
  			<form id="settings">
  				<p class="nssc-option">
  					<label for="nsscopt-limit">Tunes Per Page</label>
  						<select id="nsscopt-limit" name="nsscopt-limit" onchange="limitChange(this)">
  							<?php $limits = array(5,10,15,20,25,30,35,40,50,60);
  								$currentlimit = get_user_meta(get_current_user_id(),'nssclimit',true);
  								if (!$currentlimit) $currentlimit = 20;
  								$output = false;
  								foreach ($limits as $l) {
  									$output .= '<option value="'.$l.'"'.selected($l,$currentlimit,false).'>'.$l.'</option>';
  								}
  								echo $output; ?>
  						</select>
  				</p>
  				<p class="nssc-option">
  					<label for="nsscopt-length">Length Filter</label>
  						<select id="nsscopt-length" name="nsscopt-length" onchange="lengthChange(this)">
  							<?php 
  								$currentlength = get_user_meta(get_current_user_id(),'nssclength',true);
  								if (!$currentlength) $currentlength = 0; ?>
  								<option value="0"<?php selected(0,$currentlength); ?>>All</option>
  								<option value="1"<?php selected(1,$currentlength); ?>>Only Tunes</option>
  								<option value="2"<?php selected(2,$currentlength); ?>>Only Mixes</option>
  						</select>
  				</p>
  				<p class="nssc-option">
                	<label for="nssc-slslider">Tune/Mix Length Split</label>
  					<div id="nssc-slslider" data-defleng="<?php echo get_user_meta(get_current_user_id(),'nssclengthsplit',true); ?>"></div>
  					<div id="nssc-songlength"></div>
  				</p>
                
  				<p class="nssc-option"><a href="javascript:void(0)" class="awesome grey clear-reposts" onclick="clearRepostCache();"><i class="fa fa-remove"></i> Clear Repost Cache</a></p>
                 <?php if(user_can(get_current_user_id(),'administrator')) : ?>
                <?php 
				if (!DB_TOKEN) : ?>
  				<p class="nssc-option"><a href="/?nssc-dbauth" class="awesome nssc"><i class="fa fa-dropbox"></i> Connect Dropbox</a></p>
                <?php else : ?>
                <?php # Include the Dropbox SDK libraries
				global $dbxClient;
				
				if (!$dbxClient) {
					$appInfo = dbx\AppInfo::loadFromJsonFile(get_stylesheet_directory()."/inc/nsscjsonfmb.json");	
					$dbxClient = new dbx\Client(DB_TOKEN, "NSSC/1.0");	
				}
				$accountInfo = $dbxClient->getAccountInfo(); ?>
  				<div id="dropboxpanel">
                    <p class="nssc-option">
                        <a id="nssc-dbd" href="javascript:void(0)" class="connect-dropbox awesome grey" onclick="deauthDB()"><i class="fa fa-ban"></i> Disconnect Dropbox</a> 
                        <label for="nssc-dbd">Connected To Dropbox As <strong><?php echo $accountInfo['display_name']; ?></strong></label>
                    </p>
                    <div class="dbusage">
                        <?php echo outputDBUsage(($accountInfo['quota_info']['shared'] + $accountInfo['quota_info']['normal']),$accountInfo['quota_info']['quota']); ?>
                    </div>
                </p>
               	<?php endif; ?>
                <?php endif; ?>
  			</form>
  		</div>
  	</div>
    <?php endif; ?>
</div> 