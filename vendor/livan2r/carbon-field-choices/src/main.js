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
		const {
			id,
			field,
			onChange
		} = this.props;

		const element = document.getElementById( id );
		if (element.length) {
			const choices = new Choices( element, {
				// Whether a search area should be shown
				searchEnabled: field.attributes.searchEnabled ? field.attributes.searchEnabled : false,

				// The value of the search inputs placeholder.
				placeholderValue: field.attributes.placeholder ? field.attributes.placeholder : __('Type to search', 'carbon-fields-ui'),

				// Whether choices and groups should be sorted. If false, choices/groups will appear in the order they were given.
				shouldSort: field.attributes.shouldSort ? field.attributes.shouldSort : false,

				//The maximum amount of search results to show ("-1" indicates no limit).
				searchResultLimit: field.attributes.searchResultLimit ? field.attributes.searchResultLimit : 10,

				// The minimum amount of characters a user must type before a search is performed.
				searchFloor: field.attributes.searchFloor ? field.attributes.searchFloor : 3,

				// The text that is shown whilst choices are being populated via AJAX.
				loadingText: field.attributes.loadingText ? field.attributes.loadingText : __('Loading...', 'carbon-fields-ui'),

				// The text that is shown when a user hovers over a selectable choice.
				itemSelectText: field.attributes.itemSelectText ? field.attributes.itemSelectText : '',

				// Whether HTML should be rendered in all Choices elements.
				allowHTML: field.attributes.allowHTML ? field.attributes.allowHTML : false,

				// Whether each item should have a remove button.
				removeItemButton: field.attributes.removeItemButton ? field.attributes.removeItemButton : true,

				// The value of the search inputs placeholder.
				searchPlaceholderValue: field.attributes.searchPlaceholderValue ? field.attributes.searchPlaceholderValue : null,

				// The amount of choices to be rendered within the dropdown list ("-1" indicates no limit)
				// only apply when the user is searching
				render_choice_limit: field.render_choice_limit && field.fetch_url && field.attributes.searchEnabled
					? field.render_choice_limit : -1,

				// Add a callback to run when the instance is ready.
				callbackOnInit: () => {
					// set the node id to the choices element
					const choicesElement = document.getElementById(this.props.id).parentNode.parentNode;
					choicesElement.id = 'cf-choices-' + this.props.id.replace('cf-', '');

					// add the searchable class to the choices element
					if (field.attributes.searchEnabled) {
						choicesElement.className += ' searchable';
					}
				}
			})

			if (field.fetch_url) {
				choices.setChoices(function(callback) {
					return fetch(field.fetch_url)
						.then(function(res) {
							return res.json();
						})
						.then(function(data) {
							return data.releases.map(function(release) {
								return {label: release.title, value: release.title};
							});
						});
				});
			}
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
						className="cf-select__input form-control"
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
