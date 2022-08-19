<?php
/**
 * Torno Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Torno Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_TORNO_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'torno-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_TORNO_CHILD_VERSION, 'all' );

}

/**
 * Enqueue scripts
 */
function child_enqueue_scripts() {
	wp_enqueue_script ('torno-child-main.js', get_theme_file_uri('/torno-child-main.js'), NULL, '1.0', true); 

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );
add_action( 'wp_enqueue_scripts', 'child_enqueue_scripts', 15 );

/**
 * Add shortcodes
 *
 */
//  Display related products in sigle post page of different course types
function display_related_product_collection() { 
	ob_start(); 
	?> 
	<div class="torno-products-container torno-grid-row">
		<?php
			$post_type = get_post_type();
			if ($post_type == 'school_course') {
				$relatedProducts = get_field('related_school_course');
			} elseif ($post_type == 'college_courses') {
				$relatedProducts = get_field('related_college_course');
			} elseif ($post_type == 'study_abroad') {
				$relatedProducts = get_field('related_study_abroad');
			}

			foreach($relatedProducts as $product) {
				?>
			<div class="torno-grid-col col-sm-6">
				<div class="torno-product-box">
					<?php 
						$_product = wc_get_product( $product->ID );
						$image_id  = get_post_thumbnail_id($product->ID, 'woocommerce_gallery_thumbnail');
						$image_url = wp_get_attachment_image_url( $image_id, 'full' ); 
					?>
					<div  class="torno-product-image-box">
						<img src="<?php echo $image_url ?>" alt="<?php echo $product->post_title ?>">
					</div>
					<div class="torno-product-detail-box">
						<div class="torno-product-detail-box--a">
							<?php $terms = get_the_terms( $product->ID, 'product_cat' ); ?>
							<p><?php echo $terms[0]->name; ?></p>
							<h5><?php echo $product->post_title ?></h5>
						</div>
						<div class="torno-product-detail-box--b">
							<p id ="relp-regular-price"><?php echo $_product->get_regular_price(); ?></p>
							<p id ="relp-sale-price"><?php echo $_product->get_sale_price(); ?></p>
							<!-- <a onclick="readMoreFunction(this)" id="readMoreBtn" class="readMoreBtn">read more...</a> -->
							<!-- <p id ="pdPara">
								<?php //echo $product->post_excerpt; ?>
							</p> -->
							<a href="<?php echo $_product->add_to_cart_url(); ?>" data-quantity="1" class="torno-add-to-cart-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo $product->ID; ?>" data-product_sku="" aria-label="Add “GMAT Complete Course” to your cart" rel="nofollow">Add to cart</a>
						</div>
					</div>
				</div>
			</div>
			<?php } wp_reset_postdata();?>
		</div>
		<?php
	return ob_get_clean();
}

// Course category icon boxes in courses page
function display_course_category_icon_boxes( $atts = []) { 
	ob_start(); 
	
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
	
	$course_type = esc_html__($atts['type'], 'torno');
	
	if ($course_type == 'school_course') {
		$parent_ID = 64;
	} elseif ($course_type == 'college_courses') {
		$parent_ID = 63;
	} elseif ($course_type == 'study_abroad') {
		$parent_ID = 65;
	}

	?>
	<div class="torno-course-listing">
					
		<?php
				$args = array(
							'taxonomy' => 'product_cat',
							'orderby' => 'name',
							'order'   => 'ASC',
								'parent' => $parent_ID
						);

				$cats = get_categories($args);

				foreach($cats as $cat) {
				?>
					<a class="torno-course-bundle-container" href="<?php echo get_category_link( $cat->term_id ) ?>">
							<div>	
								<h5>
									<?php echo $cat->name; ?>
								</h5>
								<p>
									<?php $a = $cat->category_count; ?>
									<?php echo $a." Product"; ?>
								</p>
							</div>
							<div>
								<?php 
									$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
									$size = 
									$image = wp_get_attachment_image_src( $thumbnail_id );
								?>
								<p style="height:46px; width:46px; font-size:46px; color: #818a91;" ><img src="<?php echo $image[0] ?>"></p>
							</div>
					</a>
				<?php
			} wp_reset_postdata();
		?>
	</div>

	<?php
	return ob_get_clean();
}

// display list of course buttons
function list_the_courses_as_buttons($atts =[]) { 
	ob_start(); 

	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	$course_type = esc_html__($atts['type'], 'torno');

	?>
	<html>
		<div class="torno-course-button-container">

			 <?php 
			$schoolCourses = new WP_Query(array(
				'post_type' => $course_type 
			));

			while($schoolCourses->have_posts()) {
				$schoolCourses->the_post(); ?>

				<a href="<?php the_permalink(); ?>" class="course-button course-button-color"><?php the_title(); ?></a> 
			<?php } wp_reset_postdata(); 
			 ?>
	   </div>
	</html>
	<?php
	return ob_get_clean();
}

// Show list of speakers in open session card
function photo_gallery_shortcode() { 
	ob_start(); 
		
	?>
	<div class="gallery">
		<?php
		
				$relatedSpeakers = get_field('speakers');
				if ($relatedSpeakers) {
					foreach ($relatedSpeakers as $speaker) {
						setup_postdata($speaker);
		?>
						<div class="wrapper">
							<img src="<?php echo get_field('speaker_image', $speaker->ID)["url"]; ?>" alt="<?php echo get_field('speaker_image', $speaker->ID)["url"]; ?>">
							<div class="desc"><?php echo get_field('speaker_qualification', $speaker->ID); ?></div>
						</div>
		<?php
					}
				} wp_reset_postdata(); 
		?>
	</div><?php
	return ob_get_clean();
}

