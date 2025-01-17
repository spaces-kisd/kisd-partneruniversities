<?php

return
	array(
		'key'                   => 'group_5c39cff33b9ec',
		'title'                 => 'Solutions',
		'fields'                => array(
			array(
				'key'               => 'field_5c6ed46e408d5',
				'label'             => 'full_name',
				'name'              => 'full_name',
				'type'              => 'text',
				'instructions'      => 'A few words that describe the Solution.',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5c39ceffa4c7f',
				'label'             => 'Priority',
				'name'              => 'priority',
				'type'              => 'range',
				'instructions'      => 'The higher the priority, the higher the chance this post will show on the front page.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '100',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => 5,
				'min'               => '',
				'max'               => 10,
				'step'              => '',
				'prepend'           => '',
				'append'            => '',
			),
			array(
				'key'               => 'field_5c5d96eefee18',
				'label'             => 'deadline',
				'name'              => 'deadline',
				'type'              => 'number',
				'instructions'      => 'Add a Year.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '50',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'min'               => 1800,
				'max'               => 2100,
				'step'              => '',
			),
			array(
				'key'               => 'field_5c5d972cfee19',
				'label'             => 'Name of the location',
				'name'              => 'location_name',
				'type'              => 'text',
				'instructions'      => 'The location name you want to show in the frontend.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
			array(
				'key'               => 'field_5c5d975dfee1a',
				'label'             => 'Number of Employees',
				'name'              => 'number_of_employees',
				'type'              => 'number',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'min'               => 0,
				'max'               => '',
				'step'              => '',
			),
			array(
				'key'               => 'field_5c39cee4a4c7e',
				'label'             => 'Location',
				'name'              => 'location',
				'type'              => 'google_map',
				'instructions'      => '',
				'required'          => 1,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'center_lat'        => '',
				'center_lng'        => '',
				'zoom'              => '',
				'height'            => '',
			),
			array(
				'key'               => 'field_5c4c99fdcf11c',
				'label'             => 'Similar Solutions',
				'name'              => 'similar_solutions',
				'type'              => 'relationship',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'post_type'         => array(
					0 => 'solution',
				),
				'taxonomy'          => '',
				'filters'           => array(
					0 => 'search',
					1 => 'post_type',
					2 => 'taxonomy',
				),
				'elements'          => '',
				'min'               => '',
				'max'               => '',
				'return_format'     => 'object',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'solution',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'seamless',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	)
;
