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

	/**
	 * Get the configuration for the choices field.
	 * @param id
	 * @param field
	 * @returns Partial<options>
	 */
	getUserConfig = (id, field) => {
		const {
			attributes,
			render_choice_limit,
			fetch_url
		} = field;

		return {
			// Whether a search area should be shown
			searchEnabled: attributes.searchEnabled ? attributes.searchEnabled : false,

			// The value of the search inputs placeholder.
			placeholderValue: attributes.placeholder ? attributes.placeholder : __('Type to search',
				'carbon-fields-ui'),

			// Whether choices and groups should be sorted. If false, choices/groups will appear in the order they were given.
			shouldSort: attributes.shouldSort ? attributes.shouldSort : false,

			//The maximum amount of search results to show ("-1" indicates no limit).
			searchResultLimit: attributes.searchResultLimit ? attributes.searchResultLimit : 10,

			// The minimum amount of characters a user must type before a search is performed.
			searchFloor: attributes.searchFloor ? attributes.searchFloor : 3,

			// The text that is shown whilst choices are being populated via AJAX.
			loadingText: attributes.loadingText ? attributes.loadingText : __('Loading...', 'carbon-fields-ui'),

			// The text that is shown when a user hovers over a selectable choice.
			itemSelectText: attributes.itemSelectText ? attributes.itemSelectText : '',

			// Whether HTML should be rendered in all Choices elements.
			allowHTML: attributes.allowHTML ? attributes.allowHTML : false,

			// Whether each item should have a remove button.
			removeItemButton: attributes.removeItemButton ? attributes.removeItemButton : true,

			// The value of the search inputs placeholder.
			searchPlaceholderValue: attributes.searchPlaceholderValue ? attributes.searchPlaceholderValue : null,

			// The amount of choices to be rendered within the dropdown list ("-1" indicates no limit)
			// only apply when the user is searching
			render_choice_limit: render_choice_limit && fetch_url && attributes.searchEnabled
				? field.render_choice_limit : -1,

			//Function to run once Choices initialises.
			callbackOnInit: () => { this.onInit(id, attributes); }
		}
	}

	/**
	 * Choices initialization callback.
	 */
	onInit = (id, attributes) => {
		// set the node id to the choices element
		const choicesElement = document.getElementById(id).parentNode.parentNode;
		choicesElement.id = 'cf-choices-' + id.replace('cf-', '');

		// add the searchable class to the choices element
		if (attributes.searchEnabled) {
			choicesElement.className += ' searchable';
		}
	}

	/**
	 * Load choices from the fetch url.
	 * @param choices
	 * @param fetch_url
	 */
	loadChoices = (choices, fetch_url) => {
		const loadOptions = (searchTerm) => {
			const url = fetch_url.replace('{query}', searchTerm);

			fetch(url)
				.then(response => response.json())
				.then(data => {
					const options = data.results.map(result => {
						return {
							value: result.id,
							label: result.title,
						};
					});

					// Clear existing choices and set the new ones
					choices.clearChoices();
					choices.setChoices(options, 'value', 'label', true);

				})
				.catch(error => console.error(__('Error fetching options:', 'carbon-fields-ui'), error));
		}

		choices.containerOuter.element.addEventListener('search', (event) => {
			const searchTerm = event.detail.value;
			loadOptions(searchTerm);
		});

		loadOptions('');
	}

	/**
	 * Component did mount.
	 */
	componentDidMount() {
		const {
			id,
			field,
			onChange
		} = this.props;

		const { fetch_url } = field;

		const element = document.getElementById( id );
		if (element.length) {
			const choices = new Choices( element, this.getUserConfig(id, field ) );

			if (fetch_url) {
				this.loadChoices(choices, fetch_url);
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