// About us page shortcode
function about_us_shortcode() { 
	ob_start(); ?>
		<html>
		<div class="info">
			<div class="naccs">
				<div class="grid">
					<div class="gc gc--1-of-3">
						<div class="menu">
							<div class="active"><span class="light"></span><span>About Us</span></div>
							<ul class="nacc">
								<li class="collapse">
									<div id="for_scroll">
										<h3>About Us</h3>
										<h4></h4>
										<p class="mission"> "Our mission is to establish and maintain a technology integrated
											learning environment designed to reach and engage the learner – anyone, anywhere,
											anytime. We ensure adequate support, training, development, and systems are in place
											to provide students with the resources, and information necessary for an effective
											learning environment. We achieve our mission by" </p>
									</div>
								</li>
							</ul>
							<div><span class="light"></span><span>Mission</span></div>
							<ul class="nacc">
								<li class="collapse">
									<div id="for_scroll">
										<h3>Mission</h3>
										<h4></h4>
										<p class="mission"> "Our mission is to establish and maintain a technology integrated
											learning environment designed to reach and engage the learner – anyone, anywhere,
											anytime. We ensure adequate support, training, development, and systems are in place
											to provide students with the resources, and information necessary for an effective
											learning environment. We achieve our mission by" </p>
										<div class="torno-highlight-block-container">
											<p>Offering high quality practice oriented learning and teaching</p>
											<p>To assist our students to work towards a single goal of serving the society
												better</p>
											<p>The efficiency of our methodologies and systems</p>
										</div>
									</div>
								</li>
							</ul>
							<div><span class="light"></span><span>Vision</span></div>
							<ul class="nacc">
								<li class="collapse">
									<div>
										<h3>Vision</h3>
										<h4></h4>
										<p class="vision">Our vision is to provide excellent educational opportunities that are responsive to the needs of society and supporting learning through technology to positively impact the lives of our students. We enable students to realize their potential and make their career dreams come true.</p>
									</div>
								</li>
							</ul>
							<div><span class="light"></span><span>Team</span></div>
							<ul class="nacc">
								<li class="collapse">
									<div>
										<h3>Team</h3>
										<h5>Meet our Awesome Team. When your mission is to be better, faster and smarter, you need the best people driving your vision forward. </h5> <p> A dedicated and passionate team is the back bone of our institution.</p>
										<div class="member-container">
											<?php
									$staffDetails = new WP_Query(array(
										'post_type' => 'staff_details',
										'posts_per_page' => 6
									));

									while($staffDetails->have_posts()) {
										$staffDetails->the_post();	

										$image = get_field('staff_image');
										
								?>
											<div class="member">
												<img src="<?php echo esc_url($image['url']); ?>"
													alt="<?php echo esc_attr($image['alt']); ?>">
												<h2>
													<?php the_title(); ?>
												</h2>
												<p id="typeset" class="who">
													<?php the_field('designation') ?>
												</p>
												<p class="member-text"></p>

												<div class="soc-icons">
													<!--	<a href="https://twitter.com">&#xf099;</a> -->
													<a href="<?php echo get_field('linkedin')?>">&#xf0e1;</a>
													<!--	<a href="https://github.com/">&#xf09b;</a> -->
													<!--	<a href="https://plus.google.com">&#xf0d5;</a> -->
												</div>
											</div>
											<?php } wp_reset_postdata(); ?>
										</div>
									</div>
								</li>
							</ul>
							<div><span class="light"></span><span>Media</span></div>
							<ul class="nacc">
								<li class="collapse">
									<div>
										<h3>Media</h3>
										<h5>Catch up on the latest news coverage, awards,industry accolades and press releases.</h5>
										
										<?php
							$mediaUpdates = new WP_Query(array(
								'post_type' => 'medias',
								//'posts_per_page' => 3,
								'paged' => get_query_var('paged', 1),
								'order' => 'DESC'
							));
			
							while($mediaUpdates->have_posts()) {
								$mediaUpdates->the_post();
								$image = get_field('image');
								$site_url = get_field('linkedin')
						?>
										<div id="media" class="media blog">
											<div class="post group">
												<!--<h4 class="post__title">
													<?php //the_title(); ?>
												</h4>-->
												<div class="post__date">
													<span class="post__date__mo">
														<?php echo get_the_time('M' ); ?>
													</span>
													<span class="post__date__day">
														<?php echo get_the_time('d'); ?>
													</span>
													<span class="post__date__yr">
														<?php echo get_the_time('Y'); ?>
													</span>
												</div>
												<?php 
									$post = get_post();
									$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); 
								?>
												<img src=<?php echo esc_url($image['url']); ?> alt="<?php echo esc_attr($image['alt'])?>" class="post__image" />
												<p class="post__content">
													<?php the_title();//the_field('news'); ?>
												
												<a href="<?php echo the_field('external_url');?>" target="_blank" rel="noopener noreferrer"><h6 style="text-align:right; padding-top:5px; margin-bottom: 0px;">Read Now</h6></a></p>
											</div>
										</div>
										<?php } wp_reset_postdata(); ?>
									</div>
									<?php echo paginate_links(array('total' => $pastEvents->max_num_pages));?>
								</li>
							</ul>
							<div><span class="light"></span><span>Testimonials</span></div>
							<ul class="nacc">
								<li class="collapse">
									<div>
										<h3>Testimonials</h3>
										<h5>Every year we get hundreds of emails and google reviews from our satisfied students. Below are just some of the great feedback we are so happy to receive.</h5> 
										<p>Please feel free to submit your own feedback to Prep Academy; we always take into account the views of our students, to make sure we are delivering the highest possible quality educational experience.</p>
										<div style="margin: 0 auto; padding-bottom: 80px;">
											<?php
							$testimonials = new WP_Query(array(
								'post_type' => 'student_testimonials',
								'posts_per_page' => 3
							));
			
							$count = 1;
							while($testimonials->have_posts()) {
								$testimonials->the_post();
								$image = get_field('student_image');
					?>
											<div <?php if($count%2==0) {echo 'class="testimonial-quote group"' ;} else
												{echo 'class="testimonial-quote group right"' ;}?>>
												<?php 
								$post = get_post();
								$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); 
					?>
												<img src="<?php echo esc_url($image['url']); ?>">
												<div class="quote-container">
													<blockquote>
														<p>
															<?php the_field('testimony'); ?>
														</p>
													</blockquote>
													<cite><span>
															<?php the_field('student_name'); ?>
														</span><br>
														<?php the_field('designation'); ?><br>
														<?php the_field('institute'); ?>
													</cite>
												</div>
											</div>
											<?php 
						$count++;
				}  wp_reset_postdata(); ?>
										</div>
									</div>
								</li>
							</ul>


							<?php /* Next we define the elements of desktop design */ ?>

						</div>
					</div>
					<div class="gc gc--2-of-3">
						<ul class="nacc second">
							<li class="active">
								<div>
									<h3>About Us</h3>
									<h4></h4>
										<p class="mission">Our mission is to establish and maintain a technology integrated learning
											environment designed to reach and engage the learner – anyone, anywhere, anytime. We
											ensure adequate support, training, development, and systems are in place to provide
											students with the resources, and information necessary for an effective learning
											environment. We achieve our mission by :</p>
								</div>
							</li>
							<li>
								<div>
									<h3>Mission</h3>
									<h4></h4>
										<p class="mission">Our mission is to establish and maintain a technology integrated learning
											environment designed to reach and engage the learner – anyone, anywhere, anytime. We
											ensure adequate support, training, development, and systems are in place to provide
											students with the resources, and information necessary for an effective learning
											environment. We achieve our mission by :</p>
									<div class="torno-highlight-block-container">
										<p>Offering high quality practice oriented learning and teaching</p>
										<p>To assist our students to work towards a single goal of serving the society better
										</p>
										<p>The efficiency of our methodologies and systems</p>
									</div>
								</div>
							</li>

							<li>
								<div>
									<h3>Vision</h3>
									<h4></h4>
									<p class="vision">Our vision is to provide excellent educational opportunities that are responsive to the needs of society and supporting learning through technology to positively impact the lives of our students. We enable students to realize their potential and make their career dreams come true.</p>
								</div>
							</li>
							<li>
								<div>
									<h3>Team</h3>
									<h5>Meet our Awesome Team. When your mission is to be better, faster and smarter, you need the best people driving your vision forward. </h5> <p> A dedicated and passionate team is the back bone of our institution.</p>
									<div class="member-container">
										<?php
								$staffDetails = new WP_Query(array(
									'post_type' => 'staff_details',
									'posts_per_page' => 6
								));

								while($staffDetails->have_posts()) {
									$staffDetails->the_post();	

									$image = get_field('staff_image');
							?>
										<div class="member">
											<img src="<?php echo esc_url($image['url']); ?>"
												alt="<?php echo esc_attr($image['alt']); ?>">
											<h2>
												<?php the_title(); ?>
											</h2>
											<p id="typeset" class="who">
												<?php the_field('designation') ?>
											</p>
											<p class="member-text"></p>

											<div class="soc-icons">
												<!--	<a href="https://twitter.com">&#xf099;</a> -->
												<a href="<?php echo get_field('linkedin')?>">&#xf0e1;</a>
												<!--	<a href="https://github.com/">&#xf09b;</a> -->
												<!--	<a href="https://plus.google.com">&#xf0d5;</a> -->
											</div>
										</div>
										<?php } wp_reset_postdata(); ?>
									</div>
								</div>
							</li>

							<li>
								<div>
									<h3>Media</h3>
									<h5>Catch up on the latest news coverage, awards, industry accolades and press releases.</h5>
									

									<?php
						$mediaUpdates = new WP_Query(array(
							'post_type' => 'medias',
							//'posts_per_page' => 3
							'paged' => get_query_var('paged', 1),
							'order' => 'DESC'
						));

						while($mediaUpdates->have_posts()) {
							$mediaUpdates->the_post();
							$image = get_field('image');
					?>
									<div id="media" class="media blog">
										<div class="post group">
											<!--<h4 class="post__title">
												<?php //the_title(); ?>
											</h4>-->
											<div class="post__date">
												<span class="post__date__mo">
													<?php echo get_the_time('M' ); ?>
												</span>
												<span class="post__date__day">
													<?php echo get_the_time('d'); ?>
												</span>
												<span class="post__date__yr">
													<?php echo get_the_time('Y'); ?>
												</span>
											</div>
											<?php 
								$post = get_post();
								$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); 
							?>
											<img src=<?php echo esc_url($image['url']); ?> alt="<?php echo esc_attr($image['alt']);?>" class="post__image" />
											<p class="post__content">
											<?php the_title();//the_field('news'); ?>
											
											<?php //if ( get_field(['external_url'])): ?>
												<a href="<?php echo the_field('external_url');?>" target="_blank" rel="noopener noreferrer"><h6 style="text-align:right; padding-top:5px; margin-bottom:0px;">Read Now</h6></a></p>
											<?php //endif ?>
										</div>
									</div>
									<?php } wp_reset_postdata();
				?>
								</div>
								<?php echo paginate_links(array('total' => $pastEvents->max_num_pages));?>
							</li>

							<li>
								<div>
									<h3>Testimonials</h3>
									<h5> Every year we get hundreds of emails and google reviews from our satisfied students. Below are just some of the great feedback we are so happy to receive. </h5> <p> Please feel free to submit your own feedback to Prep Academy; we always take into account the views of our students, to make sure we are delivering the highest possible quality educational experience.</p>
									<div style="margin: 0 auto; padding-bottom: 80px;">
										<?php
							$testimonials = new WP_Query(array(
								'post_type' => 'student_testimonials',
								'posts_per_page' => 3
							));
			
							$count = 1;
							while($testimonials->have_posts()) {
								$testimonials->the_post();
								$image = get_field('student_image')
					?>
										<div <?php if($count%2==0) {echo 'class="testimonial-quote group"' ;} else
											{echo 'class="testimonial-quote group right"' ;}?>>
											<?php 
						$post = get_post();
						$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) ); 
					?>
											<img src="<?php echo esc_url($image['url']); ?>">
											<div class="quote-container">
												<blockquote>
													<p>
														<?php the_field('testimony'); ?>
													</p>
												</blockquote>
												<cite><span>
														<?php the_field('student_name'); ?>
													</span><br>
													<?php the_field('designation'); ?><br>
													<?php the_field('institute'); ?>
												</cite>
											</div>
										</div>
										<?php 
							$count++;
							}  wp_reset_postdata();
					?>
									</div>
								</div>
							</li>
						</ul>

					</div>
				</div>
			</div>
		</div>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.min.js"></script>
		<script types="text/javascript">

			$(document).on("click", ".naccs .menu div", function () {
				var numberIndex = $(this).index();
				let query = window.matchMedia("(min-width: 800px)");

				if (query.matches) {
					if (!$(this).is("active")) {
						$(".naccs .menu div").removeClass("active");
						$(".naccs ul.second li").removeClass("active");

						$(this).addClass("active");
						$(".naccs ul.second")
							.find("li:eq(" + Math.floor(numberIndex / 2) + ")")
							.addClass("active");

						var listItemHeight = $(".naccs ul.second")
							.find("li:eq(" + Math.floor(numberIndex / 2) + ")")
							.innerHeight();
						$(".naccs ul.second").height(listItemHeight + "px");
					}
				} else {
					if (!$(this).is("active")) {
						$(".naccs .menu div").removeClass("active");
						$(".naccs ul li").removeClass("active");
						$(".naccs ul").height(0);

						$(this).addClass("active");
						$('html, body').animate({
							scrollTop: $('#for_scroll').offset().top - 20 
						}, 'slow');
					}

					if (!$(this).is("active")) {

						var nextElement = $(this).next();
						var nextElementChild = nextElement.find("li");
						var listItemHeight = nextElementChild.outerHeight(true);

						if (nextElement.height()) {
							nextElement.height(0);
							nextElementChild.removeClass("active");
						} else {
							nextElementChild.addClass("active");
							nextElement.height(listItemHeight);
						}
					}

				}

			});
		</script>

		</html>
	<?php
	return ob_get_clean();
}

