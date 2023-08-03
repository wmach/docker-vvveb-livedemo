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

namespace Vvveb\Component\Product;

use Vvveb\Sql\Product_ReviewSQL;
use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;
use Vvveb\System\Images;

class Reviews extends ComponentBase {
	public static $defaultOptions = [
		'start'           => 0,
		'limit'           => 10,
		'language_id'     => null,
		'site_id'         => null,
		'product_id'      => null,
		'status'          => 1,
	];

	public $options = [];

	function results() {
		$results['product_review'] = [];

		$reviews = new Product_ReviewSQL();

		$results = $reviews->getAll($this->options);

		if (isset($results)) {
			foreach ($results['product_review'] as $id => &$review) {
				if (isset($review['images'])) {
					$review['images'] = json_decode($review['images'], 1);

					foreach ($review['images'] as &$image) {
						$image = Images::image('review', $image);
					}
				}

				if (isset($review['image'])) {
					$review['images'][] = Images::image('review', $review['image']);
				}
			}
		}

		list($results) = Event::trigger(__CLASS__, __FUNCTION__, $results);

		return $results;
	}
}
