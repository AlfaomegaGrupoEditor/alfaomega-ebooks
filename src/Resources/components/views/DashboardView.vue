<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {ref} from 'vue';
    import {aoWidget} from '@/components';

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
            value: '40/1200',
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
</script>

<template>
    <div class="row">
        <ao-widget v-for="widget in widgets"
                   :key="widget.slug"
                   v-bind="widget"
        />
    </div>
</template>
