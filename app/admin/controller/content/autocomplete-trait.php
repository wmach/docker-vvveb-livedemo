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

namespace Vvveb\Controller\Content;

use \Vvveb\Sql\categorySQL;
use Vvveb\System\Images;

trait AutocompleteTrait {
	function categoriesAutocomplete() {
		$categories = new CategorySQL();

		$results = $categories->getCategories([
			'start'       => 0,
			'limit'       => 10,
			'language_id' => 1,
			'site_id'     => 1,
			'search'      => '%' . trim($this->request->get['text']) . '%',
		]);

		$search = [];

		if (isset($results['categories'])) {
			foreach ($results['categories'] as $category) {
				$search[$category['taxonomy_item_id']] = $category['name'];
			}
		}

		$view         = $this->view;
		$view->noJson = true;

		$this->response->setType('json');
		$this->response->output($search);

		return false;
	}

	function manufacturersAutocomplete() {
		$manufacturers = new \Vvveb\Sql\ManufacturerSQL();

		$options = [
			'start'       => 0,
			'limit'       => 10,
			'search'      => '%' . trim($this->request->get['text']) . '%',
		] + $this->global;

		$results = $manufacturers->getAll($options);

		$search = [];

		foreach ($results['manufacturer'] as $manufacturer) {
			$manufacturer['image']                    = Images::image($manufacturer['image'], 'manufacturer');
			$search[$manufacturer['manufacturer_id']] = '<img width="32" height="32" src="' . $manufacturer['image'] . '"> ' . $manufacturer['name'];
		}

		//echo json_encode($search);
		$this->response->setType('json');
		$this->response->output($search);

		return false;
	}

	function vendorsAutocomplete() {
		$vendors = new \Vvveb\Sql\VendorSQL();

		$options = [
			'start'       => 0,
			'limit'       => 10,
			'search'      => '%' . trim($this->request->get['text']) . '%',
		] + $this->global;

		$results = $vendors->getAll($options);

		$search = [];

		foreach ($results['vendor'] as $vendor) {
			$vendor['image']               = Images::image($vendor['image'], 'vendor');
			$search[$vendor['vendor_id']]  = '<img width="32" height="32" src="' . $vendor['image'] . '"> ' . $vendor['name'];
		}

		//echo json_encode($search);
		$this->response->setType('json');
		$this->response->output($search);

		return false;
	}

	function productsAutocomplete() {
		$products = new \Vvveb\Sql\ProductSQL();

		$options = [
			'start'       => 0,
			'limit'       => 10,
			'search'      => '%' . trim($this->request->get['text']) . '%',
		] + $this->global;

		$results = $products->getAll($options);

		$search = [];

		foreach ($results['products'] as $product) {
			$product['image']                        = Images::image($product['image'], $this->object);
			$search[$product[$this->object . '_id']] = '<img width="32" height="32" src="' . $product['image'] . '"> ' . $product['name'];
		}

		//echo json_encode($search);
		$this->response->setType('json');
		$this->response->output($search);

		return false;
	}
}