// Updates page shortcode
function daily_updates_shortcode() { 
	ob_start(); ?>
	<html>
	<div class="info">
		<div class="naccs">
			<div class="grid">
				<div class="gc gc--1-of-3">
					<div class="menu">
						<div class="active"><span class="light"></span><span>Study Materials</span></div>
						<ul class="nacc">
							<li class="collapse">
								<div>
									<h3>Study Materials</h3>
									<h5>In today’s world, it is vital for students to have access to top-notch resources in order to excel. </h5> <p> We  have compiled study material for a variety of subjects, which is available to assist students in clearing their concepts and identifying their weaknesses.</p>
									<!--<button class="course-button course-button-color">Course Title 1</button>
									<button class="course-button course-button-color">Course Title 2</button>
									<button class="course-button course-button-color">Course Title 3</button>
									<button class="course-button course-button-color">Course Title 3</button>
									<button class="course-button course-button-color">Course Title 3</button>-->
									<h6>Coming Soon.....</h6>
									<h3 style="margin-top: 20px;">Test Series</h3>
									<h5>It is not only important to study regularly but also to conduct regular assessments to strengthen the weaker areas of knowledge.</h5>
									<a href="https://test.prepacademy.in" class="link-hover"><button
											class="course-button course-button-color">Test Series</button></a>
								</div>
							</li>
						</ul>
						<div><span class="light"></span><span>Exam Dates</span></div>
						<ul class="nacc">
							<li class="collapse">
								<div>
									<h3>Exam Updates</h3>
									<h5>Keep Track of Important Exam Dates. Students/candidates can find date sheets and time-table for all government related jobs and school board exams.</h5>
									<div style="margin: 0 auto; padding-bottom: 80px;">
										<?php
							$examPosts = new WP_Query(array(
									'post_type' => 'exams',
									'posts_per_page' => 6
								));
			
							while($examPosts->have_posts()) {
								$examPosts->the_post();
							
						?>
										<div class="details-list group right">
											<div class="item-container">
												<div>
													<h4 style="margin: 0;">
														<?php the_title(); ?>
													</h4>
													<h5 style="margin:0;margin-bottom: 5px;">Last Date:
														<?php the_field('last_date');?>
													</h5>
													<p style="margin:0;margin-bottom: 5px;"><?php the_field('description');?></p>
													<a href="<?php the_field('notification_url');?>" target="_blank" class="link-hover" rel="noreferrer noopener">
														<h5 style="margin:0;margin-top: 10px;">Know More</h5>
													</a>
												</div>
											</div>
										</div>
										<?php } wp_reset_postdata();?>
									</div>
								</div>
							</li>
						</ul>
						<div><span class="light"></span><span>Government Jobs</span></div>
						<ul class="nacc">
							<li class="collapse">
								<div>
									<h3>Government Job Openings</h3>
									<h5>India being such a huge democracy, Government Jobs form a crucial part of the system. Lakhs of candidates after completing their education wants to join the system and for their dream to come true, the government provides a lot of opportunities every year. </h5>
									<div style="margin: 0 auto; padding-bottom: 80px;">
										<div class="job-flex-container ">
											<?php
							$jobPosts = new WP_Query(array(
									'post_type' => 'government_jobs',
									'posts_per_page' => 6
								));
			
							while($jobPosts->have_posts()) {
								$jobPosts->the_post();
							
						?>
											<div>
												<header class="entry-header">
													<h3 class="entry-title"><a target="_blank" href="<?php echo esc_url(get_permalink()); ?>">
															<?php the_title(); ?>
														</a></h3> 
												</header>
												<div class="slide-content">
													<div class="slide-meta">
														<span class="job-meta"><i class="fa fa-briefcase"></i>
															<?php echo get_field('experience') ?>
														</span><br>
														<span class="job-meta"> &#8377;
															<?php echo get_field('salary') ?>
														</span><br>
														<span class="job-meta"><i class="fa fa-location-arrow">
																Unknown</i></span>
													</div>
													<div class="slide-entry-content"><span class="job-meta">
															<?php echo get_field('job_excerpt'); ?>
														</span></div>
													<footer class="entry-footer">
														<div class="slide-meta-date">Last Date:
															<?php the_field('last_date');
										/*		$lastDate = new DateTime(get_field('last_date'));
												$lastDate->format('Ymd');
												echo $lastDate; ?> */ ?>
														</div>
													</footer>
												</div>
											</div>
											<?php } wp_reset_postdata();?>
										</div>
									</div>
								</div>

							</li>
						</ul>
						<div><span class="light"></span><span>Private Jobs</span></div>
						<ul class="nacc">
							<li class="collapse">
								<div>
									<h3>Private Job Openings</h3>
									<h5>Job aspirants can know all the Latest Private Jobs in India. Both Freshers and Experienced candidates shall follow this page to notice the  Jobs  in various cities across India.</h5>
									<div style="margin: 0 auto; padding-bottom: 80px;">
										<div class="job-flex-container ">
											<?php
							$jobPosts = new WP_Query(array(
									'post_type' => 'private_jobs',
									'posts_per_page' => 6
								));
			
							while($jobPosts->have_posts()) {
								$jobPosts->the_post();
							
						?>
											<div>
												<header class="entry-header">
													<h3 class="entry-title"><a target="_blank" href="<?php echo esc_url(get_permalink()); ?>">
															<?php the_title(); ?>
														</a></h3>
												</header>
												<div class="slide-content">
													<div class="slide-meta">
														<span class="job-meta"><i class="fa fa-briefcase"></i>
															<?php echo get_field('experience') ?>
														</span><br>
														<span class="job-meta"> &#8377;
															<?php echo get_field('salary') ?>
														</span><br>
														<span class="job-meta"><i class="fa fa-location-arrow">
																Unknown</i></span>
													</div>
													<div class="slide-entry-content"><span class="job-meta">
															<?php echo get_field('job_excerpt'); ?>
														</span></div>
													<footer class="entry-footer">
														<div class="slide-meta-date">Last Date:
															<?php the_field('last_date');
										/*		$lastDate = new DateTime(get_field('last_date'));
												$lastDate->format('Ymd');
												echo $lastDate; ?> */ ?>
														</div>
													</footer>
												</div>
											</div>
											<?php } wp_reset_postdata();?>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="gc gc--2-of-3">
					<ul class="nacc second">
						<li class="active">
							<div>
								<h3>Study Materials</h3>
								<h5>In today’s world, it is vital for students to have access to top-notch resources in order to excel. We  have compiled study material for a variety of subjects, which is available to assist students in clearing their concepts and identifying their weaknesses.</h5>
								<h6> Coming Soon.....</h6>
								<!--<button class="course-button course-button-color">Course Title 1</button>
								<button class="course-button course-button-color">Course Title 2</button>
								<button class="course-button course-button-color">Course Title 3</button>
								<button class="course-button course-button-color">Course Title 3</button>
								<button class="course-button course-button-color">Course Title 3</button>-->
								<h3 style="margin-top: 20px;">Test Series</h3>
								<h5>It is not only important to study regularly but also to conduct regular assessments to strengthen the weaker areas of knowledge. We provide excellent assessments and test for our students.</h5>
								<a href="https://test.prepacademy.in" class="link-hover"><button
										class="course-button course-button-color">Test Series</button></a>
							</div>
						</li>
						<li>
							<div>
								<h3>Exam Updates</h3>
								<h5>Keep Track of Important Exam Dates. Students/candidates can find date sheets and time-table for all government related jobs and school board exams.</h5>
								<div style="margin: 0 auto; padding-bottom: 80px;">
									<?php
						$examPosts = new WP_Query(array(
								'post_type' => 'exams',
								'posts_per_page' => 6
							));
		
						while($examPosts->have_posts()) {
							$examPosts->the_post();
						
					?>
									<div class="details-list group right">
										<div class="item-container">
											<div>
												<h4 style="margin: 0;">
													<?php the_title(); ?>
												</h4>
												<h5 style="margin:0;margin-bottom: 5px;">Last Date:
													<?php the_field('last_date');?>
												</h5>
												<p style="margin:0;margin-bottom: 5px;"><?php the_field('description');?></p>
												<a href="<?php the_field('notification_url');?>" target="_blank" class="link-hover" rel="noreferrer noopener">
													<h5 style="margin:0;margin-top: 10px;">Know More</h5>
												</a>
											</div>
										</div>
									</div>
									<?php } wp_reset_postdata();?>
								</div>
							</div>
						</li>
						<li>
							<div>
								<h3>Government Job Openings</h3>
								<h5>India being such a huge democracy, Government Jobs form a crucial part of the system. Lakhs of candidates after completing their education wants to join the system and for their dream to come true, the government provides a lot of opportunities every year.</h5> <p> Some of them are of national level and some of State level. UPSC, SSC, IAS, IBPS, SBI, Railway-RRB, Bank Jobs being some national-level government jobs that have their own charm. Some state government exams like UPPSC, UKPSC, TNPSC, MPPSC and more also provide good opportunities to have a settled and subtle government job. Starting from an 8th Pass candidate, with suitable qualifications and skills, one can check all types of relevant government jobs. </p>
								<div style="margin: 0 auto; padding-bottom: 80px;">
									<div class="job-flex-container ">
										<?php
						$jobPosts = new WP_Query(array(
								'post_type' => 'government_jobs',
								'posts_per_page' => 6
							));
		
						while($jobPosts->have_posts()) {
							$jobPosts->the_post();
						
					?>
										<div>
											<header class="entry-header">
											<h3 class="entry-title"><a target="_blank" href="<?php echo esc_url( get_permalink());?>">
														<?php the_title(); ?>
													</a></h3>
											</header>
											<div class="slide-content">
												<div class="slide-meta">
													<span class="job-meta"><i class="fa fa-briefcase"></i>
														<?php echo get_field('experience') ?>
													</span><br>
													<span class="job-meta"> &#8377;
														<?php echo get_field('salary') ?>
													</span><br>
													<span class="job-meta"><i class="fa fa-location-arrow">
															Unknown</i></span>
												</div>
												<div class="slide-entry-content"><span class="job-meta">
														<?php echo get_field('job_excerpt'); ?>
													</span></div>
												<footer class="entry-footer">
													<div class="slide-meta-date">Last Date:
														<?php the_field('last_date');
									/*		$lastDate = new DateTime(get_field('last_date'));
											$lastDate->format('Ymd');
											echo $lastDate; ?> */ ?>
													</div>
												</footer>
											</div>
										</div>
										<?php } wp_reset_postdata();?>
									</div>
								</div>
							</div>

						</li>
						<li>
							<div>
								<h3>Private Job Openings</h3>
								<h5>Job aspirants can know all the Latest Private Jobs in India. Both Freshers and Experienced candidates shall follow this page to notice the  Jobs  in various cities across India. </h5><p>Through this page, we list the Private Jobs Recruitment  of all the private-sector job openings. If the job seekers are preferred to work in the Private sector, they can see the Private jobs List  with complete details. Up to date information of all the Private jobs Off Campus Drive  of all the Private companies is also provided.  </p>
								<div style="margin: 0 auto; padding-bottom: 80px;">
									<div class="job-flex-container ">
										<?php
						$jobPosts = new WP_Query(array(
								'post_type' => 'private_jobs',
								'posts_per_page' => 6
							));
		
						while($jobPosts->have_posts()) {
							$jobPosts->the_post();
						
					?>
										<div>
											<header class="entry-header">
												<h3 class="entry-title"><a target="_blank" href="<?php echo esc_url(get_permalink()); ?>">
														<?php the_title(); ?>
													</a></h3>
											</header>
											<div class="slide-content">
												<div class="slide-meta">
													<span class="job-meta"><i class="fa fa-briefcase"></i>
														<?php echo get_field('experience') ?>
													</span><br>
													<span class="job-meta"> &#8377;
														<?php echo get_field('salary') ?>
													</span><br>
													<span class="job-meta"><i class="fa fa-location-arrow">
															Unknown</i></span>
												</div>
												<div class="slide-entry-content"><span class="job-meta">
														<?php echo get_field('job_excerpt'); ?>
													</span></div>
												<footer class="entry-footer">
													<div class="slide-meta-date">Last Date:
														<?php the_field('last_date');
									/*		$lastDate = new DateTime(get_field('last_date'));
											$lastDate->format('Ymd');
											echo $lastDate; ?> */ ?>
													</div>
												</footer>
											</div>
										</div>
										<?php } wp_reset_postdata();?>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.min.js"></script>
	<script types="text/javascript">
		
		$(window).on("load", function(){
			let query = window.matchMedia("(min-width: 800px)");

			if (query.matches) {
				var listItemHeight = $(".naccs ul.second")
				.find("li.active")
				.innerHeight();
				$(".naccs ul.second").height(listItemHeight + "px");
			}
		})
		$(document).on("click", ".naccs .menu div", function () {
			var numberIndex = $(this).index();
			let query = window.matchMedia("(min-width: 800px)");

			if (query.matches) {
				if (!$(this).is("active")) {
					$(".naccs .menu div").removeClass("active");
					$(".naccs ul.second li").removeClass("active");

					$(this).addClass("active");
					$(".naccs ul.second")
						.find("li:eq(" + Math.floor(numberIndex / 2) + ")")
						.addClass("active");

					var listItemHeight = $(".naccs ul.second")
						.find("li:eq(" + Math.floor(numberIndex / 2) + ")")
						.innerHeight();
					$(".naccs ul.second").height(listItemHeight + "px");
				}
			} else {
				if (!$(this).is("active")) {
					$(".naccs .menu div").removeClass("active");
					$(".naccs ul li").removeClass("active");
					$(".naccs ul").height(0);

					$(this).addClass("active");
				}

				if (!$(this).is("active")) {

					var nextElement = $(this).next();
					var nextElementChild = nextElement.find("li");
					var listItemHeight = nextElementChild.outerHeight(true);

					if (nextElement.height()) {
						nextElement.height(0);
						nextElementChild.removeClass("active");
					} else {
						nextElementChild.addClass("active");
						nextElement.height(listItemHeight);
					}
				}

			}

		});
	</script>

	</html>
	<?php
	return ob_get_clean();
}

