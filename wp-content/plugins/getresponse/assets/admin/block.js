
( function( blocks, editor, element ) {

	var createElement  = element.createElement
	var BlockControls = editor.BlockControls
	var SelectControl = wp.components.SelectControl
	var Toolbar = wp.components.Toolbar
	var optins = fca_eoi_gutenblock_script_data.optins
	var twostep_optins = fca_eoi_gutenblock_script_data.twostep_optins

	blocks.registerBlockType( 'optin-cat/gutenblock', {
		title: 'Optin Cat Form',
		icon: 'email',
		category: 'widgets',
		keywords: ['email', 'optin', 'form' ],
		edit: function( props ) {
			return [
				createElement(
					BlockControls,
					{ 
						key: 'controls'
					},		
					createElement(
						SelectControl,
						{	
							className: 'fca-eoi-gutenblock-select',
							value: props.attributes.post_id,
							options: optins,
							onChange: function( newValue ){ props.setAttributes({ post_id: newValue }) }
						}
					),
					props.attributes.post_id == 0 ? '' : 
					createElement(
						'a',
						{	
							href: fca_eoi_gutenblock_script_data.editurl + '?post=' + props.attributes.post_id + '&action=edit',
							target: '_blank',
							className: 'fca-eoi-gutenblock-link'
						},
						'Edit'
					),
					createElement(
						'a',
						{	
							href: fca_eoi_gutenblock_script_data.newurl + '?post_type=easy-opt-ins',
							target: '_blank',
							className: 'fca-eoi-gutenblock-link'
						},
						'New'
					)
				),
				createElement( wp.components.ServerSideRender, {
					block: 'optin-cat/gutenblock',
					attributes:  props.attributes,
				})
			]
		},

		save: function( props ) {
			return null
		},
	} )



	blocks.registerBlockType( 'optin-cat/gutenblock-twostep', {
		title: 'Optin Cat Two-Step Optin',
		icon: 'email',
		category: 'widgets',
		keywords: ['email', 'optin', 'form' ],
		edit: function( props ) {
			return [
				createElement(
					BlockControls,
					{ 
						key: 'controls'
					},		
					createElement(
						SelectControl,
						{	
							className: 'fca-eoi-gutenblock-select',
							value: props.attributes.post_id,
							options: twostep_optins,
							onChange: function( newValue ){ props.setAttributes({ post_id: newValue }) }
						}
					),
					props.attributes.post_id == 0 ? '' : 
					createElement(
						'a',
						{	
							href: fca_eoi_gutenblock_script_data.editurl + '?post=' + props.attributes.post_id + '&action=edit',
							target: '_blank',
							className: 'fca-eoi-gutenblock-link'
						},
						'Edit'
					),
					createElement(
						'a',
						{	
							href: fca_eoi_gutenblock_script_data.newurl + '?post_type=easy-opt-ins',
							target: '_blank',
							className: 'fca-eoi-gutenblock-link'
						},
						'New'
					)
				),
				createElement( wp.components.ServerSideRender, {
					block: 'optin-cat/gutenblock-twostep',
					attributes:  props.attributes,
				})
			]
		},

		save: function( props ) {
			return null
		},
	} )




}(
	window.wp.blocks,
	window.wp.editor,
	window.wp.element
))