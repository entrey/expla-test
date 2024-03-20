<?php
/**
 * Provide a admin area view for the plugin
 *
 * @package Expla_Test/admin/partials
 * @link    https://github.com/entrey
 * @since   1.0.0
 */


$the_replace_form = function ( $column_name ) {
    ?>
    <form class="replace-form" data-column-replace="<?php echo esc_attr( $column_name ); ?>">
        <input
            type="text"
            name="new-keyword"
            id="<?php echo esc_attr( "post-new-$column_name" ); ?>"
            placeholder="<?php esc_attr_e( 'new keyword...', 'expla-test' ); ?>"
            required
        >
        <input
            type="submit"
            value="<?php esc_attr_e( 'Replace', 'expla-test' ); ?>"
            disabled
        >
    </form>
    <?php
};

?>
<div class="expla" data-nonce="<?php echo wp_create_nonce(); ?>">
    <form class="query-form">
        <input
            type="text"
            name="keyword"
            id="post-keyword"
            placeholder="<?php esc_attr_e( 'keyword...', 'expla-test' ); ?>"
            required
        >
        <input
            type="submit"
            value="<?php esc_attr_e( 'Search', 'expla-test' ); ?>"
            disabled
        >
    </form>

    <div class="query-result">
        <h2 class="title">
            <?php esc_html_e( 'Results for', 'expla-test' ); ?>
            <span class="keyword"></span>
        </h2>

        <table class="result__table">
            <thead>
                <tr class="table__row">
                    <th class="table__column title">
                        <h3>
                            <?php esc_html_e( 'Title', 'expla-test' ); ?>
                        </h3>
                        <?php $the_replace_form( 'title' ); ?>
                    </th>
                    <th class="table__column content">
                        <h3>
                            <?php esc_html_e( 'Content', 'expla-test' ); ?>
                        </h3>
                        <?php $the_replace_form( 'content' ); ?>
                    </th>
                    <th class="table__column meta-title">
                        <h3>
                            <?php esc_html_e( 'Meta-title', 'expla-test' ); ?>
                        </h3>
                        <?php $the_replace_form( 'meta-title' ); ?>
                    </th>
                    <th class="table__column meta-description">
                        <h3>
                            <?php esc_html_e( 'Meta-description', 'expla-test' ); ?>
                        </h3>
                        <?php $the_replace_form( 'meta-description' ); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr class="template" data-post-id="">
                    <td class="table__column title"></td>
                    <td class="table__column content"></td>
                    <td class="table__column meta-title"></td>
                    <td class="table__column meta-description"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
