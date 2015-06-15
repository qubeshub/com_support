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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!($this->ticket instanceof \Components\Support\Models\Ticket))
{
	$this->ticket = new \Components\Support\Models\Ticket($this->ticket);
}

$base = rtrim(Request::base(), '/');
if (substr($base, -13) == 'administrator')
{
	$base = rtrim(substr($base, 0, strlen($base)-13), '/');
	$sef = 'support/ticket/' . $this->ticket->get('id');
}
else
{
	$sef = Route::url($this->ticket->link());
}
$link = $base . '/' . trim($sef, '/');

$usertype = Lang::txt('COM_SUPPORT_UNKNOWN');
if ($this->ticket->submitter('id'))
{
	jimport( 'joomla.user.helper' );
	$usertype = implode(', ', JUserHelper::getUserGroups($this->ticket->submitter('id')));
}

$message = '';
if ($this->delimiter)
{
	$message .= $this->delimiter . "\n";
	$message .= Lang::txt('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
	$message .= 'Message from ' . rtrim(Request::base(), '/') . '/support / Ticket #' . $this->ticket->get('id') . "\n";
}
$message .= '----------------------------'."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET')).': '.$this->ticket->get('id')."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_SUMMARY')).': '.$this->ticket->get('summary')."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED')).': '.$this->ticket->get('created')."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')).': '.$this->ticket->submitter('name') . ($this->ticket->get('login') ? ' ('.$this->ticket->get('login').')' : '') . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE')).': '.$usertype."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL')).': '. $this->ticket->get('email') ."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_IP_HOSTNAME')).': '. $this->ticket->get('ip') .' ('.$this->ticket->get('hostname').')' ."\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_OS')).': '. $this->ticket->get('os') . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_BROWSER')).': '. $this->ticket->get('browser') . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_UAS')).': '. $this->ticket->get('uas') . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_COOKIES')).': ' . ($this->ticket->get('cookies') ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED') : Lang::txt('COM_SUPPORT_COOKIES_DISABLED')) . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_REFERRER')).': '. $this->ticket->get('referrer') . "\n";
$message .= '----------------------------'."\n\n";
$message .= $this->ticket->content('clean');
if ($this->ticket->attachments()->total() > 0)
{
	$message .= "\n\n";
	foreach ($this->ticket->attachments() as $attachment)
	{
		$message .= $base . '/' . trim(Route::url($attachment->link()), '/') . "\n";
	}
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
