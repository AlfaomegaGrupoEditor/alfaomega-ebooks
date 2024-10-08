<table class="form-table alfaomega-ebook-access-metabox">
    <input type="hidden" name="alfaomega_ebook_nonce" value="<?php echo wp_create_nonce( "alfaomega_ebook_access_nonce" ); ?>">
    <tr>
        <th>
            <label for="alfaomega_access_Cover">
                <?php esc_html_e( 'Cover', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input 
                type="text" 
                name="alfaomega_ebook_cover"
                id="alfaomega_ebook_cover"
                class="regular-text cover"
                value="<?php echo ( isset( $cover ) ) ? esc_attr( $cover ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_isbn">
                <?php esc_html_e( 'ISBN Digital', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_ebook_isbn"
                id="alfaomega_ebook_isbn"
                class="regular-text isbn"
                value="<?php echo ( isset( $isbn ) ) ? esc_attr( $isbn ) : 'ISBN'; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_type">
                <?php esc_html_e( 'Type', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_type"
                id="alfaomega_access_type"
                class="regular-text type"
                value="<?php echo ( isset( $type ) ) ? esc_attr( $type ) : 'purchase'; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_order_id">
                <?php esc_html_e( 'Order Id', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_order_id"
                id="alfaomega_access_order_id"
                class="regular-text order_id"
                value="<?php echo ( isset( $order_id ) ) ? esc_attr( $order_id ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_sample_id">
                <?php esc_html_e( 'Sample Id', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_sample_id"
                id="alfaomega_access_sample_id"
                class="regular-text sample_id"
                value="<?php echo ( isset( $sample_id ) ) ? esc_attr( $sample_id ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_status">
                <?php esc_html_e( 'Status', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_status"
                id="alfaomega_access_status"
                class="regular-text status"
                value="<?php echo ( isset( $status ) ) ? esc_attr( $status ) : 'created'; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_read">
                <?php esc_html_e( 'Read', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_read"
                id="alfaomega_access_read"
                class="regular-text read"
                value="<?php echo ( isset( $tag_id ) ) ? esc_attr( $tag_id ) : '1'; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_download">
                <?php esc_html_e( 'Download', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_download"
                id="alfaomega_access_download"
                class="regular-text download"
                value="<?php echo ( isset( $download ) ) ? esc_attr( $download ) : '1'; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_access_due_date">
                <?php esc_html_e( 'Due date', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_access_due_date"
                id="alfaomega_access_due_date"
                class="regular-text until"
                value="<?php echo ( isset( $due_date ) ) ? esc_attr( $due_date ) : ''; ?>"
                required
            >
        </td>
        <td>
            <input
                type="text"
                name="alfaomega_access_download_at"
                id="alfaomega_access_download_at"
                class="regular-text download_at"
                value="<?php echo ( isset( $download_at ) ) ? esc_attr( $download_at ) : ''; ?>"
                required
            >
        </td>
        <td>
            <input
                type="text"
                name="alfaomega_access_read_at"
                id="alfaomega_access_read_at"
                class="regular-text read_at"
                value="<?php echo ( isset( $read_at ) ) ? esc_attr( $read_at ) : ''; ?>"
                required
            >
        </td>
    </tr>
</table>
