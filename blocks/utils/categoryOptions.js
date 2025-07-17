import { select } from '@wordpress/data';

export function getCategoryOptions(jobNotices) {
    if (String(jobNotices.taxonomyEnabled).toLowerCase() !== 'true') {
        return [];
    }

    const taxonomy = jobNotices.taxonomySlug || 'people-category';
    const cats = select('core').getEntityRecords('taxonomy', taxonomy);

    if (!cats) return [];

    const options = cats.map((cat) => ({
        label: cat.name,
        value: cat.id,
    }));

    options.unshift({ label: 'Select an option', value: 0 });

    return options;
}
