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

namespace Components\Support\Tables;

use Lang;
use User;
use Date;

/**
 * Table class for support ticket comment
 */
class Comment extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_comments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->comment = trim($this->comment);
		if (!$this->comment && trim($this->changelog) == '')
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_COMMENT'));
		}

		$this->ticket = intval($this->ticket);
		if (!$this->ticket)
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_TICKET'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = User::get('id');
		}

		if ($this->created_by && is_string($this->created_by))
		{
			$owner = User::getInstance($this->created_by);
			if ($owner && $owner->get('id'))
			{
				$this->created_by = (int) $owner->get('id');
			}
		}

		if (!$this->created)
		{
			$this->created = Date::toSql();
		}

		return true;
	}

	/**
	 * Get comments on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @param   string   $sort        Field to sort by
	 * @param   string   $dir         Direction to sort
	 * @return  array
	 */
	public function getComments($authorized, $ticket=NULL, $sort='id', $dir='ASC')
	{
		if (!$ticket)
		{
			$ticket = $this->id;
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$dir = strtoupper($dir);
		if (!in_array($dir, array('ASC', 'DESC')))
		{
			$dir = 'ASC';
		}
		$sql = "SELECT * FROM $this->_tbl WHERE ticket=" . $this->_db->Quote($ticket) . " $sqladmin ORDER BY " . $sort . " " . $dir;

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of comments on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @return  integer
	 */
	public function countComments($authorized, $ticket=NULL)
	{
		if (!$ticket)
		{
			$ticket = $this->_ticket;
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE ticket=" . $this->_db->Quote($ticket) . " $sqladmin");
		return $this->_db->loadResult();
	}

	/**
	 * Get the newest comment on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @return  object
	 */
	public function newestComment($authorized, $ticket=NULL)
	{
		if (!$ticket)
		{
			$ticket = $this->_ticket;
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery("SELECT created FROM $this->_tbl WHERE ticket=" . $this->_db->Quote($ticket) . " $sqladmin ORDER BY created DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get the newest comment on a ticket
	 *
	 * @param   integer  $authorized  Administrator access?
	 * @param   integer  $ticket      Ticket ID
	 * @return  object
	 */
	public function newestCommentsForTickets($authorized, $ticket=NULL)
	{
		if (!$ticket)
		{
			$ticket = $this->_ticket;
		}
		if (is_array($ticket))
		{
			$ticket = array_map('intval', $ticket);
			$ticket = implode(',', $ticket);
		}
		if ($authorized)
		{
			$sqladmin = "";
		}
		else
		{
			$sqladmin = "AND access=0";
		}
		$this->_db->setQuery("SELECT ticket, MAX(created) AS lastactivity FROM $this->_tbl WHERE ticket IN (" . $ticket . ") $sqladmin GROUP BY ticket");
		return $this->_db->loadAssocList('ticket');
	}

	/**
	 * Delete comments based on parent ticket ID
	 *
	 * @param   integer  $ticket  Ticket ID
	 * @return  boolean  True on success
	 */
	public function deleteComments($ticket=NULL)
	{
		if ($ticket === NULL)
		{
			$ticket = $this->ticket;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE ticket=" . $this->_db->Quote($ticket));
		if (!$this->_db->query())
		{
			$this->setError($database->getErrorMsg());
			return false;
		}
	}
}