// Contact page shortcode
function reach_us_shortcode() { 
	ob_start(); ?>
	<html>
	<div class="info">
		<div class="naccs">
			<div class="grid">
				<div class="gc gc--1-of-3">
					<div class="menu">
						<div class="active"><span class="light"></span><span>Find A Centre</span></div>
						<ul class="nacc">
							<li class="collapse">
								<div>
									<h3>Find a Centre</h3>
									<h5>To use the below search you need Javascript enabled in your browser.</h5>
									<script>
										var stateObject = {
											"kerala": "Kerala",
											"tamilnadu": "Tamil Nadu",
											"karnataka": "Karnataka",
											"maharashtra": "Maharashtra"
										};
										var districtObject = {
											"kerala": { "thiruvananthapuram": "Thiruvananthapuram", "ernakulam": "Ernakulam", "kozhikode": "Kozhikode" },
											"tamilnadu": { "chennai": "Chennai", "coiambatore": "Coiambatore" },
											"karnataka": { "bengaluru": "Bengaluru" },
											"maharashtra": { "mumbai": "Mumbai" }
										};

										window.onload = function () {
											var stateSel = document.getElementById('state'),
												districtSel = document.getElementById('district');

											for (const [key, value] of Object.entries(stateObject)) {
												stateSel.options[stateSel.options.length] = new Option(value, key); //Options(text, value) => here value (<option value ="" >) is  our dictionary key;
											}

											stateSel.onchange = function () {
												districtSel.length = 1; //remove all options bar first

												if (this.selectIndex < 1) return;
												for (const [key, value] of Object.entries(districtObject[this.value])) {
													districtSel.options[districtSel.options.length] = new Option(value, key);
												}
											};
										};


									</script>

									<form class="form-inline" action="">
										<label for="state">State</label>
										<select id="state" name="state" aria-placeholder="Select State">
											<option value="none">Select State</option>
										</select>
										<label for="district">District</label>
										<select id="district" name="district" aria-placeholder="Select District">
											<option value="none">Select District</option>
										</select>
										<button class="apply-button apply-button-color" type="submit">Submit</button>
									</form>
									<br>
									<br>
									<br>

									<?php 
						$state = $_GET["state"];
						$district = $_GET["district"];
						
			
						$centers = new WP_Query(array(
							'post_type' => 'centers',
							'posts_per_page' => -1,
							'meta_query' => array(
										'relation' => 'AND',
										array(
										'key' => 'state',
										'compare' => '=',
										'value' => $state
										),
										array(
											'key' => array('district_of_kerala', 'district_of_tamil_nadu', 'district_of_karnataka'),
											'compare' => '=',
											'value' => $district
										),
								),
						)); 
						
							if($centers->have_posts()) {
								while($centers->have_posts()) {
								$centers->the_post(); ?>
									<div class="card">
										<div class="container">
											<div class="cardcontainer">
												<div class="photo"> <img
														src="<?php echo esc_url(get_field('city_image')['url']); ?>">
													<div class="photos">Active</div>
												</div>
												<div class="content">
													<p class="txt4">
														<?php echo get_field('district') ?>
													</p>
													<p class="txt5">Address:</p>
													<p class="txt2">
														<?php the_field('address')?>
													</p>
												</div>
												<div class="footer">
													<p><a class="waves-effect waves-light btn" href="#"><i
																class="fa fa-phone" aria-hidden="true"></i> +91
															<?php echo get_field('contact_number'); ?>
														</a></p>
													<p class="txt3"><i class="fas fa-envelope-open-text"></i>
														<?php echo get_field('email_address'); ?>
													</p>
												</div>
											</div>
										</div>
									</div>
									<?php	} wp_reset_postdata();
							} else { ?>

									<p class="txt5">Sorry we will be opening at
										<?php echo $district; ?> soon !!!
									</p>
									<?php	} 
					?>
								</div>
							</li>
						</ul>
						<div><span class="light"></span><span>Career Opportunities</span></div>
						<ul class="nacc">
							<li class="collapse">
								<div>
									<h3>Career Opportunities</h3>
									<p>Come to Prep Academy to maximise yourself because when you maximise, we maximise. Our success lies in our people. We offer our employees opportunities to make a meaningful impact, gain new skills and build successful careers in a diverse and inclusive workplace. Bring your unique perspective. Bring curiosity. Bring ingenuity. Bring drive. Because what makes you unique, makes us better.</p>
									<!--<br><h6> No Current Openings.....</h6>-->
									<div style="margin: 0 auto; padding-bottom: 30px;">
										<?php
							$examPosts = new WP_Query(array(
									'post_type' => 'career_opportunities',
									'posts_per_page' => 6
								));
			
							while($examPosts->have_posts()) {
								$examPosts->the_post();
							
						?>
										<div class="details-list group right">
											<div class="item-container">
												<div>
													<h4 style="margin: 0;">
														<?php the_title(); ?>
													</h4>
													<h5 style="margin:0;margin-bottom: 5px;">Job ID:
														<?php the_field('job_id');?>
													</h5>
													<h5 style="margin:0;margin-bottom: 5px;">Last Date:
														<?php the_field('last_date');?>
													</h5>
													<p style="margin:0;margin-bottom: 5px;"><?php the_field('job_description');?></p>
										
												</div>
											</div>
										</div>
										<?php } wp_reset_postdata();?>
									</div>
								</div>
								<div>
									<h3>Send Us Resume</h3>
									<h5>Send your detailed resume to career@prepacademy.in</h5>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="gc gc--2-of-3">
					<ul class="nacc second">
						<li class="active">
							<div>
								<h3>Find a Centre</h3>
								<h5>To use the below search you need Javascript enabled in your browser.</h5>
								<script type="text/javascript">
									var stateObject = {
										"kerala": "Kerala",
										"tamilnadu": "Tamil Nadu",
										"karnataka": "Karnataka",
										"maharashtra": "Maharashtra"
									};
									var districtObject = {
										"kerala": { "thiruvananthapuram": "Thiruvananthapuram", "ernakulam": "Ernakulam", "kozhikode": "Kozhikode" },
										"tamilnadu": { "chennai": "Chennai", "coiambatore": "Coiambatore" },
										"karnataka": { "bengaluru": "Bengaluru" },
										"maharashtra": { "mumbai": "Mumbai" }
									};

									window.onload = function () {
										var stateSel = document.getElementById('state2'),
											districtSel = document.getElementById('district2');

										for (const [key, value] of Object.entries(stateObject)) {
											stateSel.options[stateSel.options.length] = new Option(value, key); //Options(text, value) => here value (<option value ="" >) is  our dictionary key;
										}

										stateSel.onchange = function () {
											districtSel.length = 1; //remove all options bar first

											if (this.selectIndex < 1) return;
											for (const [key, value] of Object.entries(districtObject[this.value])) {
												districtSel.options[districtSel.options.length] = new Option(value, key);
											}
										};
									};
								</script>

								<form class="form-inline" action="">
									<label for="state">State</label>
									<select id="state2" name="state2" aria-placeholder="Select State">
										<option value="none">Select State</option>
									</select>
									<label for="district">District</label>
									<select id="district2" name="district2" aria-placeholder="Select District">
										<option value="none">Select District</option>
									</select>
									<button id="desktop-form-button" class="apply-button apply-button-color"
										type="submit">Submit</button>
								</form>
								<br>
								<br>
								<br>
								<?php 
					$state = $_GET["state2"];
					$district = $_GET["district2"];
					
		
					$centers = new WP_Query(array(
						'post_type' => 'centers',
						'posts_per_page' => -1,
						'meta_query' => array(
									'relation' => 'AND',
									array(
									'key' => 'state',
									'compare' => '=',
									'value' => $state
									),
									array(
										'key' => array('district_of_kerala', 'district_of_tamil_nadu', 'district_of_karnataka'),
										'compare' => '=',
										'value' => $district
									),
							),
					)); 
					
					if ($district!="none") {
						if($centers->have_posts()) {
							while($centers->have_posts()) {
							$centers->the_post(); ?>
								<div class="card">
									<div class="container">
										<div class="cardcontainer">
											<div class="photo"> <img
													src="<?php echo esc_url(get_field('city_image')['url']); ?>">
												<div class="photos">Active</div>
											</div>
											<div class="content">
												<p class="txt4">
													<?php echo get_field('district') ?>
												</p>
												<p class="txt5">Address:</p>
												<p class="txt2">
													<?php the_field('address')?>
												</p>
											</div>
											<div class="footer">
												<p><a class="waves-effect waves-light btn" href="#"><i class="fa fa-phone"
															aria-hidden="true"></i> +91
														<?php echo get_field('contact_number'); ?>
													</a></p>
												<p class="txt3"><i class="fas fa-envelope-open-text"></i>
													<?php echo get_field('email_address'); ?>
												</p>
											</div>
										</div>
									</div>
								</div>
								<style>
									.gc,
									.info {
										min-height: 850px;
									}
								</style>
								<?php	} wp_reset_postdata();
						} else { ?>

								<style>
									.gc,
									.info {
										min-height: 250px;
									}
								</style>
								<?php	} 
					}?>

							</div>
						</li>
						<li>
							<div>
								<h3>Career Opportunities</h3>
								<p>Come to Prep Academy to maximise yourself because when you maximise, we maximise. Our success lies in our people. We offer our employees opportunities to make a meaningful impact, gain new skills and build successful careers in a diverse and inclusive workplace. Bring your unique perspective. Bring curiosity. Bring ingenuity. Bring drive. Because what makes you unique, makes us better.</p>
								<!--<br><h6> No Current Openings.....</h6>-->
								<div style="margin: 0 auto; padding-bottom: 30px;">
										<?php
							$examPosts = new WP_Query(array(
									'post_type' => 'career_opportunities',
									'posts_per_page' => 6
								));
			
							while($examPosts->have_posts()) {
								$examPosts->the_post();
							
						?>
										<div class="details-list group right">
											<div class="item-container">
												<div>
													<h4 style="margin: 0;">
														<?php the_title(); ?>
													</h4>
													<h5 style="margin:0;margin-bottom: 5px;">Job ID:
														<?php the_field('job_id');?>
													</h5>
													<h5 style="margin:0;margin-bottom: 5px;">Last Date:
														<?php the_field('last_date');?>
													</h5>
													<p style="margin:0;margin-bottom: 5px;"><?php the_field('job_description');?></p>
													
												</div>
											</div>
										</div>
										<?php } wp_reset_postdata();?>
									</div>
							</div>
							<div>
								<h3>Send Us Resume</h3>
								<h5>Send your detailed resume to career@prepacademy.in</h5>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.0/js/swiper.min.js"></script>
	<script types="text/javascript">
		$(document).on("click", ".naccs .menu div", function () {
			var numberIndex = $(this).index();
			let query = window.matchMedia("(min-width: 1029px)");

			if (query.matches) {
				if (!$(this).is("active")) {
					$(".naccs .menu div").removeClass("active");
					$(".naccs ul.second li").removeClass("active");

					$(this).addClass("active");
					$(".naccs ul.second")
						.find("li:eq(" + Math.floor(numberIndex / 2) + ")")
						.addClass("active");

					var listItemHeight = $(".naccs ul.second")
						.find("li:eq(" + Math.floor(numberIndex / 2) + ")")
						.innerHeight();
					$(".naccs ul.second").height(listItemHeight + "px");
				}
			} else {
				if (!$(this).is("active")) {
					$(".naccs .menu div").removeClass("active");
					$(".naccs ul li").removeClass("active");
					$(".naccs ul").height(0);

					$(this).addClass("active");
				}

				if (!$(this).is("active")) {

					var nextElement = $(this).next();
					var nextElementChild = nextElement.find("li");
					var listItemHeight = nextElementChild.outerHeight(true);

					if (nextElement.height()) {
						nextElement.height(0);
						nextElementChild.removeClass("active");
					} else {
						nextElementChild.addClass("active");
						nextElement.height(listItemHeight);
					}
				}

			}

		});
	</script>

	</html>

	<?php
	return ob_get_clean();
}

