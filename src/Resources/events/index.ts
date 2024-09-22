import mitt from "mitt";
import type { Emitter } from "mitt";
import { onUnmounted } from "vue";
import type {
  ApiCheckEvent,
  NotificationEvent
} from './types';

/**
 * Type definition for the EventBus.
 * It currently supports a single event type: foo.
 */
type EventBus = {
  notification: NotificationEvent;
  apiSuccess: ApiCheckEvent;
}

/**
 * Type definition for a generic event handler function.
 */
type Handler<T = unknown> = (event: T) => void;

/**
 * Type definition for a collection of event handlers.
 * Each key corresponds to an event type, and the value is a handler function for that event type.
 */
type EventHandlers<T extends Record<string, unknown>> = {
  [K in keyof T]: (event: T[K]) => void;
};

// Create an instance of the event bus
export const eventBus = mitt<EventBus>();

/**
 * A hook for using mitt events within a Vue component.
 * Automatically cleans up event handlers when the component is unmounted.
 *
 * @param mitt - The event emitter to use.
 * @param handlers - The event handlers to register.
 * @returns A cleanup function that removes all registered handlers.
 */
export function useMittEvents<T extends Record<string, unknown>>(
  mitt: Emitter<T>,
  handlers: Partial<EventHandlers<T>>
) {
  for (const key of Object.keys(handlers)) {
    if (handlers[key]) {
      mitt.on(key, handlers[key] as Handler);
    }
  }

  function cleanup() {
    for (const key of Object.keys(handlers)) {
      if (handlers[key]) {
        mitt.off(key, handlers[key]);
      }
    }
  }

  onUnmounted(cleanup);

  return cleanup;
}
