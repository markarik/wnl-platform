import moment from 'moment';

export const buildFiltersByPath = (filters = {}) => {
	const filtersByPath = {};
	const filterGroups = Object.keys(filters);

	filterGroups.forEach(filterGroup => {
		buildPathForItems(filters[filterGroup], filterGroup, filtersByPath);
	});

	return filtersByPath;
};

const buildPathForItems = (filters, prefix, result) => {
	if (!filters.items  || filters.items.length === 0) {
		return result;
	}

	filters.items.forEach((item, index) => {
		const newPrefix = `${prefix}.items[${index}]`;
		result[`${prefix}.items[${index}]`] = false;
		buildPathForItems(item, newPrefix, result);
	});
};

export const parseFilters = (activeFilters, filters, userId) => {
	const parsedFilters = [];
	const groupedFilters = {};

	activeFilters.forEach((path) => {
		const [filterGroup, ] = path.split('.');
		const filterValue = _.get(filters, path, {}).value;

		groupedFilters[filterGroup] = groupedFilters[filterGroup] || [];
		groupedFilters[filterGroup].push(filterValue);
	});

	Object.keys(groupedFilters).forEach((group) => {
		if (filters[group].type === FILTER_TYPES.TAGS) {
			parsedFilters.push({ [group]: groupedFilters[group] });
		} else if (filters[group].type === FILTER_TYPES.LIST) {
			parsedFilters.push({
				[group]: {
					user_id: userId,
					date: moment().subtract(3, 'hours').format('YYYY-MM-DD'),
					list: groupedFilters[group]
				}
			});
		} else if (filters[group].type === FILTER_TYPES.SEARCH) {
			parsedFilters.push({
				[group]: {
					phrase: filters.search.items[0].value,
				}
			});
		}
	});

	return parsedFilters;
};

export const FILTER_TYPES = {
	BOOLEAN: 'boolean',
	LIST: 'list',
	TAGS: 'tags',
	SEARCH: 'search',
};
