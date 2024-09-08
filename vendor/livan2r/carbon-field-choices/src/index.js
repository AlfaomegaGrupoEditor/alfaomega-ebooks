/**
 * External dependencies.
 */
import { registerFieldType } from '@carbon-fields/core';

/**
 * Internal dependencies.
 */
import './style.scss';
import ChoicesField from './main';

registerFieldType( 'choices', ChoicesField );
