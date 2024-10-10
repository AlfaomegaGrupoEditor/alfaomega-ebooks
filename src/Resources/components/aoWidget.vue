<script setup lang="ts">
    import MdiImport from '~icons/mdi/import?raw&width=3.2rem&height=3.2rem';
    import MdiUpdate from '~icons/mdi/update?raw&width=3.2rem&height=3.2rem';
    import MdiLinkVariant from '~icons/mdi/link-variant?raw&width=3.2rem&height=3.2rem';
    import MdiLabelPercentOutline from '~icons/mdi/label-percent-outline?raw&width=3.2rem&height=3.2rem';
    import MdiBoxOutline from '~icons/mdi/box-outline?raw&width=1.2rem&height=1.2rem';
    import {useI18n} from 'vue-i18n';
    import {computed} from 'vue';

    const {t} = useI18n();
    const props = defineProps({
        icon: { type: String, default: 'MdiImport'},
        value: { type: [String, Number], default: 0 },
        description: { type: String, default: 'description' },
        title: { type: String, default: 'Title' },
        variant: { type: String, default: 'grey'},
        action: { type: String, default: 'action'}
    });
    const iconClass = computed(() => {
        switch (props.icon) {
            case 'MdiImport':
                return MdiImport;
            case 'MdiUpdate':
                return MdiUpdate;
            case 'MdiLinkVariant':
                return MdiLinkVariant;
            case 'MdiLabelPercentOutline':
                return MdiLabelPercentOutline;
            default:
                return MdiBoxOutline;
        }
    });
</script>

<template>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card mx-2 shadow p-3 mb-5 bg-body">
            <div class="card-content">
                <div class="card-title fw-bold fs-6">
                    {{ title }}
                </div>
                <div class="card-body d-flex justify-content-between">
                    <div class="widget-icon rounded-circle px-2 py-2">
                        <span v-html="iconClass"
                              :style="{color: variant}"
                        />
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold fs-4 text-secondary">{{ value }}</h3>
                        <span class="text-secondary fs-7">{{ description }}</span>
                    </div>
                </div>
                <div class="card-body d-flex justify-content-end">
                    <BButton
                        class="fw-bold"
                        variant="outline-primary"
                        size="sm"
                        style="min-width: 100px"
                    >
                        {{ $t(action) }}
                    </BButton>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .widget-icon {
        background-color: #e4e3e3;
    }
</style>
