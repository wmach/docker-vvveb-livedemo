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

namespace Vvveb\Controller\Feed;

use Vvveb\Controller\Base;

class Posts extends Base {
	function index() {
		$posts = new \Vvveb\Sql\PostSQL();

		$results = $posts->getAll();

		if ($results && isset($results['posts'])) {
			foreach ($results['posts'] as $id => &$post) {
				if (isset($post['images'])) {
					$post['images'] = json_decode($post['images'], 1);

					foreach ($post['images'] as &$image) {
						$image = Images::image($image, 'post');
					}
				}

				if (isset($post['image'])) {
					$post['images'][] = Images::image($post['image'], 'post');
				}

				if (empty($post['excerpt']) && ! empty($post['content'])) {
					$post['excerpt'] = substr(strip_tags($post['content']), 0, 200);
				}
			}
		}
		//var_dump($results);
		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		$this->view->posts = $results;

		//die();

		//return null;

		//return $results;
	}
}
