<?php
/**
 * Plugin Name: Listing Posts Type
 * Plugin URI: https://wordpress.org/plugins/listing-posts-type/
 * Description: Display the most recent Custom Posts Type in the sidebar.
 * Version: 0.3.1
 * Author: Alberto Ochoa
 * Author URI: https://gitlab.com/albertochoa
 * Text Domain: listing-posts-type
 * Domain Path: /languages
 *
 * Display the most recent Custom Posts Type in the sidebar.
 * Copyright (C) 2010-2018 Alberto Ochoa
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

/**
 * Poedit is a good tool to for translating.
 * @link http://poedit.net
 *
 * @since 0.3.1
 */
function listing_posts_type_textdomain() {
	load_plugin_textdomain( 'listing-posts-type', false, 'listing-posts-type/languages' );
}
add_action( 'init', 'listing_posts_type_textdomain' );

/**
 *  Display the Custom Posts Type in the sidebar.
 *
 * @since 0.1
 * @param string $args Mixed arguments for the list
 * @return string Output of the Posts Type
 */
function listing_posts_type( $args = '' ) {

	$defaults = array(
		'number'       => 5,
		'title'        => __( 'Posts Type', 'listing-posts-type' ),
		'title_before' => '<h3>',
		'title_after'  => '</h3>',
		'status'       => 'publish',
		'post_type'    => 'post'
	);

	$r = wp_parse_args( $args, $defaults );

	if ( !$number = (int) $r['number'] ) {
		$number = 10;
	} else if ( $number < 1 ) {
		$number = 1;
	} else if ( $number > 15 ) {
		$number = 15;
	}

	$r_query = new WP_Query( array(
		'post_type'           => $r['post_type'],
		'showposts'           => $number,
		'nopaging'            => 0,
		'post_status'         => $r['status'],
		'ignore_sticky_posts' => 1
	) );

	if ( $r_query->have_posts() ) :
		if ( $r['title'] ) :
			echo $r['title_before'] . $r['title'] . $r['title_after']; ?>
			<ul> <?php
			while ( $r_query->have_posts() ) : $r_query->the_post(); ?>
				<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li> <?php

			endwhile; ?>
			</ul> <?php

		else:
			while ( $r_query->have_posts() ) : $r_query->the_post(); ?>
				<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li> <?php
			endwhile;

		endif;

		wp_reset_postdata();
	endif;
}

/**
 * Display the most recent Custom Posts Type in the sidebar with Widgets.
 *
 * @since 0.1
 */
add_action( 'widgets_init', 'register_widget_post_type' );

function register_widget_post_type() {
	register_widget( 'WP_Widget_Recent_Posts_Type' );
}

class WP_Widget_Recent_Posts_Type extends WP_Widget {

	function WP_Widget_Recent_Posts_Type() {

		$widget_ops = array( 'classname' => 'widget_recent_post_type', 'description' => __( "Display the most recent Custom Posts Type in the sidebar.", 'listing-posts-type' ) );
		$this->WP_Widget( 'recent-posts-type', __( 'Recent Posts Type', 'listing-posts-type' ), $widget_ops );
		$this->alt_option_name = 'widget_recent_post_type';

		add_action( 'save_post',    array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {

		$cache = wp_cache_get( 'widget_recent_post_type', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recent Posts Type', 'listing-posts-type' ) : $instance['title'], $instance, $this->id_base );

		if ( !$number = (int) $instance['number'] ) {
			$number = 10;
		} else if ( $number < 1 ) {
			$number = 1;
		} else if ( $number > 15 ) {
			$number = 15;
		}

		$r = new WP_Query( array(
			'post_type'           => $instance['recent_post_type'],
			'showposts'           => $number,
			'nopaging'            => 0,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1
		) );


		if ( $r->have_posts() ) : ?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php  while ( $r->have_posts() ) : $r->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget;

		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_recent_post_type', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['recent_post_type'] = $new_instance['recent_post_type'];
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_recent_post_type'] ) ) {
			delete_option( 'widget_recent_post_type' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_recent_post_type', 'widget' );
	}

	function form( $instance ) {

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$recent_post_type = isset( $instance['recent_post_type'] ) ? $instance['recent_post_type'] : '';

		if ( !isset( $instance['number'] ) || !$number = (int) $instance['number'] ) {
			$number = 5;
		} ?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'listing-posts-type' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('recent_post_type'); ?>"><?php _e( 'Posts Type', 'listing-posts-type' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'recent_post_type' ); ?>" name="<?php echo $this->get_field_name( 'recent_post_type' ); ?>"> <?php

		$post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );

		foreach ( $post_types  as $post_type ) {
			echo '<option value="' . $post_type->name . '"'
				. ( $post_type->name == $instance['recent_post_type'] ? ' selected="selected"' : '' )
				. '>' . $post_type->labels->name . "</option>\n";
		} ?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'listing-posts-type' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p> <?php
	}
}

