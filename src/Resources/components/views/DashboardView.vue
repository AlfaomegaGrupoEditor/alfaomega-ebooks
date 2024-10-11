<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {ref} from 'vue';
    import {aoWidget, aoListActionItem} from '@/components';

    const {t} = useI18n();
    const widgets = ref([
        {
            slug: 'import_ebooks',
            title: t('import_ebooks'),
            icon: 'MdiImport',
            variant: 'green',
            value: "60/1400",
            description: t('import_ebooks_description'),
            action: 'import',
        },
        {
            slug: 'update_ebooks',
            title: t('update_ebooks'),
            icon: 'MdiUpdate',
            variant: 'navy',
            value: 60,
            description: t('update_ebooks_description'),
            action: 'update',
        },
        {
            slug: 'link_products',
            title: t('link_products'),
            icon: 'MdiLinkVariant',
            variant: 'orangered',
            value: '1160/1200',
            description: t('link_products_description'),
            action: 'link',
        },
        {
            slug: 'setup_prices',
            title: t('setup_prices'),
            icon: 'MdiLabelPercentOutline',
            variant: 'maroon',
            value: '40',
            description: t('setup_prices_description'),
            action: 'setup',
        }
    ]);
    const actions = ref([
        {
            slug: 'linked_products',
            title: 'linked_products',
            description: 'linked_products_description',
            count: 14,
            action: '/wp-admin/edit.php?s&post_status=all&post_type=product&action=-1&ebooks_filter=sync&product_cat&product_type&stock_status&fb_sync_enabled&filter_action=Filtrar&paged=1&action2=-1'
        },
        {
            slug: 'all_ebooks',
            title: 'all_ebooks',
            description: 'all_ebooks_description',
            count: 2,
            action: '/wp-admin/edit.php?post_type=alfaomega-ebook'
        },
        {
            slug: 'ebook_access',
            title: 'ebook_access',
            description: 'ebook_access_description',
            count: 0,
            action: '/wp-admin/edit.php?post_type=alfaomega-access'
        },
        {
            slug: 'sample_codes',
            title: 'sample_codes',
            description: 'sample_codes_description',
            count: 0,
            action: '/wp-admin/edit.php?post_type=alfaomega-sample'
        },
        {
            slug: 'config',
            title: 'config',
            description: 'config_description',
            action: '/wp-admin/admin.php?page=alfaomega_ebooks_settings'
        },
        {
            slug: 'about',
            title: 'about',
            description: 'about_description',
            action: '#/about'
        }
    ]);
</script>

<template>
    <div class="row">
        <ao-widget v-for="widget in widgets"
                   :key="widget.slug"
                   v-bind="widget"
        />
    </div>
    <div class="row">
        <div class="d-flex justify-content-center w-100">
            <BCard class="px-0 w-100"
                border-variant="dark"
            >
                <div class="card-title fw-bold fs-6 px-0 pt-0 pb-2 text-muted text-uppercase border-bottom">
                    {{ $t('ebooks_manager') }}
                </div>

                <BListGroup flush>
                    <ao-list-action-item v-for="action in actions"
                                         :key="action.slug"
                                         v-bind="action"
                    />
                </BListGroup>

            </BCard>
        </div>
    </div>
</template>
