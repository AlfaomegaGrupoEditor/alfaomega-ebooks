module.exports = {
    plugins: {
        'postcss-prefix-selector': {
            prefix: '.bootstrap-app',
            transform: (prefix, selector, prefixedSelector) => {
                if (selector.startsWith(':root')) {
                    return selector;
                }
                return prefixedSelector;
            }
        },
    }
}
