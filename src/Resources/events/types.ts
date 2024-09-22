/**
 * Type definition for the 'notificationEvent'.
 * This type represents the structure of the data that is emitted with the 'notification' event.
 */
export type NotificationEvent = {
  message: string;
  type: 'error' | 'success' | 'info' | 'warning'
}
