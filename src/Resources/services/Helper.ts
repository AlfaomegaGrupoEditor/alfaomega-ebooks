import {BooksFilterType, OrderType} from '@/types';

/**
 * Check if a variable is empty
 * @param variable
 */
const empty = (variable: any): boolean =>
{
    return variable === null
           || variable === undefined
           || variable === '';
};

/**
 * Update the browser history
 * @param pFilter
 * @param pCategory
 * @returns BooksFilterType
 */
const updateHistory = (pFilter: BooksFilterType | null = null, pCategory: string | null = null): BooksFilterType =>
{
    const urlParams = new URLSearchParams(window.location.search);
    let activeFilters: BooksFilterType;

    if (pFilter === null) {
        pFilter = {
            category: pCategory === null ? (
                urlParams.get('category') || null
            ) : pCategory,
            accessType: urlParams.get('accessType') || null,
            accessStatus: urlParams.get('accessStatus') || null,
            searchKey: urlParams.get('searchKey') || null,
            order: {
                'field': urlParams.get('order_by') || 'title',
                'direction': urlParams.get('order_direction') || 'asc'
            } as OrderType,
            perPage: urlParams.get('perPage') || 8,
            currentPage: pCategory === null ? (urlParams.get('currentPage') || 1) : 1
        };
    }

    activeFilters = Object.keys(pFilter).reduce((acc, key) => {
        if (pFilter[key] !== null && key !== 'order') {
            acc[key] = pFilter[key];
        }
        return acc;
    }, {});

    activeFilters.order_by = pFilter.order.field;
    activeFilters.order_direction = pFilter.order.direction;
    activeFilters.category = pCategory === null ? (
        urlParams.get('category') || null
    ) : pCategory;

    const queryString = new URLSearchParams(activeFilters).toString();

    window.history.pushState(null, '', `?${queryString}`);
    return activeFilters;
};

/**
 * Check if a variable is null
 * @param variable
 */
const isNull = (variable: any): boolean =>
{
    return variable === null
           || variable === undefined
           || variable === ''
           || variable === 'null'
           || variable === 0;
};

/**
 * Get the value of a variable
 * @param variable
 * @param defaultValue
 */
const getValue = (variable: any, defaultValue:any = null): any =>
{
    return isNull(variable) ? defaultValue : variable;
};

/**
 * Set a class to an element
 * @param cssQuery
 * @param className
 * @param add
 */
const setClass = (cssQuery, className: string, add = true): void =>
{
    setTimeout(() => {
        const element = document.querySelector(cssQuery);
        if (element) {
            add ? element.classList.add(className)
                : element.classList.remove(className);
        }
    }, 100);
};

/**
 * Get the process name
 * @param process
 */
const getProcess = (process: ProcessType): ProcessType =>
{
    switch (process) {
        case 'import-new-ebooks':
            return 'importNewEbooks';
        case 'update-ebooks':
            return 'updateEbooks';
        case 'link-products':
            return 'linkProducts';
        case 'setup-prices':
            return 'setupPrices';
        default:
            return 'importNewEbooks';
    }
}

/**
 * Format a date
 * @param date
 */
const formatDate = (date: string) => {
    if (date === '0000-00-00 00:00:00') {
        return '-';
    }

    return new Date(date).toLocaleString(
        'es-ES',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        });
};

export {
    empty,
    updateHistory,
    isNull,
    getValue,
    setClass,
    getProcess,
    formatDate
};
