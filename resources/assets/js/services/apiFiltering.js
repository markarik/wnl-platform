import moment from 'moment'

export const buildFiltersByPath = (filters = {}) => {
    const filterGroups = Object.keys(filters)
    const filtersByPath = {}

    filterGroups.forEach(filterGroup => {
        filters[filterGroup].items.forEach((item, index) => {
            filtersByPath[`${filterGroup}.items[${index}]`] = false
        })
    })

    return filtersByPath
}

export const parseFilters = (activeFilters, filters, userId) => {
	const parsedFilters        = []
	const groupedFilters = {}

	activeFilters.forEach((path, index) => {
		const [filterGroup, ...tail] = path.split('.')
		const filterValue            = _.get(filters, path, {}).value
		const filterType             = _.get(filters, filterGroup, {}).type

		groupedFilters[filterGroup] = groupedFilters[filterGroup] || []
		groupedFilters[filterGroup].push(filterValue)
	})

	Object.keys(groupedFilters).forEach((group) => {
		if (filters[group].type === FILTER_TYPES.TAGS) {
			parsedFilters.push({[group]: groupedFilters[group]})
		} else if (filters[group].type === FILTER_TYPES.LIST) {
			parsedFilters.push({
				[group]: {
					user_id: userId,
					date: moment().subtract(3, 'hours').format('YYYY-MM-DD'),
					list: groupedFilters[group]
				}
			})
		}
	})

	return parsedFilters;
}

export const FILTER_TYPES = {
	BOOLEAN: 'boolean',
	LIST: 'list',
	TAGS: 'tags',
}

