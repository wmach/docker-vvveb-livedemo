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

namespace Vvveb\Controller\Product;

use function Vvveb\__;
use Vvveb\Controller\Content\Edit;
use Vvveb\Sql\ProductSQL;
use Vvveb\System\Sites;

class Product extends Edit {
	protected $type = 'product';

	protected $object = 'product';

	protected $module  = 'product/product';

	function getThemeFolder() {
		return DIR_THEMES . DS . Sites::getTheme() ?? 'default';
	}

	function save() {
		$product_id  	    = $this->request->get[$this->object . '_id'] ?? $this->request->post[$this->object . '_id'] ?? false;

		$post                    = &$this->request->post;
		$post['shipping']        = isset($post['shipping']) ? 1 : 0;
		$post['manufacturer_id'] = isset($post['manufacturer_id']) ? ($post['manufacturer_id'] ?: 0) : null;
		$post['vendor_id']       = isset($post['vendor_id']) ? ($post['vendor_id'] ?: 0) : null;

		$product_related  = $this->request->post['product_related'] ?? [];
		$product          = new ProductSQL();

		parent::save();

		if ($product_related) {
			$product->productRelated([$this->object . '_id' => $product_id, $this->object . '_related' => $product_related]);
		}

		return $this->index();
	}

	function index() {
		parent::index();

		$view                             = $this->view;
		$product                          = new ProductSQL();
		$data                             = $product->getData($view->product ?? []);
		$view->product['manufacturer_id'] = $view->product['manufacturer_id'] ?? $view->product['manufacturer_id'] ?? '';
		$view->product['vendor_id']       = $view->product['vendor_id'] ?? $view->product['vendor_id'] ?? '';
		$data['subtract']                 = [1 => __('Yes'), 0 => __('No')]; //Subtract stock options
		$data['status']                   = [0 => __('Disabled'), 1 => __('Enabled')]; //Subtract stock options
		$view->set($data);
	}
}
