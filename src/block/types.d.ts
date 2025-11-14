interface Window {
    ArveBlockJsBefore: {
        settings: Record<string, Setting>;
        options: {
            mode: string;
            align_maxwidth: number | number;
            [key: string]: unknown;
        };
        settingPageUrl: string;
        gutenbergActive: boolean;
    };
}

interface Depends {
	key: string;
	value: string;
}

interface BuildControlsProps {
	attributes: Record<string, unknown>;
	setAttributes: (attributes: Record<string, unknown>) => void;
}

interface Setting {
    label: string;
    tab: string;
    category: string;
    type: string;
    description?: string;
    placeholder?: string;
    options?: Record<string, string>;
    ui?: string;
    ui_element: string;
    ui_element_type: 'text' | 'number' | 'checkbox';
    depends?: Depends[];
    default?: unknown;
    shortcode?: boolean;
    option?: boolean;
}

interface BuildControlsProps {
    attributes: Record<string, unknown>;
    setAttributes: (attributes: Record<string, unknown>) => void;
}

interface GutenbergSelectOption {
    label: string;
    value: string;
}
