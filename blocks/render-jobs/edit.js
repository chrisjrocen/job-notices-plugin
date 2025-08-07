/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
    TextControl,
    PanelBody,
    PanelRow,
    ToggleControl
} from '@wordpress/components';

import { useEffect } from '@wordpress/element';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ 
    attributes, 
    setAttributes,
    clientId 
}) {
    const {
        heroDesc,
        heroTitle,
        postsPerPage,
        showPagination
    } = attributes;

    const blockProps = useBlockProps();

    const instanceId = clientId;
    useEffect(() => {
        setAttributes({ instanceId });
    }, [instanceId]);

    const boolean_options = [
        {
            label: 'Yes',
            value: true
        },
        {
            label: 'No',
            value: false
        }
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Settings', 'job-notices-plugin')}>
                    <PanelRow>
                        <TextControl
                            label={__('Hero Description', 'job-notices-plugin')}
                            value={attributes.heroDesc}
                            onChange={(value) => setAttributes({ heroDesc: value })}
                        />
                    </PanelRow>
                    <PanelRow>
                        <TextControl 
                            label={__('Number of Jobs', 'job-notices-plugin')}
                            value={attributes.postsPerPage}
                            onChange={(value) => setAttributes({ postsPerPage: value })}
                            min={1}
                            max={100}
                        />
                    </PanelRow>
                    <PanelRow>
                        <ToggleControl
                            label={__('Show Pagination', 'job-notices-plugin')}
                            checked={showPagination}
                            onChange={(value) => setAttributes({ showPagination: value })}
                            help={showPagination ? __('Pagination is enabled.', 'job-notices-plugin') : __('Pagination is disabled.', 'job-notices-plugin')}
                        />
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <ServerSideRender
                    block="job-notices/render-jobs"
                    attributes={attributes}
                />
            </div>

        </>
    );
}