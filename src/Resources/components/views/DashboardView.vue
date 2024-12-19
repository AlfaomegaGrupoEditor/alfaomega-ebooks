<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {ref, computed, watch, onMounted} from 'vue';
    import {aoWidget, aoListActionItem} from '@/components';
    import {useEbookStore, useProcessStore} from '@/stores';
    import {ProcessType} from '@/types';

    const {t} = useI18n();
    const ebookStore = useEbookStore();
    const ebooksInfo = computed(() => ebookStore.getEbooksInfo);
    const productsInfo = computed(() => ebookStore.getProductsInfo);
    const accessInfo = computed(() => ebookStore.getAccessInfo);
    const codesInfo = computed(() => ebookStore.getCodesInfo);
    const processStore = useProcessStore();
    const queueStatus = computed(() => processStore.getQueueStatus);

    const widgets = ref([
        {
            slug: 'import_ebooks',
            title: t('import_ebooks'),
            icon: 'MdiImport',
            variant: 'green',
            value: `${ebooksInfo.value.imported}/${ebooksInfo.value.catalog}`,
            description: t('import_ebooks_description'),
            action: 'import',
        },
        {
            slug: 'update_ebooks',
            title: t('update_ebooks'),
            icon: 'MdiUpdate',
            variant: 'navy',
            value: ebooksInfo.value.imported,
            description: t('update_ebooks_description'),
            action: 'update',
        },
        {
            slug: 'link_products',
            title: t('link_products'),
            icon: 'MdiLinkVariant',
            variant: 'orangered',
            value: `${productsInfo.value.unlinked}/${productsInfo.value.catalog}`,
            description: t('link_products_description'),
            action: 'link',
        },
        {
            slug: 'setup_prices',
            title: t('setup_prices'),
            icon: 'MdiLabelPercentOutline',
            variant: 'maroon',
            value: ebooksInfo.value.imported,
            description: t('setup_prices_description'),
            action: 'setup',
        }
    ]);
    const actions = ref([
        {
            slug: 'linked_products',
            title: 'linked_products',
            description: 'linked_products_description',
            count: productsInfo.value.linked,
            action: '/wp-admin/edit.php?s&post_status=all&post_type=product&action=-1&ebooks_filter=sync&product_cat&product_type&stock_status&fb_sync_enabled&filter_action=Filtrar&paged=1&action2=-1'
        },
        {
            slug: 'all_ebooks',
            title: 'all_ebooks',
            description: 'all_ebooks_description',
            count: ebooksInfo.value.imported,
            action: '/wp-admin/edit.php?post_type=alfaomega-ebook'
        },
        {
            slug: 'ebook_access',
            title: 'ebook_access',
            description: 'ebook_access_description',
            count: accessInfo.value.total,
            action: '/wp-admin/edit.php?post_type=alfaomega-access'
        },
        {
            slug: 'sample_codes',
            title: 'sample_codes',
            description: 'sample_codes_description',
            count: codesInfo.value.total,
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
    const process: ProcessType[] = [
        'import-new-ebooks',
        'update-ebooks',
        'link-products',
        'setup-prices'
    ];

    watch(ebooksInfo, () => {
        widgets.value[0].value = `${ebooksInfo.value.imported}/${ebooksInfo.value.catalog}`;
        widgets.value[1].value = ebooksInfo.value.imported;
        widgets.value[3].value = ebooksInfo.value.imported;
        actions.value[1].count = ebooksInfo.value.imported;
    });
    watch(productsInfo, () => {
        widgets.value[2].value = `${productsInfo.value.unlinked}/${productsInfo.value.catalog}`;
        actions.value[0].count = productsInfo.value.linked;
    });
    watch(accessInfo, () => {
        actions.value[2].count = accessInfo.value.total;
    });
    watch(codesInfo, () => {
        actions.value[3].count = codesInfo.value.total;
    });

    onMounted(() => {
        process.forEach((process: ProcessType) => processStore.dispatchRetrieveQueueStatus(process));
    });
</script>

<template>
    <div class="row">
        <ao-widget v-for="widget in widgets"
                   :key="widget.slug"
                   v-bind="widget"
        />
    </div>
    <div class="row">
        <div class="d-flex justify-content-center mx-3">
            <BCard class="px-0 w-100 border-light">
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

            <BCard class="px-0 mx-3 border-light"
                   style="min-width: 400px"
            >
                <div class="card-title fw-bold fs-6 px-0 pt-0 pb-2 text-muted text-uppercase border-bottom">
                    {{ $t('ebooks_stats') }}
                </div>

                <div class="row px-2 mt-2">
                    <div class="col">
                        <div class="row">
                            <div class="fw-bold text-center">- {{ $t('ebooks')}} -</div>
                        </div>
                        <div class="row mt-1 px-2">
                            <div class="col offset-1">
                                <div class="pl-2 pb-1">{{ $t('products')}}: <BBadge class="fs-7">{{ productsInfo.catalog }}</BBadge></div>
                                <div class="pl-2 pb-1">{{ $t('ebooks')}}: <BBadge class="fs-7">{{ ebooksInfo.imported }}</BBadge></div>
                                <div class="pl-2 pb-1">{{ $t('linked')}}: <BBadge class="fs-7" variant="success">{{ productsInfo.linked }}</BBadge></div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="fw-bold text-center">- {{ $t('queue')}} -</div>
                        </div>
                        <div class="row mt-1 px-2">
                            <div class="col offset-1">
                                <div class="pl-2 pb-1">{{ $t('pending')}}: <BBadge class="fs-7" variant="info">{{ Number(queueStatus.pending) + Number(queueStatus.processing) }}</BBadge></div>
                                <div class="pl-2 pb-1">{{ $t('completed')}}: <BBadge class="fs-7" variant="success">{{ queueStatus.completed }}</BBadge></div>
                                <div class="pl-2 pb-1">{{ $t('failed')}}: <BBadge class="fs-7" variant="primary">{{ queueStatus.failed }}</BBadge></div>
                                <div class="pl-2 pb-1">{{ $t('excluded')}}: <BBadge class="fs-7" variant="warning">{{ queueStatus.excluded }}</BBadge></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-2 mt-4">
                    <div class="row">
                        <div class="fw-bold text-center">- {{ $t('samples')}} -</div>
                    </div>
                    <div class="row mt-1 px-2">
                        <div class="col offset-2">
                            <div class="pl-2 pb-1">{{ $t('import')}}: <BBadge class="fs-7">{{ codesInfo.import }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('sample')}}: <BBadge class="fs-7" variant="primary">{{ codesInfo.samples }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('total')}}: <BBadge class="fs-7">{{ codesInfo.total }}</BBadge></div>
                        </div>
                        <div class="col">
                            <div class="pl-2 pb-1">{{ $t('created')}}: <BBadge class="fs-7">{{ codesInfo.created }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('sent')}}: <BBadge class="fs-7">{{ codesInfo.sent }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('redeemed')}}: <BBadge class="fs-7" variant="success">{{ codesInfo.redeemed }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('expired')}}: <BBadge class="fs-7">{{ codesInfo.expired }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('cancelled')}}: <BBadge class="fs-7">{{ codesInfo.cancelled }}</BBadge></div>
                        </div>
                    </div>
                </div>

                <div class="px-2 mt-2">
                    <div class="row">
                        <div class="fw-bold text-center">- {{ $t('ebook_access')}} -</div>
                    </div>
                    <div class="row mt-1 px-2">
                        <div class="col offset-2">
                            <div class="pl-2 pb-1">{{ $t('sample') }}:
                                <BBadge>{{ accessInfo.sample }}</BBadge>
                            </div>
                            <div class="pl-2 pb-1">{{ $t('purchase') }}:
                                <BBadge variant="info">{{ accessInfo.purchase }}</BBadge>
                            </div>
                            <div class="pl-2 pb-1">{{ $t('total') }}:
                                <BBadge>{{ accessInfo.total }}</BBadge>
                            </div>
                        </div>
                        <div class="col">
                            <div class="pl-2 pb-1">{{ $t('created')}}: <BBadge>{{ accessInfo.created }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('active')}}: <BBadge variant="success">{{ accessInfo.active }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('expired')}}: <BBadge>{{ accessInfo.expired }}</BBadge></div>
                            <div class="pl-2 pb-1">{{ $t('cancelled')}}: <BBadge>{{ accessInfo.cancelled }}</BBadge></div>
                        </div>
                    </div>
                </div>
            </BCard>
        </div>
    </div>
</template>

<style scoped>
    .border-light{
        border-color: var(--bs-light-border-subtle) !important;
    }
</style>
