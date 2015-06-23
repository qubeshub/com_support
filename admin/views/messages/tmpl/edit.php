<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_MESSAGES') . ': ' . $text, 'support.png');
Toolbar::apply();
Toolbar::save();
Toolbar::spacer();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('messages');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancelmsg') {
		submitform( pressbutton );
		return;
	}

	// form field validation
	if ($('#field-message').val() == '') {
		alert('<?php echo Lang::txt('COM_SUPPORT_MESSAGE_ERROR_NO_TEXT'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_SUPPORT_MESSAGE_LEGEND'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_SUPPORT_MESSAGE_SUMMARY'); ?>:</label><br />
				<input type="text" name="msg[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-message"><?php echo Lang::txt('COM_SUPPORT_MESSAGE_TEXT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<textarea name="msg[message]" id="field-message" cols="35" rows="10"><?php echo $this->escape(stripslashes($this->row->message)); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<p><?php echo Lang::txt('COM_SUPPORT_MESSAGE_TEXT_EXPLANATION'); ?></p>
		<dl>
			<dt>{ticket#}</dt>
			<dd><?php echo Lang::txt('COM_SUPPORT_MESSAGE_TICKET_NUM_EXPLANATION'); ?></dd>

			<dt>{sitename}</dt>
			<dd><?php echo Config::get('sitename'); ?></dd>

			<dt>{siteemail}</dt>
			<dd><?php echo Config::get('mailfrom'); ?></dd>
		</dl>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="msg[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>