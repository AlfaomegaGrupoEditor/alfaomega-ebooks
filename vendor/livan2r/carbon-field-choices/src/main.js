/**
 * External dependencies.
 */
import { Component } from '@wordpress/element';
import { debounce, get } from 'lodash';
import { __ } from '@wordpress/i18n';

/**
 * Choices dependencies.
 */
import "choices.js/public/assets/styles/choices.css";
import Choices from 'choices.js';

/**
 * The internal dependencies.
 */
import './style.scss';
import placeholder from 'lodash/fp/placeholder';

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
		console.log(this.props);
		if (element.length) {
			const choices = new Choices( element, {
				// Whether a search area should be shown
				searchEnabled: this.props.field.attributes.searchEnabled ? this.props.field.attributes.searchEnabled : false,

				// The value of the search inputs placeholder.
				placeholderValue: this.props.field.attributes.placeholder
					? this.props.field.attributes.placeholder :
					__('Type to search', 'carbon-fields-ui'),

				// Whether choices and groups should be sorted. If false, choices/groups will appear in the order they were given.
				shouldSort: this.props.field.attributes.shouldSort ? this.props.field.attributes.shouldSort : false,

				//The maximum amount of search results to show ("-1" indicates no limit).
				searchResultLimit: this.props.field.attributes.searchResultLimit ? this.props.field.attributes.searchResultLimit : 5,

				itemSelectText: '',

				allowHTML: true,

				removeItemButton: true,

				searchPlaceholderValue: "Search for a Smiths' record",
			})

				/*.setChoices(function(callback) {
					return fetch(
						'https://api.discogs.com/artists/83080/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW'
					)
						.then(function(res) {
							return res.json();
						})
						.then(function(data) {
							return data.releases.map(function(release) {
								return { label: release.title, value: release.title };
							});
						});
				});*/
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
						className="cf-choises__input form-control"
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
