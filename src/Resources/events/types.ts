/**
 * Type definition for the 'notificationEvent'.
 * This type represents the structure of the data that is emitted with the 'notification' event.
 */
type NotificationEvent = {
    message: string;
    type: 'error' | 'success' | 'info' | 'warning'
}

/**
 * Type definition for the 'apiCheckEvent'.
 */
type ApiCheckEvent = {
    user_id: string;
}

/**
 * Type definition for the 'catalogSelectedEvent'.
 */
type CatalogSelectedEvent = {
    catalog_id: 'all_ebooks' | 'purchase' | 'sample'    ;
}
export {
    NotificationEvent,
    ApiCheckEvent,
    CatalogSelectedEvent
};
