module.exports = {
    plugins: {
        'postcss-prefix-selector': {
            prefix: '.bootstrap-app',
            transform: (prefix, selector, prefixedSelector) => {
                if (selector.startsWith(':root')
                    || selector.startsWith('table.wp-list-table .column-ebook_isbn')
                    || selector.startsWith('.ebook_isbn')) {
                    return selector;
                }
                return prefixedSelector;
            }
        },
    }
}