// Replace the home link URL
function woo_custom_breadrumb_home_url() {
    return 'http://prepacademy.local/courses/';
}
add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url');

//Change the title placeholder text of CPT

function change_default_title( $title ){
     $screen = get_current_screen();

     if  ( $screen->post_type == 'staff_details') {
          return 'Enter Name';
     }
     if  ( $screen->post_type == 'school_course' or $screen->post_type =='college_courses' or $screen->post_type =='study_abroad') {
          return 'Enter Course Name';
     }
     if  ($screen->post_type =='open_session_sepaker') {
          return 'Enter Speaker Name';
     }
}
add_filter( 'enter_title_here', 'change_default_title' );

// Update your custom mobile breakpoint below - like return 544;
add_filter( 'astra_mobile_breakpoint', function() {
    return 544;
});

// Update your custom tablet breakpoint below - like return 921;
add_filter( 'astra_tablet_breakpoint', function() {
    return 1289;
});

//Edit the order of items
function wpse_custom_menu_order( $menu_ord ) {
    if ( !$menu_ord ) return true;

    return array(
        'index.php', // Dashboard
		'profile.php', //Profile
        'separator1', // First separator
        'edit.php', // Posts
        'upload.php', // Media
        'link-manager.php', // Links
        'edit-comments.php', // Comments
        'edit.php?post_type=page', // Pages
        'themes.php', // Appearance
        'plugins.php', // Plugins
        'users.php', // Users
        'tools.php', // Tools
        'options-general.php', // Settings
		'edit.php?post_type=school_course', //School Courses
		'edit.php?post_type=college_courses', //College Courses
		'edit.php?post_type=study_abroad', //Study Abroad
		'edit.php?post_type=study_materials', //Study Material
		'edit.php?post_type=exams', //Exams
		'edit.php?post_type=government_jobs', //Govt Jobs
		'edit.php?post_type=private_jobs', //Private Jobs
		'edit.php?post_type=open_sessions', //Open Sessions
		'edit.php?post_type=medias', //Media Updates
		'edit.php?post_type=student_testimonials', //Testimonials
		'edit.php?post_type=staff_details', //Staff
		'edit.php?post_type=centers', //Centers
		'separator2',
		'smart-slider3&nextendcontroller=sliders&nextendaction=index', //Smart Slider
		'fv-leads', //Forms
    );
}
add_filter( 'custom_menu_order', 'wpse_custom_menu_order', 10, 1 );
add_filter( 'menu_order', 'wpse_custom_menu_order', 10, 1 );

