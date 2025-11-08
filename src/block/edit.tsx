/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */

// Import the editor styles
import './editor.scss';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { buildControls } from './controls';

interface EditProps {
    attributes: {
        mode?: string;
        align?: string;
        maxwidth?: string;
        [key: string]: any;
    };
    setAttributes: (attributes: Record<string, any>) => void;
}

declare const window: Window & {
    ArveBlockJsBefore: {
        options: {
            mode?: string;
            align_maxwidth?: string | number;
            [key: string]: any;
        };
    };
};

export function Edit({ attributes, setAttributes }: EditProps) {
    const { mode, align, maxwidth } = attributes;
    const { options } = window.ArveBlockJsBefore;
    let pointerEvents = true;
    const style: React.CSSProperties = {};

    // Create a clean copy of attributes without block layout props
    const attrCopy = { ...attributes };
    delete attrCopy.align;
    delete attrCopy.maxwidth;

    // Handle alignment and max width styles
    if (maxwidth && (align === 'left' || align === 'right')) {
        style.width = '100%';
        style.maxWidth = maxwidth;
    } else if (align === 'left' || align === 'right') {
        style.width = '100%';
        style.maxWidth = options.align_maxwidth as string | number;
    }

    const blockProps = useBlockProps({ style });

    if (mode === 'normal' || (!mode && options.mode === 'normal')) {
        pointerEvents = false;
    }

    return (
        <>
            <div {...blockProps} key="block">
                <ServerSideRender
                    className={classnames({
                        'arve-ssr': true,
                        'arve-ssr--pointer-events-none': !pointerEvents,
                    })}
                    block="nextgenthemes/arve-block"
                    attributes={attrCopy}
                    skipBlockSupportAttributes={true}
                />
            </div>
            <InspectorControls key="insp">
                {buildControls({ attributes, setAttributes })}
            </InspectorControls>
        </>
    );
}
