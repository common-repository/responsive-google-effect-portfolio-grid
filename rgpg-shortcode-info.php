<?php
//-- CREATE RGPGRID FUNCTION --------------------------------------------
//-----------------------------------------------------------------------
function rgpg_createsc_func()
{
	$rgpgObj = new RGPGrid(); 
?>
<div id="rgpg_wrap" class="wrap rgpg_wrap">
    <h2><?php _e('Responsive Google Effect Portfolio Grid&nbsp;'.rgpg_get_version().'','rgpgrid'); ?></h2>
	<div class="handlediv" title="Click to toggle"><br/></div>
	<h3 class="hndle"><span><?php _e("Shortcode Parameters Description",'rgpgrid'); ?></span></h3>
	<div class="inside">
		<table cellspacing="15">
			<tr>
				<td colspan="2" style="border-bottom:1px dotted #ccc">
					<strong class="rgpg-sc">[RGPGrid category="1" width="200" height="200" titlefont="45" titlecolor="#111" contentcolor="#444" containerbackground="#e6e6e6" buttonbackgroundcolor="#e6e6e6" buttonbordercolor="#333" buttonfontcolor="#333" buttontext="Visit Website"]</strong>
					<br/><br/>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td style="font-size: 18px">
					<b><?php _e('Parameter','rgpgrid'); ?></b>
				</td>
				<td style="font-size: 18px;">
					<b><?php _e('Description','rgpgrid'); ?></b>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('width','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Width for post thumbnail','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('height','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Height for post thumbnail','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('category','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Category id, whose post you want to show','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('titlecolor','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Font color for the post title','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('titlefont','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Font-Size for the post title','rgpgrid'); ?>
				</td>
			</tr>		
			<tr>
				<td>
					<b><?php _e('contentcolor','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Font Color for the post content','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('containerbackground','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Background color for the description container of the post','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('buttonfontcolor','rgpgrid'); ?></b>
				</td>
				<td>
					<?php _e('Font color for "Visit More" button under the post','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('buttonbordercolor','rgpgrid') ?></b>
				</td>
				<td>
					<?php _e('Border color for "Visit More" button under the post','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('buttonbackgroundcolor','rgpgrid') ?></b>
				</td>
				<td>
					<?php _e('Background color for "Visit More" button under the post','rgpgrid'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php _e('buttontext','rgpgrid') ?></b>
				</td>
				<td>
					<?php _e('Change text for "Visit More" button under the post','rgpgrid'); ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
}
?>