//Add additional separators in admin menu
function admin_menu_separator( $parent_file ) {
    $menu = &$GLOBALS['menu'];
    $submenu = &$GLOBALS['submenu'];
    foreach( $submenu as $key => $item )
    {
        foreach ( $item as $index => $data )
        {
            // Check if we got the identifier
            if ( in_array( 'wp-menu-separator', $data, true ) )
            {
                // Set the MarkUp, so it gets used instead of the menu title
                $data[0] = '<div class="separator"></div>';
                // Grab our index and temporarily save it, so we can safely overrid it
                $new_index = $data[2];
                // Set the parent file as new index, so core attaches the "current" class
                $data[2] = $GLOBALS['parent_file'];
                // Reattach to the global with the new index
                $submenu[ $key ][ $new_index ] = $data;
                // Prevent duplicate
                unset( $submenu[ $key ][ $index ] );
                // Get back into the right order
                ksort( $submenu[ $key ] );
            }
        }
    }
    foreach( $menu as $key => $item )
    {
        if (
            in_array( 'wp-menu-separator', $item )
            AND 5 < count( $item )
            )
        {
            $menu[ $key ][2] = 'separator0';
            $menu[ $key ][4] = 'wp-menu-separator';
            unset(
                 $menu[ $key ][5]
                ,$menu[ $key ][6]
            );
        }
    }
    return $parent_file;
}

