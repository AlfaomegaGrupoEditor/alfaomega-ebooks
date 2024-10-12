<script setup lang="ts">
    import {
        MdiImport,
        MdiUpdate,
        MdiLinkVariant,
        MdiLabelPercentOutline,
        MdiBoxOutline
    } from "@/components/icons"
    import {useI18n} from 'vue-i18n';
    import {computed} from 'vue';

    const {t} = useI18n();
    const props = defineProps({
        slug: { type: String, default: 'slug' },
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
        <BCard class="shadow mx-2 mb-5 px-0 pb-0">
            <div class="card-title fw-bold fs-6 px-0 py-0">
                {{ title }}
            </div>
            <div class="card-body d-flex justify-content-between pt-2 px-0">
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
            <div class="card-body d-flex justify-content-end px-0 py-0">
                <BButton
                    variant="info"
                    size="sm"
                    style="min-width: 100px;"
                    :to="{name: slug}"
                >
                    {{ $t(action) }}
                </BButton>
            </div>
        </BCard>
    </div>
</template>

<style scoped>
    .widget-icon {
        background-color: #e4e3e3;
    }
</style>
