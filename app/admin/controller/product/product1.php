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
use function Vvveb\humanReadable;
use function Vvveb\sanitizeHTML;
use Vvveb\Sql\ProductSQL;
use Vvveb\System\CacheManager;
use Vvveb\System\Core\View;
use Vvveb\System\Images;
use Vvveb\System\Sites;
use Vvveb\System\Validator;

class Product extends Edit {
	protected $type = 'product';

	protected $object = 'product';

	function getThemeFolder() {
		return DIR_THEMES . DS . Sites::getTheme() ?? 'default';
	}

	function index() {
		$view = $this->view;

		/* Media modal configuration */
		$admin_path      = \Vvveb\adminPath();
		$controllerPath  = $admin_path . 'index.php?module=media/media';
		$view->scanUrl   = "$controllerPath&action=scan";
		$view->uploadUrl = "$controllerPath&action=upload";
		$theme           = Sites::getTheme() ?? 'default';
		$view->themeCss  = PUBLIC_PATH . "themes/$theme/css/admin-post-editor.css";

		$postOptions   = [];
		$post_id       = $this->request->get[$this->object . '_id'] ?? $this->request->post[$this->object . '_id'] ?? false;
		$posts         = new ProductSQL();

		if ($post_id) {
			$postOptions[$this->object . '_id'] = (int)$post_id;
		} else {
			if (isset($this->request->get['slug'])) {
				$postOptions['slug'] = $this->request->get['slug'];
			}
		}

		if ($postOptions) {
			$post = $posts->get($postOptions + $this->global);

			if (! $post) {
				$message = sprintf(__('%s not found!'), humanReadable(__($this->type)));
				$this->notFound(false, ['message' => $message, 'title' => $message]);
			}

			//featured image
			if (isset($post['image'])) {
				$post['image_url'] = Images::image($post['image'], $this->object);
			}

			//gallery
			if (isset($post['images'])) {
				$post['images'] = Images::images($post['images'], $this->object);
			}

			//$productImages = $posts->getImages($postOptions);
		} else {
			$post['image_url'] = Images::image('', $this->object);
		}

		if (isset($post['date_modified'])) {
			$post['date_modified'] = str_replace(' ', 'T', $post['date_modified']);
		} else {
			$post['date_modified'] = date("Y-m-d\TH:i:s", isset($post['date_modified']) && $post['date_modified'] ? strtotime($post['date_modified']) : time());
		}

		if (isset($post[$this->object . '_content'][1]['slug'])) {
			//$post['url'] = \Vvveb\url("content/{$this->type}/index", ['slug'=> $post[$this->object . '_content'][1]['slug']]);
			$post['url'] = \Vvveb\url("content/{$post['type']}/index", $post);
		}

		$this->type = $post['type'] ?? $this->type;
		$type_name  = humanReadable(__($this->type));

		$defaultTemplate = \Vvveb\getCurrentTemplate();
		$template        = isset($post['template']) && $post['template'] ? $post['template'] : $defaultTemplate;
		$themeFolder     = $this->getThemeFolder();

		if (isset($post['url'])) {
			$design_url            = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'url' => $post['url'], 'template' => $template], false, false);
			$post['design_url']    = $design_url;
		}

		if (! file_exists($themeFolder . DS . $template)) {
			if ($template == $defaultTemplate) {
				$view->template_missing = sprintf(__('Template missing, choose existing template or <a href="%s" target="_blank">create global template</a> for %s.'), $design_url, $type_name);
			} else {
				$view->template_missing = sprintf(__('Template missing, <a href="%s" target="_blank">create template</a> for this  %s.'), $design_url , $type_name);
			}
		}

		$data     		      = $posts->getData($post);

		$data['subtract'] = [1 => __('Yes'), 0 => __('No')]; //Subtract stock options
		$view->set($data);

		$post['url']           = isset($post[$this->object . '_content'][1]['slug']) ? \Vvveb\url('post/post/index', ['slug'=> $post[$this->object . '_content'][1]['slug']]) : '';
		$defaultTemplate       = \Vvveb\getCurrentTemplate();
		$template              = (isset($post['template']) && $post['template']) ? $post['template'] : $defaultTemplate;
		$post['design_url']    = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'template' => $template, 'url' => $post['url']], false, false);

		$view->product             = $post;
		$view->taxonomies          = $this->taxonomies($post[$this->object . '_id'] ?? false);
		$view->status              = ['publish', 'draft', 'pending', 'private', 'password'];
		$view->templates           = \Vvveb\getTemplateList(false, ['email']);
		$validator                 = new Validator([$this->object]);
		$view->validatorJson       = $validator->getJSON();
	}

	function save() {
		$validator = new Validator([$this->object]);

		$post    = $this->request->post;
		$post_id = $this->request->get[$this->object . '_id'] ?? $this->request->get->post[$this->object . '_id'] ?? false;

		foreach ($post[$this->object . '_content'] as &$desc) {
			$desc['content'] = sanitizeHTML($desc['content']);
		}

		//if (($this->view->errors = $validator->validate($post)) === true)
		{
			$posts = new ProductSQL();
			//var_dump($post[$this->object . '_image']);
			$publicPath                     = \Vvveb\publicMediaUrlPath() . 'media/';
			$post[$this->object . '_image'] = $post[$this->object . '_image'] ?? [];

			foreach ($post[$this->object . '_image'] as &$image) {
				$image = str_replace($publicPath,'', $image);
			}

			//process tags
			if (isset($post['tag'])) {
				foreach ($post['tag'] as $listId => $tags) {
					foreach ($tags as $tagId => $tag) {
						//existing tag add to post taxonomy_item list
						if (is_numeric($tagId)) {
							//$post[$this->object]['taxonomy_item'][] = $tagId;
						} else {
							//add new taxonomy_item
							$tagId = $this->addCategory($listId, $tag);
						}
						$post['taxonomy_item'][] = $tagId;
					}
				}
			}

			$post = $post + $this->global;
			$posts->productImage([$this->object . '_id' => $post_id, $this->object . '_image' => $post[$this->object . '_image']]);

			if ($post_id) {
				$postId = (int)$post_id;

				$data   = [$this->object => $post, $this->object . '_id' => $postId, 'site_id' => $this->global['site_id']];
				$result = $posts->edit($data);

				if ($result >= 0) {
					$this->view->success[] = ucfirst($this->type) . ' ' . __('saved') . '!';
					//CacheManager::delete($this->object);
					CacheManager::delete();
				} else {
					$this->view->errors = [$posts->error];
				}
			} else {
				$data   = [$this->object => $post, 'site_id' => $this->global['site_id']];
				$result = $posts->add($data);

				if (! $result[$this->object]) {
					$this->view->errors = [$posts->error];
				} else {
					$post_id = $result[$this->object];
					$posts->productImage([$this->object . '_id' => $post_id, $this->object . '_image' => $post[$this->object . '_image']]);

					CacheManager::delete($this->object);
					$successMessage        = __('Product saved!');
					$this->view->success[] = $successMessage;
					$this->redirect(['module' => 'post/post', $this->object . '_id' => $post_id, 'success' => $successMessage]);
				}
			}
		}

		$this->index();
	}
}