function add_admin_menu_separator() {
    add_menu_page( '', '', 'read', 'wp-menu-separator', '', '', '21' );
    add_submenu_page( 'edit.php?post_type=page', 'wp-menu-separator', '', 'read', '11', '' );
}
add_filter( 'parent_file', 'admin_menu_separator' );

//To Change the Posts Name in Admin Menu and Labels
function change_admin_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Blog';
    $submenu['edit.php'][5][0] = 'Blog Posts';
    $submenu['edit.php'][10][0] = 'Add Blog Post';
    $submenu['edit.php'][15][0] = 'Blog Categories'; // Change name for categories
    $submenu['edit.php'][16][0] = 'Labels'; // Change name for tags
    echo '';
}

function change_post_object_label() {
        global $wp_post_types;
        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'Blog Posts';
        $labels->singular_name = 'Blog Post';
        $labels->add_new = 'Add Blog Post';
        $labels->add_new_item = 'Add Blog Post';
        $labels->edit_item = 'Edit Blog Posts';
        $labels->new_item = 'Blog Post';
        $labels->view_item = 'View Blog Post';
        $labels->search_items = 'Search Blog Posts';
        $labels->not_found = 'No Blog Posts found';
        $labels->not_found_in_trash = 'No Blog Posts found in Trash';
    }

// Disable gutenberg starter template in post types
add_filter( 'ast_block_templates_disable', '__return_true' );

//List speakers on open session single page
function open_session_single_page_speaker_list() { 
	ob_start(); 	
			$relatedSpeakers = get_field('speakers');
			if ($relatedSpeakers) {
				foreach ($relatedSpeakers as $speaker) {
					setup_postdata($speaker);
					?>
					<div class="overlay">
						<div class="outer-container">
							<div class="inner-container1">
								<img src="<?php echo get_field('speaker_image', $speaker->ID)["url"]; ?>" alt="<?php echo get_field('speaker_image', $speaker->ID)["url"]; ?>">
							</div>
							<div class="inner-container2">
								<h3><?php echo get_the_title($speaker); ?></h3>
								<h5><?php echo get_field('speaker_qualification', $speaker->ID); ?></h5>
								<p><?php echo get_field('speaker_description', $speaker->ID); ?></p>
							</div>
						</div>
					</div>
					<?php
					}
				} wp_reset_postdata(); 
	return ob_get_clean();
}

//Disable admin notices
function ds_admin_theme_style() {
	if (!current_user_can('edit_dashboard')) {
		echo '<style>.update-nag, .updated, .error, .is-dismissible .notice .notice-success { display: none; }</style>';
		echo '<style>.wp-core-ui .notice.is-dismissible{ display: none; }</style>';
		echo '<style>.fv-review, .fv-pro-box, .fv-notice{ display: none !important; }</style>';
		echo '<style> .notice-info, .notice.notice-info.important { display: none !important; }</style>';
	}
}

// Collapsible footer menu items
function footer_collapsable_list() {
	ob_start(); ?>
		<ul class="torno-icon-list-items">
			<li class="torno-icon-list-item">
				<div class="collapsible">
					<span class="torno-icon-list-icon">
						<i aria-hidden="true" class="fas fa-angle-right"></i> </span>
					<a href="">
						<span class="torno-icon-list-text">School Courses</span>
					</a>
				</div>
				<div class="inner-list-container">
					<ul>
						<?php
							$schoolCourses = new WP_Query(array(
								'post_type' => 'school_course',
								'posts_per_page' => -1
							));

							while($schoolCourses->have_posts()) {
								$schoolCourses->the_post();
								?><li><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></li><?php
							} wp_reset_postdata();
						?>
					</ul>
				</div>
			</li>
			<li class="torno-icon-list-item">
				<div class="collapsible">
					<span class="torno-icon-list-icon">
						<i aria-hidden="true" class="fas fa-angle-right"></i> </span>
					<a href="">
						<span class="torno-icon-list-text">College Courses</span>
					</a>
				</div>
				<div class="inner-list-container">
					<ul>
						<?php
							$collegeCourses = new WP_Query(array(
								'post_type' => 'college_courses',
								'posts_per_page' => -1
							));

							while($collegeCourses->have_posts()) {
								$collegeCourses->the_post();
								?><li><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></li><?php
							} wp_reset_postdata();
						?>
					</ul>
				</div>
			</li>
			<li class="torno-icon-list-item">
				<div class="collapsible">
					<span class="torno-icon-list-icon">
						<i aria-hidden="true" class="fas fa-angle-right"></i> </span>
					<a href="">
						<span class="torno-icon-list-text">Study Abroad</span>
					</a>
				</div>
				<div class="inner-list-container">
					<ul>
						<?php
							$studyAbroad = new WP_Query(array(
								'post_type' => 'study_abroad',
								'posts_per_page' => -1
							));

							while($studyAbroad->have_posts()) {
								$studyAbroad->the_post();
								?><li><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></li><?php
							} wp_reset_postdata();
						?>
					</ul>
				</div>
			</li>
			<li class="torno-icon-list-item">
				<div class="collapsible">
					<span class="torno-icon-list-icon">
						<i aria-hidden="true" class="fas fa-angle-right"></i> </span>
					<a href="">
						<span class="torno-icon-list-text">Study Materials</span>
					</a>
				</div>
				<div class="inner-list-container">
					<ul>
						<?php
							$studyMaterials = new WP_Query(array(
								'post_type' => 'study_materials',
								'posts_per_page' => -1
							));

							while($studyMaterials->have_posts()) {
								$studyMaterials->the_post();
								?><li><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></li><?php
							} wp_reset_postdata();
						?>
					</ul>
				</div>
			</li>
		</ul>
		
		<script>
			var coll = document.getElementsByClassName("collapsible");
			var i;

			for (i = 0; i < coll.length; i++) {
			  coll[i].addEventListener("click", function() {
				var content = this.nextElementSibling;
				if (content.style.maxHeight) {
				  	content.style.maxHeight = null;
					this.firstElementChild.firstElementChild.classList.remove("fa-angle-down");
					this.firstElementChild.firstElementChild.classList.add("fa-angle-right");
				} else {
					this.firstElementChild.firstElementChild.classList.remove("fa-angle-right");
					this.firstElementChild.firstElementChild.classList.add("fa-angle-down");
				  	content.style.maxHeight = content.scrollHeight + "px";
				}
			  });
			} 
		</script>
	<?php
	return ob_get_clean();
}


// Mobile view of home page course buttons with tabs
function mobile_tab_for_course_buttons_shortcode() {
    ob_start(); ?>
    <div class="w3-container">
        <h4>Choose your Preference</h4>

        <div class="w3-bar w3-black">
            <button class="w3-bar-item w3-button tablink w3-red" onclick="openTab(event,'schoolCourse')" autofocus>School Courses</button>
            <button class="w3-bar-item w3-button tablink" onclick="openTab(event,'collegeCourse')">College
                Courses</button>
            <button class="w3-bar-item w3-button tablink" onclick="openTab(event,'studyAbroad')">Study
                Abroad</button>
        </div>
        <div id="schoolCourse" class="w3-container w3-border course">
            <div class=" button-justify">

                <?php
                $schoolCourses = new WP_Query(array(
                    'post_type' => 'school_course'
                ));

                while ($schoolCourses->have_posts()) {
                    $schoolCourses->the_post(); ?>

                    <a href="<?php the_permalink(); ?>" class="course-button-mobile course-button-color">
                        <?php the_title(); ?>
                    </a>
                <?php }
                wp_reset_postdata();
                ?>
            </div>

        </div>

        <div id="collegeCourse" class="w3-container w3-border course" style="display:none">
            <div class="button-justify">

                <?php
                $collegeCourses = new WP_Query(array(
                    'post_type' => 'college_courses'
                ));

                while ($collegeCourses->have_posts()) {
                    $collegeCourses->the_post(); ?>

                    <a class="course-button-mobile course-button-color" href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                <?php }
                wp_reset_postdata();
                ?>
            </div>
        </div>

        <div id="studyAbroad" class="w3-container w3-border course" style="display:none">
            <div class="button-justify">

                <?php
                $studyAbroadCourses = new WP_Query(array(
                    'post_type' => 'study_abroad'
                ));

                while ($studyAbroadCourses->have_posts()) {
                    $studyAbroadCourses->the_post(); ?>

                    <a class="course-button-mobile course-button-color" href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                <?php }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, courseName) {
            var i, x, tablinks;
            x = document.getElementsByClassName("course");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < x.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
            }
            document.getElementById(courseName).style.display = "block";
            evt.currentTarget.className += " w3-red";
        }
    </script>

	<?php
    return ob_get_clean();
}

