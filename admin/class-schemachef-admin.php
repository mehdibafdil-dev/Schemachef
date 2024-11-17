<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/mehdibafdil-dev
 * @since      1.0.0
 *
 * @package    Schemachef
 * @subpackage Schemachef/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Schemachef
 * @subpackage Schemachef/includes
 * @author     Mehdi BAFDIL <mehdibafdil@gmail.com>
 */
class Schemachef_Admin
{
	const RECIPE_SCHEMA_PREFIX = '_recipe_';
	const SCHEMACHEF_PREFIX = '_schemachef_';
	const ICON_URL = SCHEMACHEF_ADMIN_ASSETS_PATH . '/img/vpsc_icon.svg';

	private $plugin_name;
	private $version;
	private $title;
	private $fields;
	private $loader;

	public function __construct($plugin_name, $version, $title)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->title = $title;
		$this->loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');

		$this->fields = [
			'enabled' => 'Enable Recipe Schema',
			'name' => 'Recipe Name',
			'description' => 'Description',
			'prep_time' => 'Prep Time',
			'cook_time' => 'Cook Time',
			'total_time' => 'Total Time',
			'keywords' => 'Keywords',
			'category' => 'Recipe Category',
			'calories' => 'Calories',
			'yield' => 'yield',
			'ingredient' => 'Ingredient',
			'rating' => 'Aggregate Rating (Stars)',
		];
	}

	public function register_admin_menu()
	{
		add_menu_page(
			'Schemachef',
			'Schemachef',
			'manage_options',
			'schemachef-admin',
			array($this, 'plugin_settings_page'),
			self::ICON_URL,
			25
		);
	}

	public function enqueue_bootstrap()
	{
		wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
		wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2', true);
		wp_enqueue_style('bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
	}

	public function plugin_settings_page()
	{
		$plugin_data = get_plugin_data(plugin_dir_path(__FILE__) . '../schemachef.php');

		$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates'); // Change the path to your template directory
		$twig = new \Twig\Environment($loader);
		$template = $twig->load('admin-page.twig');
		$general_enable = get_option(self::SCHEMACHEF_PREFIX . 'general_enable', 0);
		$default_recipe_name = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_name', '');
		$default_recipe_description = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_description', '');
		$default_recipe_prep_time = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_prep_time', '');
		$default_recipe_cook_time = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_cook_time', '');
		$default_recipe_total_time = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_total_time', '');
		$default_recipe_keywords = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_keywords', '');
		$default_recipe_category = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_category', '');
		$default_recipe_calories = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_calories', '');
		$default_recipe_yield = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_yield', '');
		$default_recipe_ingredient = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_ingredient', '');
		$default_recipe_rating = get_option(self::SCHEMACHEF_PREFIX . 'default_recipe_rating', '');
		$default_thumbnail_recipelist_url = plugins_url('/img/default-thumbnail-recipe-list.png', __FILE__);

		$recipes = get_posts([
			'post_type' => 'post',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key' => self::RECIPE_SCHEMA_PREFIX . 'enabled',
					'compare' => 'EXISTS',
				],
				[
					'key' => self::RECIPE_SCHEMA_PREFIX . 'name',
					'compare' => 'EXISTS',
				],
			],
		]);

		$recipe_data = [];

		foreach ($recipes as $recipe) {
			$recipe_id = $recipe->ID;
			$image_url = $this->get_featured_image_url($recipe_id);
			$recipe_data[$recipe_id] = [
				'image_url' => $image_url,
				'edit_link' => get_edit_post_link($recipe_id),
				'title' => get_the_title($recipe_id),
				'name' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'name', true),
				'description' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'description', true),
				'prep_time' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'prep_time', true),
				'cook_time' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'cook_time', true),
				'total_time' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'total_time', true),
				'keywords' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'keywords', true),
				'category' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'category', true),
				'calories' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'calories', true),
				'yield' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'yield', true),
				'ingredient' => get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'ingredient', true),
				'rating' => intval(get_post_meta($recipe_id, self::RECIPE_SCHEMA_PREFIX . 'rating', true)),
				'content_author' => get_the_author_meta('display_name', $recipe->post_author), // Changed 'author_display_name' to 'content_author'
			];
		}

		$template_data = [
			'SCHEMACHEF_ADMIN_ASSETS_PATH' => SCHEMACHEF_ADMIN_ASSETS_PATH,
			'SCHEMACHEF_PREFIX' => self::SCHEMACHEF_PREFIX,
			'plugin_data' => $plugin_data,
			'general_enable' => $general_enable,
			'default_recipe_name' => $default_recipe_name,
			'default_recipe_description' => $default_recipe_description,
			'default_recipe_prep_time' => $default_recipe_prep_time,
			'default_recipe_cook_time' => $default_recipe_cook_time,
			'default_recipe_total_time' => $default_recipe_total_time,
			'default_recipe_keywords' => $default_recipe_keywords,
			'default_recipe_category' => $default_recipe_category,
			'default_recipe_calories' => $default_recipe_calories,
			'default_recipe_yield' => $default_recipe_yield,
			'default_recipe_ingredient' => $default_recipe_ingredient,
			'default_recipe_rating' => $default_recipe_rating,
			'recipe_data' => $recipe_data,
			'recipes' => $recipes,
			'default_thumbnail_recipelist_url' => $default_thumbnail_recipelist_url
		];

		echo $template->render($template_data);
	}

	function get_featured_image_url($post_id)
	{
		if (has_post_thumbnail($post_id)) {
			$image_url = get_the_post_thumbnail_url($post_id, 'thumbnail');
			return $image_url;
		} else {
			return '';
		}
	}


	public function handle_recipe_options_form_submission()
	{

		if (isset($_POST['action']) && $_POST['action'] === 'save_recipe_general_settings') {

			if (isset($_POST['formData'])) {

				parse_str($_POST['formData'], $form_data);

				if (isset($form_data['general_enable'])) {
					$general_enable = intval($form_data['general_enable']);
					update_option(self::SCHEMACHEF_PREFIX . 'general_enable', $general_enable);
				} else {
					update_option(self::SCHEMACHEF_PREFIX . 'general_enable', 0);
				}
			}
		}

		if (isset($_POST['action']) && $_POST['action'] === 'save_recipe_options') {

			if (isset($_POST['formData'])) {
				// Parse the form data
				parse_str($_POST['formData'], $form_data);

				if (isset($form_data['default_recipe_name'])) {
					$value = sanitize_text_field($form_data['default_recipe_name']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_name', $value);
				}

				if (isset($form_data['default_recipe_description'])) {
					$value = sanitize_text_field($form_data['default_recipe_description']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_description', $value);
				}

				if (isset($form_data['default_recipe_prep_time'])) {
					$value = sanitize_text_field($form_data['default_recipe_prep_time']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_prep_time', $value);
				}

				if (isset($form_data['default_recipe_cook_time'])) {
					$value = sanitize_text_field($form_data['default_recipe_cook_time']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_cook_time', $value);
				}

				if (isset($form_data['default_recipe_total_time'])) {
					$value = sanitize_text_field($form_data['default_recipe_total_time']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_total_time', $value);
				}

				if (isset($form_data['default_recipe_keywords'])) {
					$value = sanitize_text_field($form_data['default_recipe_keywords']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_keywords', $value);
				}

				if (isset($form_data['default_recipe_category'])) {
					$value = sanitize_text_field($form_data['default_recipe_category']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_category', $value);
				}

				if (isset($form_data['default_recipe_calories'])) {
					$value = sanitize_text_field($form_data['default_recipe_calories']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_calories', $value);
				}

				if (isset($form_data['default_recipe_yield'])) {
					$value = sanitize_text_field($form_data['default_recipe_yield']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_yield', $value);
				}

				if (isset($form_data['default_recipe_ingredient'])) {
					$value = sanitize_text_field($form_data['default_recipe_ingredient']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_ingredient', $value);
				}

				if (isset($form_data['default_recipe_rating'])) {
					$value = intval($form_data['default_recipe_rating']);
					update_option(self::SCHEMACHEF_PREFIX . 'default_recipe_rating', $value);
				}

				return true;
				exit();
			}
		}
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/schemachef-admin.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-iziToast', plugin_dir_url(__FILE__) . 'css/iziToast.min.css', array(), $this->version, 'all');
		wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
		wp_enqueue_style('font-awesome', plugin_dir_url(__FILE__) . 'css/fontawesome.min.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/schemachef-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . '-iziToast', plugin_dir_url(__FILE__) . 'js/iziToast.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/fontawesome.min.js', array('jquery'), $this->version, false);
	}

	public function enqueue_custom_js()
	{
		wp_enqueue_script('schemachef-admin', plugin_dir_url(__FILE__) . 'js/schemachef-admin.js', array('jquery'), '1.0', true);
		wp_localize_script('schemachef-admin', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
		wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), '1.11.10');
	}

	public function add_recipe_schema_metabox()
	{
		add_meta_box('recipe_schema', $this->title, array($this, 'recipe_schema_callback'), 'post', 'normal', 'high');
	}

	public function recipe_schema_callback($post)
	{
		echo '<div class="recipe-schema-metabox">';
		echo '<table>';

		foreach ($this->fields as $field_key => $field_label) {
			$field_value = get_post_meta($post->ID, self::RECIPE_SCHEMA_PREFIX . $field_key, true);

			echo '<tr>';
			echo '<td class="labeltd"><label for="' . $field_key . '">' . $field_label . ':</label></td>';
			echo '<td>';
			if ($field_key === 'rating') {
				$this->render_star_rating_field($field_key, $field_value);
			} else {
				$this->render_field_input($field_key, $field_value);
			}
			echo '</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>';
	}

	private function render_star_rating_field($field_key, $field_value)
	{
		echo '<td>';
		echo '<div class="star-rating">';
		for ($i = 5; $i >= 1; $i--) {
			$checked = ($field_value == $i) ? 'checked' : '';
			echo '<input type="radio" id="' . $field_key . '_' . $i . '" name="' . $field_key . '" value="' . $i . '" ' . $checked . '/>';
			echo '<label for="' . $field_key . '_' . $i . '"></label>';
		}
		echo '</div>';
		echo '</td>';
	}

	private function render_field_input($field_key, $field_value)
	{
		$placeholder_attr = !empty($field_info['placeholder']) ? 'placeholder="' . esc_attr($field_info['placeholder']) . '"' : '';

		switch ($field_key) {
			case 'description':
				echo '<td><textarea id="' . $field_key . '" name="' . $field_key . '" ' . $placeholder_attr . '>' . esc_textarea($field_value) . '</textarea></td>';
				break;
			case 'calories':
			case 'name':
			case 'category':
			case 'ingredient':
			case 'keywords':
			case 'total_time':
			case 'prep_time':
			case 'cook_time':
			case 'yield':
				echo '<td><input type="text" id="' . $field_key . '" name="' . $field_key . '" ' . $placeholder_attr . ' value="' . esc_attr($field_value) . '" /></td>';
				break;
			case 'enabled':
				echo '<td><input type="checkbox" id="' . $field_key . '" name="' . $field_key . '" value="1" ' . checked(1, $field_value, false) . ' /></td>';
				break;
			default:
				break;
		}
	}

	public function save_recipe_schema_data($post_id)
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (isset($_POST['name']) && $_POST['name'] != '') {
			foreach ($this->fields as $field_key => $field_label) {
				if (isset($_POST[$field_key])) {
					$field_value = sanitize_text_field($_POST[$field_key]);
					update_post_meta($post_id, self::RECIPE_SCHEMA_PREFIX . $field_key, $field_value);
				}
			}

			$recipe_schema_enabled = isset($_POST['enabled']) ? 1 : 0;
			update_post_meta($post_id, self::RECIPE_SCHEMA_PREFIX . 'enabled', $recipe_schema_enabled);
		}
	}

	public function add_recipe_schema_to_content($content)
	{
		global $post;

		$recipe_schema_general_enabled = get_option(self::SCHEMACHEF_PREFIX . 'general_enable', 0);
		$recipe_schema_enabled = get_post_meta($post->ID, self::RECIPE_SCHEMA_PREFIX . 'enabled', true);

		if ($recipe_schema_general_enabled && $recipe_schema_enabled) {
			$recipe_schema = $this->generate_recipe_schema();
			$content .= $recipe_schema;
		}

		return $content;
	}

	public function generate_recipe_schema()
	{
		$post_id = get_the_ID();
		$post_date = get_the_time('Y-m-d', $post_id);

		$recipe_data = array();
		$fields = $this->fields;

		foreach ($fields as $field_key => $field_label) {
			$field_value = $this->get_field_value($field_key, $post_id);
			$recipe_data[$field_key] = $field_value;
		}

		// Get the featured image URL
		$recipe_image = get_the_post_thumbnail_url($post_id);

		$author_id = get_post_field('post_author', $post_id);
		$author_data = get_userdata($author_id);

		// Construct the JSON-LD markup
		$markup = array(
			"@context" => "https://schema.org/",
			"@type" => "Recipe",
			"name" => $recipe_data['name'],
			"image" => is_array($recipe_image) ? $recipe_image : [$recipe_image], // Ensure image is an array
			"author" => array(
				"@type" => "Person",
				"name" => $author_data->display_name,
			),
			"datePublished" => $post_date,
			"description" => $recipe_data['description'],
			"prepTime" => $recipe_data['prep_time'],
			"cookTime" => $recipe_data['cook_time'],
			"totalTime" => $recipe_data['total_time'],
			"keywords" => $recipe_data['keywords'],
			"recipeCategory" => $recipe_data['category'],
			"recipeYield" => $recipe_data['yield'],
			"nutrition" => array(
				"@type" => "NutritionInformation",
				"calories" => $recipe_data['calories'] . " calories",
			),
			"recipeIngredient" => $recipe_data['ingredient'],
			"aggregateRating" => array(
				"@type" => "AggregateRating",
				"ratingValue" => $recipe_data['rating'],
				"ratingCount" => "5",
			),
		);

		return '<script type="application/ld+json">' . json_encode($markup, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
	}

	private function get_field_value($field_key, $post_id)
	{
		$meta_key = self::RECIPE_SCHEMA_PREFIX . $field_key;
		$default_option_key = self::SCHEMACHEF_PREFIX . 'default_recipe_' . $field_key;

		$field_value = get_post_meta($post_id, $meta_key, true);

		if ($field_value === '') {
			$default_value = get_option($default_option_key, '');
			$field_value = ($default_value !== '') ? $default_value : '';
		}

		return $field_value;
	}

	function delete_recipe_callback()
	{
		if (is_user_logged_in() && current_user_can("delete_posts")) {
			$recipeID = intval($_POST["recipe_id"]);

			if (get_post_type($recipeID) === "post") {
				$meta_keys_to_delete = array(
					self::RECIPE_SCHEMA_PREFIX . 'enabled',
					self::RECIPE_SCHEMA_PREFIX . 'name',
					self::RECIPE_SCHEMA_PREFIX . 'description',
					self::RECIPE_SCHEMA_PREFIX . 'prep_time',
					self::RECIPE_SCHEMA_PREFIX . 'cook_time',
					self::RECIPE_SCHEMA_PREFIX . 'total_time',
					self::RECIPE_SCHEMA_PREFIX . 'keywords',
					self::RECIPE_SCHEMA_PREFIX . 'category',
					self::RECIPE_SCHEMA_PREFIX . 'calories',
					self::RECIPE_SCHEMA_PREFIX . 'yield',
					self::RECIPE_SCHEMA_PREFIX . 'ingredient',
					self::RECIPE_SCHEMA_PREFIX . 'rating',
				);

				foreach ($meta_keys_to_delete as $meta_key) {
					delete_post_meta($recipeID, $meta_key);
				}

				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}
}
