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
    SelectControl,
    ToggleControl,
    RangeControl
} from '@wordpress/components';

import { useEffect } from '@wordpress/element';
import Devices from './devices';

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
export default function Edit({ attributes, setAttributes, clientId }) {
    const {
        numberOfItems,
        displayTitle,
        autoplayDelay,
        autoplayDisableOninteraction,
        itemDevice,
        desktopSlidesPerView,
        tabSlidesPerView,
        phoneSlidesPerView,
        desktopSpaceBetween,
        tabSpaceBetween,
        phoneSpaceBetween,
        lazyLoad,
        showDots
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
                <PanelBody
                    title={__('Number of Items', 'job-notices')}
                    initialOpen={false}
                >
                    <PanelRow>
                        <fieldset>
                            <TextControl
                                label={__('Enter the number of items to display', 'job-notices')}
                                value={numberOfItems}
                                onChange={(value) => setAttributes({ numberOfItems: parseInt(value) })}
                                type="number"
                            />
                        </fieldset>
                    </PanelRow>
                    <PanelRow>
                        <fieldset>
                            <SelectControl
                                label={__('Display title?', 'job-notices')}
                                options={boolean_options}
                                value={displayTitle}
                                onChange={(value) => {
                                    setAttributes({ displayTitle: value });
                                }}
                            />
                        </fieldset>
                    </PanelRow>
                </PanelBody>
                <PanelBody
                    title={__('Slides Settings', 'job-notices')}
                    initialOpen={false}
                >
                    <Devices
                        device={itemDevice}
                        title={__(
                            'Slides Per View',
                            'job-notices'
                        )}
                        renderFunction={(device) =>
                            setAttributes({
                                itemDevice: device,
                            })
                        }
                    />
                    {itemDevice === 'desktop' && (
                        <RangeControl
                            value={desktopSlidesPerView}
                            onChange={(desktopSlidesPerView) =>
                                setAttributes({ desktopSlidesPerView })
                            }
                            min={1}
                            max={10}
                        />
                    )}
                    {itemDevice === 'tablet' && (
                        <RangeControl
                            value={tabSlidesPerView}
                            onChange={(tabSlidesPerView) =>
                                setAttributes({ tabSlidesPerView })
                            }
                            min={1}
                            max={10}
                        />
                    )}
                    {itemDevice === 'smartphone' && (
                        <RangeControl
                            value={phoneSlidesPerView}
                            onChange={(phoneSlidesPerView) =>
                                setAttributes({ phoneSlidesPerView })
                            }
                            min={1}
                            max={10}
                        />
                    )}

                    <Devices
                        device={itemDevice}
                        title={__(
                            'Space Between Slides',
                            'job-notices'
                        )}
                        renderFunction={(device) =>
                            setAttributes({
                                itemDevice: device,
                            })
                        }
                    />
                    {itemDevice === 'desktop' && (
                        <RangeControl
                            value={desktopSpaceBetween}
                            onChange={(desktopSpaceBetween) =>
                                setAttributes({ desktopSpaceBetween })
                            }
                            min={0}
                            max={100}
                        />
                    )}
                    {itemDevice === 'tablet' && (
                        <RangeControl
                            value={tabSpaceBetween}
                            onChange={(tabSpaceBetween) =>
                                setAttributes({ tabSpaceBetween })
                            }
                            min={0}
                            max={100}
                        />
                    )}
                    {itemDevice === 'smartphone' && (
                        <RangeControl
                            value={phoneSpaceBetween}
                            onChange={(phoneSpaceBetween) =>
                                setAttributes({ phoneSpaceBetween })
                            }
                            min={0}
                            max={100}
                        />
                    )}
                    <PanelRow>
                        <fieldset>
                            <TextControl
                                label={__('Auto play Delay', 'job-notices')}
                                value={autoplayDelay}
                                onChange={(value) => setAttributes({ autoplayDelay: parseInt(value) })}
                                type="number"
                            />
                        </fieldset>
                    </PanelRow>
                    <PanelRow>
                        <fieldset>
                            <SelectControl
                                label={__('Autoplay on interaction', 'job-notices')}
                                options={boolean_options}
                                value={autoplayDisableOninteraction}
                                onChange={(value) => {
                                    setAttributes({ autoplayDisableOninteraction: value });
                                }}
                            />
                        </fieldset>
                    </PanelRow>
                    <PanelRow>
                        <fieldset>
                            <SelectControl
                                label={__('Lazy Load', 'job-notices')}
                                options={boolean_options}
                                value={lazyLoad}
                                onChange={(value) => {
                                    setAttributes({ lazyLoad: value });
                                }}
                            />
                        </fieldset>
                    </PanelRow>
                    <PanelRow>
                        <fieldset>
                            <SelectControl
                                label={__('Show Pagination dots', 'job-notices')}
                                options={boolean_options}
                                value={showDots}
                                onChange={(value) => {
                                    setAttributes({ showDots: value });
                                }}
                            />
                        </fieldset>
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
            <div {...blockProps}>
                <ServerSideRender
                    block="job-notices/employers-slider"
                    attributes={attributes}
                />
            </div>

        </>
    );
}