// Remove the edit menu from admin bar
function remove_edit_menu( $wp_admin_bar ) {
  $wp_admin_bar->remove_node( 'edit' );
}

// Display the related testimonils in single course page
function testimonial_selection() {
    ob_start();

    $relatedTestimonials = get_field('testimonials');
    foreach ($relatedTestimonials as $testimony) {
    ?>
        <div class="overlay">
            <div class="outer-container">
                <div class="inner-container1">
                    <img src="<?php echo get_field('student_image', $testimony->ID)["url"]; ?>" alt="<?php echo get_field(' student_name', $testimony->ID); ?>" width=120px height=120px>
                </div>
                <div class="inner-container2">
                    <h3><?php echo get_field('student_name', $testimony->ID); ?></h3>
                    <h5><?php echo get_field('designation', $testimony->ID); ?></h5>
                    <p><?php echo get_field('testimony', $testimony->ID); ?></p>
                </div>
            </div>
        </div>
	<?php

    } wp_reset_postdata();
    return ob_get_clean();
}

// Display quick update image carousel 
/* function quick_updates_shortcode() {
    ob_start(); ?>
    <?php
    //Get the images ids from the post_metadata
    $images = acf_photo_gallery('quick_updates', $post->ID);
    //Check if return array has anything in it
    if (count($images)) :
        //Cool, we got some data so now let's loop over it
        foreach ($images as $image) :
            $id = $image['id']; // The attachment id of the media
            $title = $image['title']; //The title
            $caption = $image['caption']; //The caption
            $full_image_url = $image['full_image_url']; //Full size image url
            $full_image_url = acf_photo_gallery_resize_image($full_image_url, 262, 160); //Resized size to 262px width by 160px height image url
            $thumbnail_image_url = $image['thumbnail_image_url']; //Get the thumbnail size image url 150px by 150px
            $url = $image['url']; //Goto any link when clicked
            $target = $image['target']; //Open normal or new tab
            $alt = get_field('photo_gallery_alt', $id); //Get the alt which is a extra field (See below how to add extra fields)
            $class = get_field('photo_gallery_class', $id); //Get the class which is a extra field (See below how to add extra fields)
    ?>
            <div class="col-xs-6 col-md-3">
                <div class="thumbnail">
                    <?php if (!empty($url)) { ?><a href="<?php echo $url; ?>" <?php echo ($target == 'true') ? 'target="_blank"' : ''; ?>><?php } ?>
                        <img src="<?php echo $full_image_url; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>">
                        <?php if (!empty($url)) { ?></a><?php } ?>
                </div>
            </div>
    <?php endforeach;
    endif; ?>

    <?php
    return ob_get_clean();

} */

// Add ACF Photo Gallery tag to elementor
add_action( 'elementor/dynamic_tags/register_tags', function( $dynamic_tags ) {
	class ACF_Photo_Galery extends Elementor\Core\DynamicTags\Data_Tag {

		public function get_name() {
			return 'acf_photo_galery';
		}

		public function get_categories() {
			return [ 'gallery' ];
		}

		public function get_group() {
			return [ 'acf' ];
		}

		public function get_title() {
			return 'ACF Photo Gallery';
		}
      
		protected function get_value( array $options = [] ) {
			
			$post_ids = new WP_Query (array(
				'numberposts' => 1,
				'post_type' => 'quick_updates',
				'fields'      => 'ids'
			));
			
			
			if ( isset( $post_ids->posts[0] ) ) {
				$post_id = $post_ids->posts[0];
			}
         
			$photos = acf_photo_gallery("quick_updates", $post_id);
         
			return $photos;
		} 
	}
	$dynamic_tags->register_tag( 'ACF_Photo_Galery' );
} );

// Change Administrator user role name
function change_role_name() {
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();

    //You can list all currently available roles like this...
    //$roles = $wp_roles->get_names();
    //print_r($roles);

    //You can replace "administrator" with any other role "editor", "author", "contributor" or "subscriber"...
    $wp_roles->roles['administrator']['name'] = 'Developer';
    $wp_roles->role_names['administrator'] = 'Developer';           
}

//Redirect to homepage after logout

function auto_redirect_after_logout(){

  wp_redirect( home_url() );
  exit();

}

//Function to logout  the current user and redirect
function logout_and_redirect() {
	$redirect_url = get_home_url();
	$logout_url = wp_logout_url( $redirect_url );
	echo $logout_url;
}

/** Set up the Ajax Logout */
function ajax_logout_init() {
	wp_enqueue_script ('ajax-logout-script', get_theme_file_uri('/ajax-logout.js'), array('jquery'), '1.0', true);
	wp_localize_script('ajax-logout-script', 'ajax_object',
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'home_url' => get_home_url(),
			'logout_nonce' => wp_create_nonce('ajax-logout-nonce'),
		)
	);
	add_action('wp_ajax_fetch_user_data', 'ajax_fetch_user_data_func');
	add_action('wp_ajax_nopriv_fetch_user_data', 'ajax_fetch_user_data_nopriv_func');
	add_action('wp_ajax_custom_ajax_logout', 'custom_ajax_logout_func');
}

add_action( 'init', 'ajax_logout_init');

function custom_ajax_logout_func() {
    check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
    wp_logout();
    ob_clean(); // probably overkill for this, but good habit
    wp_send_json_success();
}

/** Fetch user data for logout dropdown */
function ajax_fetch_user_data_nopriv_func () {
	return;
}

function ajax_fetch_user_data_func () {
    check_ajax_referer( 'ajax-logout-nonce', 'ajaxsecurity' );
	$current_user = wp_get_current_user();
	$current_user_data = $current_user->data;
	$username = $current_user_data->user_nicename;
	$email = $current_user_data->user_email;

	$result['username'] = $username;
	$result['email'] = $email;
	$result['img_url'] = get_avatar_url($current_user_data->ID);

	$result = json_encode($result);

	echo $result;

	die();
}

/** Function to change VAT to GST */
add_filter( 'gettext', function( $translation, $text, $domain ) {
	if ( $domain == 'woocommerce' ) {
		if ( $text == '(ex. VAT)' ) { $translation = '(ex. GST)'; }
	}
	return $translation;
}, 10, 3 );

// register shortcode
function register_torno_shortcodes() {
	add_shortcode('run_display_related_product_collection', 'display_related_product_collection');
	add_shortcode('run_display_course_category_icon_boxes', 'display_course_category_icon_boxes');
	add_shortcode('run_list_the_courses_as_buttons', 'list_the_courses_as_buttons'); 
	add_shortcode('run_open_session_speaker_photo_gallery', 'photo_gallery_shortcode'); 
	add_shortcode('run_about_us_page', 'about_us_shortcode');
	add_shortcode('run_updates_page', 'daily_updates_shortcode'); 
	add_shortcode('run_reach_us_page', 'reach_us_shortcode'); 
	add_shortcode('run_open_session_single_page_speaker_list', 'open_session_single_page_speaker_list'); 
	add_shortcode('show_footer_list', 'footer_collapsable_list');
	add_shortcode('run_mobile_tab_for_course_buttons', 'mobile_tab_for_course_buttons_shortcode'); 
	add_shortcode('run_testimonial_selection_in_pages', 'testimonial_selection');
	add_shortcode('logout', 'logout_and_redirect');
	// add_shortcode('run_quick_updates', 'quick_updates_shortcode');  

}

add_action( 'init', 'register_torno_shortcodes');
add_action( 'admin_menu', 'add_admin_menu_separator' );
add_action( 'admin_menu', 'change_admin_menu_label' );
add_action( 'init', 'change_post_object_label' );
add_action( 'admin_enqueue_scripts', 'ds_admin_theme_style' );
add_action( 'login_enqueue_scripts', 'ds_admin_theme_style' );
add_action( 'wp_admin_bar', 'remove_edit_menu', 1 );
add_action( 'init', 'change_role_name');
add_action('wp_logout','auto_redirect_after_logout');
	