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

use Vvveb\System\Cache;
use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;
use Vvveb\System\Update;

class Notifications extends ComponentBase {
	public static $defaultOptions = [
		'start' => 0,
		'limit' => 10,
	];

	public $options = [];

	function results() {
		// return [];
		$cache = Cache::getInstance();

		$notifications = [
			'updates' => [
				'core' => ['hasUpdate' => 1, 'version' => '1.0'],
			],
			'sales' => [
				'new'     => ['count' => 1],
				'returns' => ['count' => 1],
			],
			'comments' => [
				'new'  => ['count' => 3],
				'span' => ['count' => 2],
			],
			'product' => [
				'reviews'   => ['count' => 3],
				'questions' => ['count' => 2],
			],
			'users' => [
				'new' => ['count' => 3],
			],
		];

		$update  = new Update();
		$updates = $update->checkUpdates('core');

		$notifications['updates']['core'] = $updates;
		$count                            = max($updates['hasUpdate'], 0);
		$notifications['count']           = $count;

		$results = [
			'notifications'   => $notifications,
			'count'           => $count,
		];

		list($results) = Event::trigger(__CLASS__, __FUNCTION__, $results);

		return $results;
	}
}
