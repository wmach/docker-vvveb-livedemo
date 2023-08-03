<?php

/**
 * Vvveb
 *
 * Copyright (C) 2022  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace Vvveb\Component;

use Vvveb\System\Event;

class TocPosts extends Vvveb\System\Component\ComponentBase {
	public static $defaultOptions = [
		'start'                           => 0,
		'count'                           => ['url', 100],
		'id_manufacturer'                 => NULL,
		'order'                           => ['url', 'price asc'],
		'taxonomy_item_id'                => NULL,
		'count'                           => 7,
		'page'                            => 1,
		'parents_only'                    => false,
		'parents_children_only'           => false,
		'parents_without_children'        => false,
	];

	function results() {
		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
