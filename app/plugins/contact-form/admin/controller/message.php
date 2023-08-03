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

namespace Vvveb\Plugins\ContactForm\Controller;

use Vvveb\Controller\Crud;
use function Vvveb\humanReadable;

class Message extends Crud {
	protected $type = 'message';

	protected $model = 'Plugins\ContactForm\Message';

	protected $module = 'plugins/contact-form';

	function index() {
		parent::index();

		if ($this->view->message) {
			$message = &$this->view->message;
			$data    = json_decode($message['data'] ?? '{}', true);
			$meta    = json_decode($message['meta'] ?? '{}', true);

			if (is_array($data)) {
				foreach ($data as $key => $value) {
					unset($data[$key]);
					$data[humanReadable($key)] = $value;
				}

				foreach ($meta as $key => $value) {
					unset($meta[$key]);
					$meta[humanReadable(strtolower($key))] = $value;
				}

				$message['message'] = $data;
				$message['meta']    = $meta;
			}
		}
	}
}
