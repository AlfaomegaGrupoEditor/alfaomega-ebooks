<table class="form-table alfaomega-ebook-metabox"> 
    <input type="hidden" name="alfaomega_ebook_nonce" value="<?php echo wp_create_nonce( "alfaomega_ebook_nonce" ); ?>">
    <tr>
        <th>
            <label for="alfaomega_ebook_isbn">
                <?php esc_html_e( 'ISBN Digital', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input 
                type="text" 
                name="alfaomega_ebook_isbn"
                id="alfaomega_ebook_isbn"
                class="regular-text isbn"
                value="<?php echo ( isset( $isbn ) ) ? esc_attr( $isbn ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_ebook_id">
                <?php esc_html_e( 'PDF Id', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_ebook_id"
                id="alfaomega_ebook_id"
                class="regular-text id"
                value="<?php echo ( isset( $id ) ) ? esc_attr( $id ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_ebook_url">
                <?php esc_html_e( 'HTML Url', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_ebook_url"
                id="alfaomega_ebook_url"
                class="regular-text url"
                value="<?php echo ( isset( $url ) ) ? esc_attr( $url ) : ''; ?>"
                required
            >
        </td>
    </tr>
    <tr>
        <th>
            <label for="alfaomega_ebook_tag_id">
                <?php esc_html_e( 'Tag Id', 'alfaomega-ebook' ); ?>
            </label>
        </th>
        <td>
            <input
                type="text"
                name="alfaomega_ebook_tag_id"
                id="alfaomega_ebook_tag_id"
                class="regular-text tag_id"
                value="<?php echo ( isset( $tag_id ) ) ? esc_attr( $tag_id ) : ''; ?>"
                required
            >
        </td>
    </tr>
</table>
