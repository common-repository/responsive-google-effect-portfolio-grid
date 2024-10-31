<?php
function RGPGrid($attr)
{
	global $rgpgObj;
	
	$rgpg_category = isset($attr['category'])?$attr['category']:'';
	$rgpg_width = isset($attr['width'])?$attr['width']:'250';
	$rgpg_height = isset($attr['height'])?$attr['height']:'250';
	$rgpg_title_font = isset($attr['titlefont'])?$attr['titlefont']:'38';
	$rgpg_title_color = isset($attr['titlecolor'])?$attr['titlecolor']:'#000';
	$rgpg_content_color = isset($attr['contentcolor'])?$attr['contentcolor']:'#000';
	$rgpg_content_font = isset($attr['contentfont'])?$attr['contentfont']:'16';
	$rgpg_background_color = isset($attr['containerbackground'])?$attr['containerbackground']:'#ddd';
	$rgpg_button_background_color = isset($attr['buttonbackgroundcolor'])?$attr['buttonbackgroundcolor']:'#ddd';
	$rgpg_button_font_color = isset($attr['buttonfontcolor'])?$attr['buttonfontcolor']:'#ddd';
	$rgpg_button_border_color = isset($attr['buttonbordercolor'])?$attr['buttonbordercolor']:'#ddd';
	$rgpg_link_text = isset($attr['buttontext'])?$attr['buttontext']:'VISIT WEBSITE';
	$rgpg_id = isset($attr['id'])?$attr['id']:'1';
	$rgpg_word_limit = isset($attr['contentwordcount'])?$attr['contentwordcount']:'50';
	$rgpg_container_height = isset($attr['containerheight'])?$attr['containerheight']:'50';
	
	$categoryarg = '';
	if(!empty($rgpg_category))
	{
		$rgpgposts  = get_posts(array(
			'post_type' => 'rgpg_portfolio',
			'numberposts'       => -1,
			'tax_query' => array(
				array(
				'taxonomy' => 'rgpg_category',
				'field' => 'term_id',
				'terms' => $rgpg_category)
			))
		);
	}
	else
	{
		$rgpgposts  = get_posts(array(
			'post_type' => 'rgpg_portfolio',
			'numberposts'       => -1,
			)
		);
	}
	?>
	<style type="text/css">
		#rgpg-container-<?php echo $rgpg_id; ?> .og-details h3{
			font-size: <?php echo $rgpg_title_font.'px'; ?>;
			color: <?php echo $rgpg_title_color;?>
		}
		#rgpg-container-<?php echo $rgpg_id; ?> .og-details p{
			color: <?php echo $rgpg_content_color;?>;
			font-size: <?php echo $rgpg_content_font.'px'; ?>;
		}
		#rgpg-container-<?php echo $rgpg_id; ?> .og-expander{
			background-color: <?php echo $rgpg_background_color;?>;
		}
		#rgpg-container-<?php echo $rgpg_id; ?> li.og-expanded > a:after{
			border-color: transparent transparent <?php echo $rgpg_background_color;?>;
		}
		#rgpg-container-<?php echo $rgpg_id; ?> .og-details a{
			background-color: <?php echo $rgpg_button_background_color;?>;
			color: <?php echo $rgpg_button_font_color;?>;
			border: 3px solid <?php echo $rgpg_button_border_color;?>;
		}
	</style>

	
	<div class="rgpg-grid" id="rgpg-container-<?php echo $rgpg_id; ?>">	
		<input type="hidden" id="rgpg-hidden-<?php echo $rgpg_id; ?>" value="<?php echo $rgpg_link_text; ?>">
		<div class="main">
			<ul  class="og-grid">
			<?php
				foreach($rgpgposts as $postData) 
				{
					$gridURL       = get_permalink($postData->ID);
					$gridtitle     = $postData->post_title;
					$gridcontent   = $postData->post_content;
					$gridcontent   = $rgpgObj->rgpg_limit_words($gridcontent,$rgpg_word_limit);
					$postthumb     = wp_get_attachment_image_src( get_post_thumbnail_id($postData->ID), array( $rgpg_width, $rgpg_height) );
					$rgpg_thumbnil = $postthumb['0'];
					$postimage_full= wp_get_attachment_image_src( get_post_thumbnail_id($postData->ID), 'full' );
					$rgpg_full     = $postimage_full['0'];
					
					$meta_values = get_post_meta( $postData->ID, '_rgpg_portfolio_link');
					$portfolio_link = $meta_values[0];
					
					$link = ltrim($portfolio_link,'http://');
					$custom_link = 'http://'.$link;
					?>
					<li>
						<a id="<?php echo $rgpg_id; ?>" href="<?php if(!empty($portfolio_link)){echo $custom_link;}else{echo $gridURL;} ?>" data-largesrc="<?php echo $rgpg_full; ?>" data-title="<?php echo $gridtitle; ?>" data-description="<?php echo $gridcontent; ?>">
							<img src="<?php echo $rgpg_thumbnil; ?>" style="width:<?php echo $rgpg_width; ?>px;height:<?php echo $rgpg_height; ?>px" alt="<?php echo $gridtitle; ?>"/>
						</a>
					</li>
					<?php
					}
			?>
			</ul>
		</div>
	</div>
<?php
}
?>