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

use \Vvveb\Sql\PostSQL;
use function Vvveb\__;
use Vvveb\Controller\Base;
//use Vvveb\System\Component\Component;
use function Vvveb\sanitizeHTML;

class Post extends Base {
	public $type = 'post';

	private function insertComment() {
		$result = false;
		$post   = &$this->request->post;

		if (isset($post['content'])) {
			//robots will also fill hidden inputs
			$notRobot =
			(isset($post['firstname-empty']) && empty($post['firstname-empty']) &&
			isset($post['lastname-empty']) && empty($post['lastname-empty']) &&
			isset($post['subject-empty']) && empty($post['subject-empty']));

			if ($notRobot) {
				$user = $this->global['user'];

				if ($user) {
					$user['author'] = $user['display_name'];
				}

				$post['content'] = sanitizeHTML($post['content']);

				$sql       = new \Vvveb\Sql\CommentSQL();
				$comment   = array_merge($post, $user, ['date_added' => date('Y-m-d H:i:s'), 'status' => 0]);
				$result    = $sql->add(['comment' => $comment]);

				if ($result['comment']) {
					$comment['comment_id'] = $result['comment'];

					$comments                                           = $this->session->get('comments', []);
					$comments[$comment['slug']][$comment['comment_id']] = $comment;
					$this->session->set('comments', $comments);

					$this->view->success[] = __('Comment was posted!');
				} else {
					$this->view->errors[] = __('Error adding comment!');
				}
			}
		}

		return $result;
	}

	function addComment() {
		return $this->index();
		//$result = $this->insertComment();
		//$this->response->setType('json');
		//$this->response->output($result);

		//return false;
	}

	function index() {
		//check if post component is loaded for the page,
		//if not then post does not exist or post component is not added/configured on the page
		/*
		$post = $this->view->post[0] ?? [];
		$component = Component::getInstance();
		$comp = $component->getComponent('post');
		if (!$post) {
			//$this->notFound();
		}*/

		if (isset($this->request->post['content'])) {
			$result = $this->insertComment();
		}

		$slug = $this->request->get['slug'] ?? '';

		if ($slug) {
			$postSql = new PostSQL();
			$options = $this->global + ['slug' => $slug, 'type' => $this->type];
			$post    = $postSql->get($options);

			if ($post) {
				$this->request->get['post_id']     = $post['post_id'];
				$this->request->request['post_id'] = $post['post_id'];
				$this->request->request['name']    = $post['name'];

				if (isset($post['template']) && $post['template']) {
					$this->view->template($post['template']);
					//force post template if a different html template is selected
					$this->view->tplFile('content/post.tpl');
				}
			} else {
				$message = __('Post not found!');
				$this->notFound(true, ['message' => $message, 'title' => $message]);
			}

			$this->view->post = $post;
		}
	}
}
