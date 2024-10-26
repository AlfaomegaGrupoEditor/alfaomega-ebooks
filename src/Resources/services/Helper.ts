import {AccessType, BooksFilterType, OrderType, ProcessType, StatusType} from '@/types';
import {State} from '@/services/processes/types';

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
const updateHistory = (pFilter: BooksFilterType | null = null, pCategory: string | null | undefined = null): void =>
{
    const urlParams = new URLSearchParams(window.location.search);
    let activeFilters: { [key: string]: any } = {};

    if (pFilter === null) {
        pFilter = {
            category: pCategory === null ? (
                urlParams.get('category') || null
            ) : pCategory,
            accessType: urlParams.get('accessType') as AccessType || null,
            accessStatus: urlParams.get('accessStatus') as StatusType || null,
            searchKey: urlParams.get('searchKey') || null,
            order: {
                'field': urlParams.get('order_by') || 'title',
                'direction': urlParams.get('order_direction') || 'asc'
            } as OrderType,
            perPage: Number(urlParams.get('perPage')) || 8,
            currentPage: pCategory === null ? Number((urlParams.get('currentPage')) || 1) : 1
        };
    }

    activeFilters = Object.keys(pFilter).reduce((acc: { [key: string]: any }, key) => {
        if (pFilter[key as keyof BooksFilterType] !== null && key !== 'order') {
            acc[key] = pFilter[key as keyof BooksFilterType];
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
const setClass = (cssQuery: string, className: string, add = true): void =>
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
const getProcess = (process: ProcessType): string =>
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
