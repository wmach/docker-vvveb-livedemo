//set selector prefix to have shorter and easier to read selectors for rules
@posts = [data-v-component-posts]
@post  = [data-v-component-posts] [data-v-post]

//editor info
@post|data-v-id = $post['post_id']
@post|data-v-type = 'post'

//search
@posts [data-v-search] = $posts['search']

@posts|prepend = <?php
	if (isset($_posts_idx)) $_posts_idx++; else $_posts_idx = 0;
	$previous_component = isset($current_component)?$current_component:null;
	$posts = $current_component = $this->_component['posts'][$_posts_idx] ?? [];

	$count = $posts['count'] ?? 0;
	$limit = isset($posts['limit']) ? $posts['limit'] : 5;
?>

@post|deleteAllButFirstChild

@post|before = <?php 
//if no posts available and page is loaded in editor then set an empty post to show post content for the editor
$_default = (isset($vvveb_is_page_edit) && $vvveb_is_page_edit ) ? [0 => []] : [];
$_default = [0 => []];
$_posts = empty($posts['posts']) ? $_default : $posts['posts'];
//$pagination = $this->posts[$_posts_idx]['pagination'];
$count = 0;
foreach ($_posts as $index => $post) {?>

	//editor attributes

	//@post [data-v-post-excerpt] = $post['excerpt']

    //catch all data attributes
    @post [data-v-post-*]|innerText = $post['@@__data-v-post-(*)__@@']
    @post img[data-v-post-*]|src = $post['@@__data-v-post-(*)__@@']
	

	//@post [data-v-post-img]|src = <?php echo $post['images'][0] ?? '';?>
	
	@post [data-v-post-url-text]  = $post['url']	
	@post a[data-v-post-*]|href   = $post['@@__data-v-post-(*)__@@']	
	@post [data-v-post-url]|title = $post['title']	
	
	@post [data-v-post-content] = <?php if (isset($post['content'])) echo $post['content'];?>
	@post [data-v-post-excerpt] = <?php if (isset($post['excerpt'])) echo $post['excerpt'];?>
	
	@post|after = <?php 
	$count++;
}

$current_component = $previous_component;
?>
