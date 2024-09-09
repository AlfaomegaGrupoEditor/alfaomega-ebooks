/**
 * External dependencies.
 */
import { Component } from '@wordpress/element';
import { debounce, get } from 'lodash';
import { __ } from '@wordpress/i18n';

/**
 * Choices dependencies.
 */
import "choices.js/public/assets/styles/choices.min.css";
import Choices from 'choices.js';

/**
 * The internal dependencies.
 */
import './style.scss';

const NoOptions = () => (
	<em>
		{ __( 'No options.', 'carbon-fields-ui' ) }
	</em>
);

export class ChoicesField extends Component {
	/**
	 * Handles the change of the input.
	 *
	 * @param {Object} e
	 * @return {void}
	 */
	handleChange = ( e ) => {
		const { id, onChange } = this.props;

		onChange( id, e.target.value );
	}

	componentDidMount() {
		const element = document.getElementById( this.props.id );
		if (element.length) {
			console.log('element', element);
			const choices = new Choices( element, {
				searchEnabled: true,
				placeholderValue: 'Search for a user',
				shouldSort: false,
				searchResultLimit: 5,
			})
		} else {
			console.error(`Element select#${this.props.id} not found`);
		}
	}

	componentMount() {
		console.log(id);

		onChange( id, value );
	}

	/**
	 * Renders the component.
	 *
	 * @return {Object}
	 */
	render() {
		const {
			id,
			name,
			field,
			onChange
		} = this.props;

		const value = this.props.value || get( field.options, '[0].value', '' );

		return (
			field.options.length > 0
				? (
					<select
						id={ id }
						name={ name }
						value={ value }
						className="cf-select__input"
						onChange={ this.handleChange }
					>
						{ field.options.map( ( option ) => (
							<option key={ option.value } value={ option.value }>
								{ option.label }
							</option>
						) ) }
					</select>
				)
				: <NoOptions />
		);
	}
}

export default ChoicesField;
