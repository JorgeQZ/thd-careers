<?php
/*
==================
    Ajax Search
======================
*/


// add the ajax fetch js
add_action( 'wp_footer', 'ajax_fetch' );
function ajax_fetch() {
?>
<script type="text/javascript">
function fetch(){


    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        data: { action: 'data_fetch', keyword: jQuery('#inputSearch').val() },
        success: function(data) {
			jQuery('#contenedor-resultados').html(data);
		}
    });

}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/2.4.1/purify.min.js"></script>

<?php
}


// the ajax function
add_action('wp_ajax_data_fetch' , 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch','data_fetch');
function data_fetch(){

	/* --------------------------------- */

		echo '<div class="cont-result">';
			//$the_query = new WP_Query( array( 'posts_per_page' => -1, 's' => esc_attr( $_POST['keyword'] ), 'post_type' => array('vacantes') ) );

			// Buscar en los títulos
			$titulo_query = new WP_Query( array(
				'posts_per_page' => -1,
				'post_type' => 'vacantes',
				'post_status' => 'publish',
				's' => esc_attr( $_POST['keyword'] ),
			) );


			// Buscar en los campos personalizados
			$customfields_query = new WP_Query( array(
				'posts_per_page' => -1,
				'post_type' => 'vacantes',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key'     => 'codigo_de_vacante',
						'value'   => esc_attr( $_POST['keyword'] ),
						'compare' => 'LIKE'
					),
				),
			) );


			// Buscar en las taxonomias
			$keyword = sanitize_text_field($_POST['keyword']);
			$terms = get_terms(array(
				'taxonomy'   => 'categorias_vacantes',
				'hide_empty' => false,
				'search'     => $keyword,
			));

			$term_slugs = wp_list_pluck($terms, 'slug');

			$taxonomies_query = new WP_Query(array(
				'posts_per_page' => -1,
				'post_type'      => 'vacantes',
				'post_status' => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => 'categorias_vacantes',
						'field'    => 'slug',
						'terms'    => $term_slugs,
						'operator' => 'IN',
					),
				),
			));

/*
	echo '<div class="cont-result">';
				if( $query->have_posts() ) :
					echo '<ul>';
					while( $query->have_posts() ): $query->the_post(); ?>

						<li>
							<a href="<?php echo esc_url( post_permalink() ); ?>">
								<?php
									the_title();
								?>
							</a>
						</li>

					<?php endwhile;
					echo '</ul>';
					wp_reset_postdata();
				endif;
			echo '</div>';
*/






			// Ahora puedes usar $combined_results para mostrar los posts

			/*$the_query = new WP_Query( array(
				'posts_per_page' => -1,
				'post_type' => 'vacantes',
				'meta_query' => array(
					'relation' => 'OR', // Relación OR para combinar
					// Búsqueda en un campo personalizado específico
					array(
						'key' => 'ubicacion', // Cambia al nombre de tu custom field
						'value' => esc_attr( $_POST['keyword'] ),
						'compare' => 'LIKE',
					),
					// Búsqueda en otro campo personalizado (si tienes más campos)
					array(
						'key' => 'descripcion', // Otro campo personalizado
						'value' => esc_attr( $_POST['keyword'] ),
						'compare' => 'LIKE',
					),
				),
			) );
			*/


			if ($titulo_query->have_posts() || $customfields_query->have_posts() || $taxonomies_query->have_posts()) {

				$merged_posts = array_merge($titulo_query->posts, $customfields_query->posts, $taxonomies_query->posts);

				foreach ($merged_posts as $post) {
					setup_postdata($post);
					$ubicacion = get_field("ubicacion", $post);
					?>
						<div class="resultado">
							<a href="<?php echo esc_url( post_permalink($post) ); ?>">
								<h2><?php echo get_the_title($post); ?></h2>
								<span><?php echo $ubicacion['label']; ?></span>
							</a>
						</div>
					<?php
				}

				wp_reset_postdata();
			} else {
				?>
					<div class="resultado">
						<p>No se encontraron resultados.</p>
					</div>
				<?php
			}

		echo '</div>';

    die();

}

