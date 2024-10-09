<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {ref} from 'vue';
    import {aoWidget} from '@/components';

    const {t} = useI18n();
    const widgets = ref([
        {
            slug: 'import-ebooks',
            title: t('import_ebooks'),
            icon: 'MdiImport',
            variant: 'green',
            value: "60/1400",
            description: t('import_ebooks_description'),
            action: 'import'
        },
        {
            slug: 'update-ebooks',
            title: t('update_ebooks'),
            icon: 'MdiUpdate',
            variant: 'navy',
            value: 60,
            description: t('update_ebooks_description'),
            action: 'update'
        },
        {
            slug: 'link-products',
            title: t('link_products'),
            icon: 'MdiLinkVariant',
            variant: 'orangered',
            value: '40/1200',
            description: t('link_products_description'),
            action: 'link'
        },
        {
            slug: 'setup-prices',
            title: t('setup_prices'),
            icon: 'MdiLabelPercentOutline',
            variant: 'maroon',
            value: '40',
            description: t('setup_prices_description'),
            action: 'setup'
        }
    ]);
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <main class="px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 fs-5 fw-bold">{{ $t('dashboard') }}</h1>
                </div>

                <div class="row">
                    <ao-widget v-for="widget in widgets"
                       :key="widget.slug"
                       v-bind="widget"
                    />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>

</style>
