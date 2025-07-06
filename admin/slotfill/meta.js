/**
 * Adds post meta input controls to posts.
 */

import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

import {
	TextareaControl,
	__experimentalInputControl as InputControl,
	__experimentalNumberControl as NumberControl,
	__experimentalVStack as VStack
} from '@wordpress/components';

/**
 * Registers a plugin that uses the `PluginDocumentSettingsPanel` SlotFill in
 * the post editor to output a custom panel.
 *
 * @link https://developer.wordpress.org/block-editor/reference-guides/slotfills/plugin-document-setting-panel/
 */
registerPlugin( 'job-notices-slotfill', {
	render: () => {
		// Gets the current post type from the `core/editor` store via
		// the `useSelect()` hook.
		// @link https://developer.wordpress.org/block-editor/reference-guides/packages/packages-data/#useselect
		const postType = useSelect(
			( select ) => select( 'core/editor' ).getCurrentPostType(),
			[]
		);

		// Assign constants for getting/setting post meta.
		// @link https://developer.wordpress.org/block-editor/how-to-guides/metabox/#step-2-add-meta-block
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

		console.log(meta, setMeta);

		// Returns controls built with Core components for handling the
		// meta input fields.
		// @link https://developer.wordpress.org/block-editor/reference-guides/components/
		return (
			<PluginDocumentSettingPanel
				title={ __( 'Job Profile', 'wp-fundi-jobs-block' ) }
			>
				<VStack>					
					<InputControl
						label={ __( 'First Name', 'wp-fundi-jobs-block' ) }
						value={ meta?.name_first }
						onChange={ ( value ) => setMeta( {
							...meta,
							name_first: value || null
						} ) }
					/>
					<InputControl
						label={ __( 'Last Name', 'wp-fundi-jobs-block' ) }
						value={ meta?.name_last }
						onChange={ ( value ) => setMeta( {
							...meta,
							name_last: value || null
						} ) }
					/>
					<InputControl
						label={ __( 'Role / Title', 'wp-fundi-jobs-block' ) }
						value={ meta?.company_role }
						onChange={ ( value ) => setMeta( {
							...meta,
							company_role: value || null
						} ) }
					/>
                    <TextareaControl
						label={ __( 'Blurb', 'wp-fundi-jobs-block' ) }
						help="Enter a blurb for the Job. Is used on Carousels."
						value={ meta?.jobs_blurb }
						onChange={ ( value ) => setMeta( {
							...meta,
							jobs_blurb: value || null
						} ) }
					/>
					<InputControl
						type="number"
						label="Ranking"
						value={ meta?.jobs_ranking }
						onChange={ ( value ) => setMeta( {
							...meta,
							jobs_ranking: value || null
						} ) }
					/>
				</VStack>
			</PluginDocumentSettingPanel>
		);
	}
} );