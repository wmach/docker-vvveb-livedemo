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

namespace Vvveb\Controller\Cart;

use Vvveb\Controller\Base;
use Vvveb\System\Cart\Cart as ShoppingCart;
use Vvveb\System\Core\View;

class Cart extends Base {
	function index() {
		$cart = ShoppingCart::getInstance();

		if (isset($this->request->get['product_id'])) {
			$cart->add($this->request->get['product_id']);
		}

		$results = $cart->getCart();

		foreach ($results as $k => $v) {
			//error_log($k);
			$this->view->$k = $v;
		}

		//$this->view->count = $results['count'];
	}

	function action($action, $productId = null, $quantity = 1) {
		$cart                = ShoppingCart::getInstance();
		$this->view->success = false;

		$productId   = $this->request->request['product_id'];
		$quantity    = $this->request->request['quantity'] ?? $quantity;

		if (isset($productId)) {
			switch ($action) {
				case 'add':
					$cart->add($productId, $quantity);

				break;

				case 'update':
					$cart->update($productId, $quantity);

				break;

				case 'remove':
					$cart->remove($productId);

				break;
			}
			//$this->view->success = $cart->$action($productId, $quantity);
		}

		$this->view->noJson = true;

		/*		
				$message = ['success' => false, 'message' => 'Error saving backup!'];

				echo json_encode($message);
				die();*/
		//die('test');

		return $this->index();
	}

	function remove() {
		return $this->action('remove');
	}

	function update() {
		return $this->action('update');
	}

	function add() {
		return $this->action('add');
	}
}
