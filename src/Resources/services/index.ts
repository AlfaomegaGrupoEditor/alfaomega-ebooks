import appController from './apps';
import libraryController from './libraries';
import ebookController from './ebooks';
import processController from './processes';

/**
 * The API controllers.
 */
export const API = {
    app: appController,
    library: libraryController,
    ebook: ebookController,
    process: processController
};
