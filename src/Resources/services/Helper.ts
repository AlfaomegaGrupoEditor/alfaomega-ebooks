import {BooksFilterType, OrderType} from '@/types';

/**
 * Check if a variable is empty
 * @param variable
 */
const empty = (variable: any): boolean => {
  return variable === null || variable === undefined || variable === '';
}

/**
 * Update the browser history
 * @param pFilter
 * @param pCategory
 * @returns BooksFilterType
 */
const updateHistory = (pFilter: BooksFilterType | null = null, pCategory: string | null = null):BooksFilterType => {
  const urlParams = new URLSearchParams(window.location.search);
  let activeFilters: BooksFilterType;

  if (pFilter === null) {
    activeFilters = {
      category: pCategory === null ? (urlParams.get('category') || null) : pCategory,
      accessType: urlParams.get('accessType') || null,
      accessStatus: urlParams.get('accessStatus') || null,
      searchKey: urlParams.get('searchKey') || null,
      order: {
        'field': urlParams.get('order_by') || 'title',
        'direction': urlParams.get('order_direction') || 'asc'
      } as OrderType,
      perPage: urlParams.get('perPage') || 8,
    } as BooksFilterType;
  } else {
    activeFilters = Object.keys(pFilter).reduce((acc, key) => {
      if (pFilter[key] !== null && key !== 'order') {
        acc[key] = pFilter[key];
      }
      return acc;
    }, {});
    activeFilters.order_by = pFilter.order.value.field;
    activeFilters.order_direction = pFilter.order.value.direction;
    activeFilters.category = pCategory === null ? (urlParams.get('category') || null) : pCategory;
  }

  const queryString = new URLSearchParams(activeFilters).toString();

  window.history.pushState(null, '', `?${queryString}`);
  return activeFilters;
}

export {
    empty,
    updateHistory
}
