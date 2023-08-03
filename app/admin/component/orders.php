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

use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;
use Vvveb\System\Images;

class Orders extends ComponentBase {
	public static $defaultOptions = [
		'start'           => 0,
		'limit'           => 10,
		'language_id'     => 1,
		'site_id'         => 1,
		'order_id'        => 'url',
		'rows'            => ['url', 4],
		'id_manufacturer' => NULL,
		'order'           => ['url', 'price asc'],
		'id_category'     => 'url',
		'id'              => NULL,
		'order_status'    => NULL,
	];

	public $options = [];

	function results() {
		$orders = new \Vvveb\Sql\OrderSQL();

		$results = $orders->getAll($this->options);

		foreach ($results['order'] as $id => &$order) {
			if (isset($order['images'])) {
				$order['images'] = json_decode($order['images'], 1);

				foreach ($order['images'] as &$image) {
					$image = Images::image('order', $image);
				}
			}

			if (isset($order['image'])) {
				$order['images'][] = Images::image('order', $order['image']);
			}
		}

		list($results) = Event::trigger(__CLASS__, __FUNCTION__, $results);

		return $results;
	}
}
