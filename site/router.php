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

namespace Components\Support\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();

		if (!empty($query['controller']) && $query['controller'] == 'media')
		{
			$segments[] = $query['controller'];
			unset($query['controller']);

			if (!empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
			return $segments;
		}

		if (!empty($query['view']) && strncmp($query['view'], 'article', 7) == 0)
		{
			unset($query['view']);
			unset($query['id']);
		}

		if (!empty($query['task']))
		{
			switch ($query['task'])
			{
				case 'delete':
				case 'download':
				case 'stats':
				case 'ticket':
				case 'tickets':
				case 'reportabuse':
					$segments[] = $query['task'];
					unset($query['task']);

					if (!empty($query['id']))
					{
						$segments[] = $query['id'];
						unset($query['id']);
					}
					if (!empty($query['file']))
					{
						$segments[] = $query['file'];
						unset($query['file']);
					}
				break;

				case 'new':
					$segments[] = 'ticket';
					$segments[] = 'new';
					unset($query['task']);
				break;

				case 'update':
					$segments[] = 'ticket';
					$segments[] = 'update';
					unset($query['task']);
				break;

				case 'feed':
					$segments[] = 'tickets';
					$segments[] = 'feed';
					unset($query['task']);
				break;

				default:
					if (!empty($query['controller']) && $query['task'] == 'display')
					{
						$segments[] = $query['controller'];
					}
					else
					{
						if (isset($query['controller']) && $query['controller'] == 'queries')
						{
							$segments[] = $query['controller'];
						}
						$segments[] = $query['task'];
					}
					unset($query['task']);
				break;
			}
		}

		unset($query['controller']);

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$vars = array();

		$count = count($segments);

		if ($count == 0)
		{
			$vars['option'] = 'com_support';
			$vars['view'] = '';
			$vars['task'] = '';
			$vars['controller'] = 'index';
			return $vars;
		}

		switch ($segments[0])
		{
			case 'media':
				$vars['option'] = 'com_support';
				$vars['controller'] = 'media';
				if (!empty($segments[1]))
				{
					$vars['task'] = $segments[1];
				}
			break;

			case 'report_problems':
				$vars['option'] = 'com_support';
				$vars['controller'] = 'tickets';
				$vars['task'] = 'new';
			break;

			case 'queries':
				$vars['controller'] = $segments[0];
				if (!empty($segments[1]))
				{
					$vars['task'] = $segments[1];
				}
				if (!empty($segments[2]))
				{
					$vars['id'] = $segments[2];
				}
			break;

			case 'tickets':
				if (isset($segments[1]))
				{
					$vars['task'] = 'feed';
					$vars['no_html'] = 1;
					$_GET['no_html'] = 1;
				}
				else
				{
					$vars['task'] = 'tickets';
				}
				$vars['controller'] = 'tickets';
			break;

			case 'reportabuse':
				$vars['task'] = (isset($segments[0])) ? $segments[0] : '';
				if (!empty($segments[1]))
				{
					if (is_numeric($segments[1]))
					{
						$vars['id'] = $segments[1];
					}
					else
					{
						$vars['task'] = $segments[1];
					}
				}
				$vars['controller'] = 'abuse';
			break;

			case 'ticket':
			case 'delete':
			default:
				$vars['task'] = (isset($segments[0])) ? $segments[0] : '';

				if (!empty($segments[1]))
				{
					if (is_numeric($segments[1]))
					{
						$vars['id'] = $segments[1];
					}
					else
					{
						$vars['task'] = $segments[1];
					}
				}
				if (!empty($segments[2]))
				{
					$vars['file'] = $segments[2];
				}
				$vars['controller'] = 'tickets';
			break;
		}

		return $vars;
	}
}